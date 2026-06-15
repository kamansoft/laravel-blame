<?php

namespace Kamansoft\LaravelBlame\Traits;

trait EnvFileHandler
{
    public function envConstantExists(string $constant): bool
    {
        $str = $this->getEnvFileContent($this->getEnvFilePath());

        return $str !== false && str_contains($str, $constant);
    }

    public function setEnvValue(string $constant, string $value = 'null'): bool
    {
        $file = $this->getEnvFilePath();
        $line = $constant.'='.$value;
        $str = $this->getEnvFileContent($file);

        if (str_contains($str, $constant)) {
            $pattern = '/^'.preg_quote($constant, '/').'=.*$/m';
            $updated = preg_replace($pattern, $line, $str, 1, $count);

            if ($count === 0 || $updated === null) {
                return false;
            }
        } else {
            $updated = rtrim($str).PHP_EOL.$line.PHP_EOL;
        }

        return (bool) file_put_contents($file, $updated);
    }

    public function getEnvFileContent(string $file): string|false
    {
        if (! is_file($file)) {
            throw new \RuntimeException(static::class." $file is not a valid file");
        }

        return file_get_contents($file);
    }

    private function getEnvFilePath(): string
    {
        return (string) config('blame.env_file_path', base_path('.env'));
    }
}
