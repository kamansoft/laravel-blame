<?php

namespace Kamansoft\LaravelBlame\Tests\Fixtures;

use Illuminate\Foundation\Auth\User as Authenticatable;

class TestUser extends Authenticatable
{
    public $timestamps = false;

    protected $table = 'test_users';

    protected $fillable = [
        'name',
        'email',
        'password',
    ];
}
