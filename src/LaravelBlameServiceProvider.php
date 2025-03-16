<?php

namespace Kamansoft\LaravelBlame;

use _PHPStan_5c71ab23c\Nette\Neon\Exception;
use Illuminate\Support\Facades\Artisan;
use Kamansoft\LaravelBlame\Commands\BlameFieldsMigrationCommand;
use Kamansoft\LaravelBlame\Commands\SystemUserCommand;
use Kamansoft\LaravelBlame\Database\Migrations\BlameMigrationCreator;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelBlameServiceProvider extends PackageServiceProvider
{
    /**
     * @throws Exception
     */
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-blame');

        if (! config()->has('auth.providers.users.model')) {
            throw new \Exception($package->name.' package needs an eloquent model to handle users from your persistent storage, you might set this as the users.model value at providers section the of auth config files in your laravel project');
        }
        $this->mergeConfigFrom(__DIR__.'/../config/private_blame.php', 'blame');
        $this->registerBlameMigrationCreator();
        $this->registerBlameMigrationCommandSingleton();
        $package
            ->hasConfigFile()
            ->hasViews()
            // ->hasMigration('create_laravel-blame_table')
            ->hasCommands([
                SystemUserCommand::class,
                BlameFieldsMigrationCommand::class,
            ])
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->startWith(function (InstallCommand $command) {
                        $command->line('creating system user');
                        Artisan::call('blame:set:systemuser');
                    })
                    ->publishConfigFile()
                    ->askToStarRepoOnGitHub('kamansoft/laravel-blade');
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
            return new BlameMigrationCreator($app['files'], __DIR__.'/../resources/stubs');
        });

        return $this;
    }
}
