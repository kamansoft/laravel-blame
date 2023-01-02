<?php

namespace Kamansoft\LaravelBlame\Commands;

use http\Exception\RuntimeException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Kamansoft\LaravelBlame\Contracts\HandleEnvFile;
use Kamansoft\LaravelBlame\Traits\EnvFileHandler;
use Kamansoft\LaravelBlame\Traits\UserModelForAuth;

class SystemUserCommand extends \Illuminate\Console\Command implements HandleEnvFile
{
    use EnvFileHandler;
    use UserModelForAuth;

    public static string $system_user_id_const_name = 'BLAME_SYSTEM_USER_ID';


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blame:set:systemuser
        {--key= : If a value is passed , this command will check for a user with that value as primary key,  if found, will set the system user id to that value, otherwise a new user with that primary key value  will be created }
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add the system user needed fields in a laravel project table, in order for models to work with laravel blame ';


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {

        if (!$this->validateAuthEloquentModel()) {
            $this->line('systemuser set command execution aborted !');
            return self::FAILURE;
        }

        $key = $this->option('key');
        if (empty($key)) {
            $this->info('No user primary key value from command param');
            if (empty(env(static::$system_user_id_const_name))) {
                $this->info('No user primary key value from env file');
            } else {
                $this->info('Got user primary key value from env file');
                $key = env(static::$system_user_id_const_name);
            }
        } else {
            $this->info('Got user primary key value from command  param');
        }

        if ($this->checkAndSetSystemUser($key)) {
            $this->line('System User set Successfully');
            return self::SUCCESS;
        }

        $this->error('System User set fail');
        return self::FAILURE;


    }


    public function createNewSystemUserUser($key = null)
    {
        $system_user_data = [
            'name' => config('blame.system_user_name'),
            'email' => config('blame.system_user_email'),
            'password' => '',//you cant log in with this user
        ];
        $system_user = $this->getUserModelForAuthInstance()->fill($system_user_data);
        $pkname = $this->getUsersModelPkName();
        if (!empty($key)) {
            $system_user->$pkname = $key;
        }
        $system_user->save();
        return $system_user->getKey();

    }

    public function checkAndSetSystemUser($key = null): bool
    {


        if (!$this->userWithPkExists($key)) {
            try {
                $key=$this->createNewSystemUserUser($key);
            } catch (\Exception $exception) {
                $this->error("Can't create new user");
                $this->line($exception->getMessage());
                return false;
            }
        }
        return $this->setEnvValue(static::$system_user_id_const_name, $key);
    }


    public function userWithPkExists($key): bool
    {
        $user_model = config('auth.providers.users.model');
        return $user_model::where($this->getUsersModelPkName(), $key)->exists();
     }

}
