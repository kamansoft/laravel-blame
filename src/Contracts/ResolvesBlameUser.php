<?php

namespace Kamansoft\LaravelBlame\Contracts;

interface ResolvesBlameUser
{
    public function __invoke(): mixed;
}
