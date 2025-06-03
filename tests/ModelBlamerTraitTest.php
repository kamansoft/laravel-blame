<?php

namespace Kamansoft\LaravelBlame\Tests;

use Illuminate\Support\Facades\Auth;
use Kamansoft\LaravelBlame\Tests\Models\TestModel;
use Kamansoft\LaravelBlame\Tests\Models\User;

class ModelBlamerTraitTest extends TestCase
{
    protected User $user;

    protected User $anotherUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test users
        $this->user = User::factory()->create(['id' => 1]);
        $this->anotherUser = User::factory()->create(['id' => 2]);
    }

    /** @test */
    public function it_sets_created_by_and_updated_by_when_creating_model_with_auth_user()
    {
        // Authenticate a user
        Auth::login($this->user);

        // Create a model
        $model = TestModel::factory()->create(['name' => 'Test Model']);

        // Assert created_by and updated_by are set to the authenticated user's id
        $this->assertEquals($this->user->id, $model->created_by);
        $this->assertEquals($this->user->id, $model->updated_by);
    }

    /** @test */
    public function it_sets_updated_by_when_updating_model_with_auth_user()
    {
        // Create a model with first user
        Auth::login($this->user);
        $model = TestModel::factory()->create(['name' => 'Test Model']);

        // Change to another user and update the model
        Auth::login($this->anotherUser);
        $model->name = 'Updated Model';
        $model->save();

        // Assert created_by remains the same and updated_by is changed
        $this->assertEquals($this->user->id, $model->created_by);
        $this->assertEquals($this->anotherUser->id, $model->updated_by);
    }

    /** @test */
    public function it_sets_system_user_id_when_no_auth_user_exists()
    {
        // Ensure no user is authenticated
        Auth::logout();

        // Create a model without an authenticated user
        $model = TestModel::factory()->create(['name' => 'System Created Model']);

        // Assert created_by and updated_by are set to the system user id
        $this->assertEquals(config('blame.system_user_id'), $model->created_by);
        $this->assertEquals(config('blame.system_user_id'), $model->updated_by);
    }

    /** @test */
    public function it_has_relationships_with_creator_and_updater()
    {
        // Authenticate a user
        Auth::login($this->user);

        // Create a model
        $model = TestModel::factory()->create(['name' => 'Relationship Test']);

        // Assert relationships work correctly
        $this->assertInstanceOf(User::class, $model->creator);
        $this->assertInstanceOf(User::class, $model->updater);
        $this->assertEquals($this->user->id, $model->creator->id);
        $this->assertEquals($this->user->id, $model->updater->id);
    }

    /** @test */
    public function it_gets_correct_user_model_pk_name()
    {
        $model = new TestModel;
        $this->assertEquals('id', $model->getUsersModelPkName());
    }
}
