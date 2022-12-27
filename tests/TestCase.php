<?php

namespace Kamansoft\LaravelBlame\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Kamansoft\LaravelBlame\LaravelBlameServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Kamansoft\\LaravelBlame\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelBlameServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        /*
        $migration = include __DIR__.'/../database/migrations/create_laravel-blame_table.php.stub';
        $migration->up();
        */
    }
}
