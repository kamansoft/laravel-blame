<?php

namespace Kamansoft\LaravelBlame\Commands;

use http\Exception\RuntimeException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Kamansoft\LaravelBlame\Contracts\HandleEnvFile;
use Kamansoft\LaravelBlame\Traits\EnvFileHandler;

class SystemUserCommand extends \Illuminate\Console\Command implements HandleEnvFile
{
    use EnvFileHandler;

    public static string $system_user_id_const_name = 'BLAME_SYSTEM_USER_ID';

    private string $pkName = '';

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

    private function getNewUserModelInstance(array $fields_values = [])
    {
        if (! $this->validateAuthEloquentModel()) {
            throw new RuntimeException('This command needs an eloquent model to handle users from your persistent storage, you might set this as the users.model value at providers section the of auth config files');
        }

        //return App::make()
        return app()->make(config('auth.providers.users.model'));
    }

    public function getPkName()
    {
        if (empty($this->pkName)) {
            $this->pkName = $this->getNewUserModelInstance()->getKeyName();
        }

        return $this->pkName;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        if (! $this->validateAuthEloquentModel()) {
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

        return self::SUCCESS;
    }

    public function validateAuthEloquentModel()
    {
        if (! config()->has('auth.providers.users.model')) {
            $this->error('This command needs an eloquent model to handle users from your persistent storage, you might set this as the users.model value at providers section the of auth config files  ');

            return false;
        }

        return true;
    }

    public function checkAndSetSystemUser($key = null)
    {
        $system_user_data = [
            'name' => config('blame.system_user_name'),
            'email' => config('blame.system_user_email'),
            'password' => '',
        ];

        /*
        if ( $this->userWithPkExists($key)) {
            return $this->setEnvValue(static::$system_user_id_const_name, $key);
        }*/

        if (! $this->userWithPkExists($key)) {
            $system_user = $this->getNewUserModelInstance();
            $system_user->fill($system_user_data);
            $pkname = $this->getPkName();
            if (! empty($key)) {
                $system_user->$pkname = $key;
            }
            try {
                $system_user->save();
                $key = $system_user->getKey();
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

        return $user_model::where($this->getPkName(), $key)->exists();
        //throw new \RuntimeException(static::class.' unable to determinate from where to retrieve users records. Providers users model or table is not specified. Please set providers.users.model or providers.users.table on auth config file');
    }
}
