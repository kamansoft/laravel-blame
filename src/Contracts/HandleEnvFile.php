<?php

namespace Kamansoft\LaravelBlame\Contracts;

interface HandleEnvFile
{
    /**
     * @param  string  $constant  constant key name
     */
    public function envConstantExists(string $constant): bool;

    /**
     * @throws \Exception
     */
    public function setEnvValue(string $constant, string $value = 'null'): bool;

    public function getEnvFileContent(string $file): string|false;
}
