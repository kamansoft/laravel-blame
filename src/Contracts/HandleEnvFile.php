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
     * @return self
     *
     * @throws Exception
     */
    public function setEnvValue(string $constant, string $value = 'null'): self;

    /**
     * @param  string  $file
     * @return string|false
     */
    public function getEnvFileContent(string $file): string|false;
}
