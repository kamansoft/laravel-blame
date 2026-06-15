<?php

use Illuminate\Support\Facades\Schema;
use Kamansoft\LaravelBlame\Tests\Fixtures\TestUser;

beforeEach(function (): void {
    $this->createUsersTable();
});

it('creates a system user and writes its id to the configured env file', function (): void {
    $envFile = tempnam(sys_get_temp_dir(), 'laravel-blame-command-env-');
    file_put_contents($envFile, '');

    config()->set('blame.env_file_path', $envFile);

    $this->artisan('blame:set:systemuser', ['--key' => 7])
        ->assertExitCode(0);

    expect(TestUser::query()->whereKey(7)->exists())->toBeTrue()
        ->and(file_get_contents($envFile))->toContain('BLAME_SYSTEM_USER_ID=7');

    unlink($envFile);
});

it('creates a blame fields migration for an existing table', function (): void {
    Schema::create('articles', function ($table) {
        $table->id();
        $table->string('title');
    });

    $migrationPath = 'tmp/laravel-blame-migrations-command-test-'.uniqid();

    $this->artisan('blame:make:migration', [
        'table' => 'articles',
        '--path' => $migrationPath,
    ])->assertExitCode(0);

    $absoluteMigrationPath = base_path($migrationPath);
    $files = glob($absoluteMigrationPath.'/*.php');

    expect($files)->toHaveCount(1);

    $migrationFile = basename($files[0]);

    expect($migrationFile)->toContain('add_blaming_fields_to_articles_table');
});
