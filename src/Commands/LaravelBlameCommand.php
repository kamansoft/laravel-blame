<?php

namespace Kamansoft\LaravelBlame\Commands;

use Illuminate\Console\Command;

class LaravelBlameCommand extends Command
{
    public $signature = 'laravel-blame';

    public $description = '';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
