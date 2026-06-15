<?php

namespace Kamansoft\LaravelBlame\Traits;

use Illuminate\Database\Eloquent\Model;

trait UserModelForAuth
{
    private string $usersModelPkName = '';

    private function getUserModelForAuthInstance(): Model
    {
        if (! $this->validateAuthEloquentModel()) {
            throw new \RuntimeException(static::class.' Needs an eloquent model to handle users from your persistent storage, you might set this as the users.model value at providers section the of auth config file.');
        }

        return app()->make(config('auth.providers.users.model'));
    }

    public function validateAuthEloquentModel(): bool
    {
        if (! config()->has('auth.providers.users.model')) {
            return false;
        }

        return true;
    }

    public function getUsersModelPkName(): string
    {
        if ($this->usersModelPkName === '') {
            $this->usersModelPkName = $this->getUserModelForAuthInstance()->getKeyName();
        }

        return $this->usersModelPkName;
    }
}
