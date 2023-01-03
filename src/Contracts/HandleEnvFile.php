<?php

namespace Kamansoft\LaravelBlame\Contracts;

interface HandleEnvFile
{
    /**
     * @param  string  $constant constant key name
     * @return bool
     */
    public function envConstantExists(string $constant): bool;

    /**
     * @param  string  $constant
     * @param  string  $value
     * @return bool
     *
     * @throws \Exception
     */
    public function setEnvValue(string $constant, string $value = 'null'): bool;

    /**
     * @param  string  $file
     * @return string|false
     */
    public function getEnvFileContent(string $file): string|false;
}
