<?php

namespace Kamansoft\LaravelBlame;

use Kamansoft\LaravelBlame\Commands\BlameFieldsMigrationCommand;
use Kamansoft\LaravelBlame\Commands\SystemUserCommand;
use Kamansoft\LaravelBlame\Database\Migrations\BlameMigrationCreator;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\LaravelPackageTools\Commands\InstallCommand;

class LaravelBlameServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */

        $this->registerBlameMigrationCreator();
        $this->registerBlameMigrationCommandSingleton();

        $package
            ->name('laravel-blame')
            ->hasConfigFile()
            ->hasViews()
            //->hasMigration('create_laravel-blame_table')
            ->hasCommands([
                SystemUserCommand::class,
                BlameFieldsMigrationCommand::class
            ])
            ->hasInstallCommand( function(InstallCommand $command) {
                $command
                    ->startWith(function(InstallCommand $command){

                    })
                    ->askToRunMigrations()
                    ->copyAndRegisterServiceProviderInApp()
                    ->askToStarRepoOnGitHub('kamanosft/laravel-blade');
            });
    }


    public function registerBlameMigrationCommandSingleton()
    {
        $this->app->singleton(BlameFieldsMigrationCommand::class, function ($app) {
            $creator = $app[BlameMigrationCreator::class];
            $composer = $app['composer'];

            return new BlameFieldsMigrationCommand($creator, $composer);
        });
        return $this;
    }

    public function registerBlameMigrationCreator()
    {
        $this->app->singleton(BlameMigrationCreator::class, function ($app) {
            return new BlameMigrationCreator($app['files'], __DIR__ . '/../resources/stubs');
        });
        return $this;
    }
}
