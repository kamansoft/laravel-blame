<?php

namespace Kamansoft\LaravelBlame\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Kamansoft\LaravelBlame\LaravelBlame
 */
class LaravelBlame extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Kamansoft\LaravelBlame\LaravelBlame::class;
    }
}
