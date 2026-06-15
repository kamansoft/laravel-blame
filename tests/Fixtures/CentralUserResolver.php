<?php

namespace Kamansoft\LaravelBlame\Tests\Fixtures;

use Kamansoft\LaravelBlame\Contracts\ResolvesBlameUser;

class CentralUserResolver implements ResolvesBlameUser
{
    public function __invoke(): mixed
    {
        return CentralUser::query()->firstOrFail();
    }
}
