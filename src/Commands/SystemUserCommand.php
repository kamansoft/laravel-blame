<?php

namespace Kamansoft\LaravelBlame\Commands;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Kamansoft\LaravelBlame\Contracts\HandleEnvFile;
use Kamansoft\LaravelBlame\Traits\EnvFileHandler;

class SystemUserCommand extends \Illuminate\Console\Command implements HandleEnvFile
{
    use EnvFileHandler;

    public static string $system_user_id_const_name = 'BLAME_SYSTEM_USER_ID';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blame:set:systemuser
        {--id= : If set, this command will check for a user with that id, and if found, will set the system user id to that user id, otherwise a new user with that id will be created }
    ';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add the system user needed for klorchid';


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        if (empty($this->option('id'))) {
            $this->info("No id from command  param");
            if (empty(env(static::$system_user_id_const_name))) {
                $this->info('No id from env file');
                $this->checkAndSetSystemUser();
            } else {
                $this->info('id from env file');
                $this->checkAndSetSystemUser(env(static::$system_user_id_const_name));
            }
        }else{
            $this->info("id from command  param");
            $this->checkAndSetSystemUser($this->option('id'));
        }



        $this->line('System User set Successfully');

    }


    public function checkAndSetSystemUser($id = null)
    {
        $system_user_data = [
            "id" => $id,
            "name" => config('blame.system_user_name'),
            "email" => config('blame.system_user_email'),
            "password" => ''
        ];

        if (!$this->userWithIdExists($id)) {
            if (config()->has('auth.providers.users.model')) {
                $user_model = config('auth.providers.users.model');
                $system_user = new $user_model($system_user_data);
                if (!empty($id)) {
                    $system_user->id=$id;
                }
                try {
                    echo "creating form model";
                    //dd($system_user);
                    $system_user->save();
                    $created_system_user_id = $system_user->id;
                } catch (\Exception $exception) {
                    throw new \RuntimeException(static::class." Can't create new user, ".$exception->getMessage());
                }
            } elseif (config()->has('auth.providers.users.table')) {
                $user_table = config('auth.providers.users.table');
                if (!empty($id)) {
                    $system_user_data['id'] = $id;
                }
                try {
                    echo "creating form table";
                    //todo: add migration executed comprobation in order to add updated by and created by fields
                    $created_system_user_id = DB::table($user_table)->insertGetId(
                        $system_user_data
                    );
                } catch (\Exception $exception) {
                    throw new \RuntimeException("Cant create new user, ".$exception->getMessage());
                }

            }
            $id=$created_system_user_id;

        }
        $this->setEnvValue(static::$system_user_id_const_name, $id);
    }


    public function userWithIdExists(?int $id): bool
    {
        if (config()->has('auth.providers.users.model')) {
            $user_model = config('auth.providers.users.model');
            return $user_model::where('id', $id)->exists();
        }

        if (config()->has('auth.providers.users.table')) {
            $user_table = config('auth.providers.users.table');
            return DB::table($user_table)->where('id', $id)->exists();
        }

        throw new \RuntimeException(static::class . " unable to determinate from where to retrieve users records. Providers users model or table is not specified. Please set providers.users.model or providers.users.table on auth config file");
    }


}
