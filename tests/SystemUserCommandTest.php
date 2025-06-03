<?php

namespace Kamansoft\LaravelBlame\Tests;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Kamansoft\LaravelBlame\Commands\SystemUserCommand;
use Kamansoft\LaravelBlame\Tests\Models\User;

class SystemUserCommandTest extends TestCase
{
    protected $envFilePath;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a temp env file at the location expected by the EnvFileHandler trait
        $this->envFilePath = app_path('../.env');

        // Ensure directory exists
        $directory = dirname($this->envFilePath);
        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        // Create empty .env file
        File::put($this->envFilePath, '');
    }

    protected function tearDown(): void
    {
        // Clean up the temp env file
        if (File::exists($this->envFilePath)) {
            File::delete($this->envFilePath);
        }

        parent::tearDown();
    }

    /** @test */
    public function it_creates_system_user_with_no_key_provided()
    {
        // Execute the command
        $this->artisan('blame:set:systemuser')
            ->assertExitCode(0);

        // Check if system user was created in DB
        $systemUser = User::where('name', config('blame.system_user_name'))
            ->where('email', config('blame.system_user_email'))
            ->first();

        $this->assertNotNull($systemUser);

        // Check if BLAME_SYSTEM_USER_ID was set in env file
        $envContent = File::get($this->envFilePath);
        $this->assertStringContainsString('BLAME_SYSTEM_USER_ID=' . $systemUser->id, $envContent);
    }

    /** @test */
    public function it_creates_system_user_with_specific_key()
    {
        $specificId = 12345;

        // Execute the command with specific key
        $this->artisan('blame:set:systemuser', ['--key' => $specificId])
            ->assertExitCode(0);

        // Check if system user was created with the specific id
        $systemUser = User::find($specificId);
        $this->assertNotNull($systemUser);
        $this->assertEquals(config('blame.system_user_name'), $systemUser->name);
        $this->assertEquals(config('blame.system_user_email'), $systemUser->email);

        // Check if BLAME_SYSTEM_USER_ID was set in env file with specific key
        $envContent = File::get($this->envFilePath);
        $this->assertStringContainsString('BLAME_SYSTEM_USER_ID=' . $specificId, $envContent);
    }

    /** @test */
    public function it_uses_existing_user_when_key_provided_matches_existing_user()
    {
        // Create a user first
        $existingUser = User::factory()->create();
        $initialUserCount = User::count();

        // Execute the command with existing user id
        $this->artisan('blame:set:systemuser', ['--key' => $existingUser->id])
            ->assertExitCode(0);

        // Check that no new user was created
        $this->assertEquals($initialUserCount, User::count());

        // Check if BLAME_SYSTEM_USER_ID was set in env file
        $envContent = File::get($this->envFilePath);
        $this->assertStringContainsString('BLAME_SYSTEM_USER_ID=' . $existingUser->id, $envContent);
    }

    /** @test */
    public function it_fails_when_auth_provider_is_not_configured()
    {
        // Save original config
        $originalConfig = config('auth.providers.users.model');

        try {
            // Remove the auth config entirely instead of setting to empty string
            config()->set('auth.providers.users', null);

            // Execute the command - we expect it to fail
            $this->artisan('blame:set:systemuser')
                ->assertExitCode(1);

        } finally {
            // Restore config
            config()->set('auth.providers.users.model', $originalConfig);
        }
    }
}
