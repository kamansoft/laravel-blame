<?php

namespace Kamansoft\LaravelBlame;

use Kamansoft\LaravelBlame\Commands\LaravelBlameCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelBlameServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-blame')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel-blame_table')
            ->hasCommand(LaravelBlameCommand::class);
    }
}
