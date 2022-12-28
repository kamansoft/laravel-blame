<?php

namespace Kamansoft\LaravelBlame\Traits;

trait EnvFileHandler
{
    public function envConstantExists(string $constant): bool
    {
        $str = $this->getEnvFileContent(app_path('../.env'));

        return ! ($str !== false && ! str_contains($str, $constant));
    }

    /**
     * @param  string  $constant
     * @param  string  $value
     * @return self
     */
    public function setEnvValue(string $constant, string $value = 'null'): self
    {
        $this->info("Atempt to set $constant=$value to .env file");
        $str = $this->getEnvFileContent(app_path('../.env'));

        if ($str !== false && ! str_contains($str, $constant)) {
            file_put_contents(app_path('../.env'), $str.PHP_EOL.$constant.'='.$value.PHP_EOL);
        } elseif (str_contains($str, $constant) and $value === env($constant)) {
            $this->info("Constant already set to $value");
        } else {
            throw new \RuntimeException('Cant set new value of  '.$constant.' entry on the project´s .env  file, the constant was previously set in the past, please remove the line with: " '.$constant.'='.env($constant).' " from your .env file and run this command again ');
        }

        $this->info('Constant successfully added to file');

        return $this;
    }

    /**
     * @param  string  $file
     * @return string|false
     */
    public function getEnvFileContent(string $file): string|false
    {
        if (! is_file($file)) {
            throw new \RuntimeException(static::class." $file is not a valid  file");
        }

        return file_get_contents($file);
    }
}
