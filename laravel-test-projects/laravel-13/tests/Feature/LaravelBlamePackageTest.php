<?php

namespace Tests\Feature;

use App\Models\BlamedPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class LaravelBlamePackageTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_blames_created_and_updated_records_with_the_authenticated_user(): void
    {
        $user = User::factory()->create();

        Auth::login($user);

        $post = BlamedPost::query()->create(['title' => 'Hello Sail']);

        $this->assertSame((string) $user->id, $post->created_by);
        $this->assertSame((string) $user->id, $post->updated_by);

        $post->update(['title' => 'Updated from Sail']);

        $this->assertSame((string) $user->id, $post->fresh()->updated_by);
    }

    public function test_it_uses_the_configured_system_user_when_no_user_is_authenticated(): void
    {
        config()->set('blame.system_user_id', 1);

        $post = BlamedPost::query()->create(['title' => 'System post']);

        $this->assertSame('1', $post->created_by);
        $this->assertSame('1', $post->updated_by);
    }
}
