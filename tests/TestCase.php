<?php

namespace Kamansoft\LaravelBlame\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Kamansoft\LaravelBlame\LaravelBlameServiceProvider;
use Kamansoft\LaravelBlame\Tests\Fixtures\TestUser;
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

    public function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'testing');
        config()->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        config()->set('auth.providers.users.model', TestUser::class);
        $envFile = tempnam(sys_get_temp_dir(), 'laravel-blame-env-');
        file_put_contents($envFile, '');
        config()->set('blame.env_file_path', $envFile);
    }

    protected function createUsersTable(): void
    {
        Schema::create('test_users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password')->default('');
        });
    }

    protected function createCentralUsersTable(): void
    {
        Schema::create('central_users', function (Blueprint $table) {
            $table->string('uuid')->primary();
            $table->string('name');
        });
    }

    protected function createPostsTable(): void
    {
        Schema::create('blamed_posts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
        });
    }

    protected function createTenantPostsTable(): void
    {
        Schema::create('tenant_posts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
        });
    }
}
