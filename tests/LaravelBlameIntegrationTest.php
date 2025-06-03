<?php

namespace Kamansoft\LaravelBlame\Tests;

use Illuminate\Support\Facades\Auth;
use Kamansoft\LaravelBlame\Tests\Models\TestModel;
use Kamansoft\LaravelBlame\Tests\Models\User;

class LaravelBlameIntegrationTest extends TestCase
{
    protected User $systemUser;

    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a system user with the ID specified in config
        $this->systemUser = User::factory()->create([
            'id' => config('blame.system_user_id'),
            'name' => config('blame.system_user_name'),
            'email' => config('blame.system_user_email'),
        ]);

        // Create a regular user
        $this->regularUser = User::factory()->create();
    }

    /** @test */
    public function full_create_update_lifecycle_with_blame_works_correctly()
    {
        // 1. Create a model with no authenticated user (should use system user)
        Auth::logout();
        $model = TestModel::create(['name' => 'No Auth Creation']);

        $this->assertEquals($this->systemUser->id, $model->created_by);
        $this->assertEquals($this->systemUser->id, $model->updated_by);
        $this->assertEquals($this->systemUser->id, $model->creator->id);
        $this->assertEquals($this->systemUser->id, $model->updater->id);

        // 2. Update the model with an authenticated user
        Auth::login($this->regularUser);
        $model->name = 'Auth Update';
        $model->save();

        // Refresh the model
        $model->refresh();

        // Check that created_by remains the system user but updated_by changes
        $this->assertEquals($this->systemUser->id, $model->created_by);
        $this->assertEquals($this->regularUser->id, $model->updated_by);
        $this->assertEquals($this->systemUser->id, $model->creator->id);
        $this->assertEquals($this->regularUser->id, $model->updater->id);

        // 3. Create another model with an authenticated user
        $model2 = TestModel::create(['name' => 'Auth Creation']);

        $this->assertEquals($this->regularUser->id, $model2->created_by);
        $this->assertEquals($this->regularUser->id, $model2->updated_by);

        // 4. Logout and update again (should use system user for update)
        Auth::logout();
        $model2->name = 'No Auth Update';
        $model2->save();
        $model2->refresh();

        $this->assertEquals($this->regularUser->id, $model2->created_by);
        $this->assertEquals($this->systemUser->id, $model2->updated_by);
    }

    /** @test */
    public function it_correctly_establishes_relationships_between_models()
    {
        // Create a model with the authenticated user
        Auth::login($this->regularUser);
        $model = TestModel::create(['name' => 'Relationship Test']);

        // Test the creator relationship
        $this->assertEquals($this->regularUser->id, $model->creator->id);
        $this->assertEquals($this->regularUser->name, $model->creator->name);
        $this->assertEquals($this->regularUser->email, $model->creator->email);

        // Test the updater relationship
        $this->assertEquals($this->regularUser->id, $model->updater->id);
        $this->assertEquals($this->regularUser->name, $model->updater->name);
        $this->assertEquals($this->regularUser->email, $model->updater->email);
    }

    /** @test */
    public function it_handles_multiple_updates_with_different_users_correctly()
    {
        // Create a second regular user
        $anotherUser = User::factory()->create();

        // Create a model with the first user
        Auth::login($this->regularUser);
        $model = TestModel::create(['name' => 'Multi-User Test']);

        // Update with the second user
        Auth::login($anotherUser);
        $model->name = 'Updated by Second User';
        $model->save();
        $model->refresh();

        $this->assertEquals($this->regularUser->id, $model->created_by);
        $this->assertEquals($anotherUser->id, $model->updated_by);

        // Update with the system user (no auth)
        Auth::logout();
        $model->name = 'Updated by System';
        $model->save();
        $model->refresh();

        $this->assertEquals($this->regularUser->id, $model->created_by);
        $this->assertEquals($this->systemUser->id, $model->updated_by);

        // Update with the first user again
        Auth::login($this->regularUser);
        $model->name = 'Updated by First User Again';
        $model->save();
        $model->refresh();

        $this->assertEquals($this->regularUser->id, $model->created_by);
        $this->assertEquals($this->regularUser->id, $model->updated_by);
    }
}
