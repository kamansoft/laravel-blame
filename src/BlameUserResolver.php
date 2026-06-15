<?php

namespace Kamansoft\LaravelBlame;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BlameUserResolver
{
    public function resolve(): mixed
    {
        $resolver = config('blame.user_resolver');

        if (is_callable($resolver)) {
            return $resolver();
        }

        $resolverClass = config('blame.user_resolver_class');

        if (is_string($resolverClass) && $resolverClass !== '' && class_exists($resolverClass)) {
            return app()->make($resolverClass)();
        }

        $guard = config('blame.auth_guard');

        return $guard ? Auth::guard($guard)->user() : Auth::user();
    }

    public function resolveId(): string
    {
        $user = $this->resolve();

        if ($user === null) {
            Log::warning(static::class.' Not logged user using system user');

            return $this->resolveSystemUserId();
        }

        if (! is_object($user) && ! is_array($user)) {
            return (string) $user;
        }

        $attribute = config('blame.user_id_attribute', 'id');

        if (method_exists($user, 'getKey') && $attribute === 'id') {
            return (string) $user->getKey();
        }

        return (string) data_get($user, $attribute, method_exists($user, 'getKey') ? $user->getKey() : '');
    }

    public function resolveSystemUserId(): string
    {
        $resolver = config('blame.system_user_resolver');

        if (is_callable($resolver)) {
            return (string) $resolver();
        }

        $resolverClass = config('blame.system_user_resolver_class');

        if (is_string($resolverClass) && $resolverClass !== '' && class_exists($resolverClass)) {
            return (string) app()->make($resolverClass)();
        }

        return (string) config('blame.system_user_id', '');
    }
}
