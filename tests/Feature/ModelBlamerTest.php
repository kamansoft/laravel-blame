<?php

use Illuminate\Support\Facades\Auth;
use Kamansoft\LaravelBlame\Tests\Fixtures\BlamedPost;
use Kamansoft\LaravelBlame\Tests\Fixtures\CentralUser;
use Kamansoft\LaravelBlame\Tests\Fixtures\CentralUserResolver;
use Kamansoft\LaravelBlame\Tests\Fixtures\TenantPost;
use Kamansoft\LaravelBlame\Tests\Fixtures\TestUser;

beforeEach(function (): void {
    $this->createUsersTable();
    $this->createPostsTable();
    $this->createCentralUsersTable();
    $this->createTenantPostsTable();
});

it('fills created_by and updated_by with the authenticated user id', function (): void {
    $user = TestUser::query()->create([
        'name' => 'Ada Lovelace',
        'email' => 'ada@example.com',
        'password' => '',
    ]);

    Auth::login($user);

    $post = BlamedPost::query()->create(['name' => 'First post']);

    expect($post->created_by)->toBe((string) $user->id)
        ->and($post->updated_by)->toBe((string) $user->id);

    $post->update(['name' => 'Updated post']);

    expect((string) $post->fresh()->updated_by)->toBe((string) $user->id);
});

it('uses the configured system user id when no user is authenticated', function (): void {
    config()->set('blame.system_user_id', 42);

    $post = BlamedPost::query()->create(['name' => 'System post']);

    expect($post->created_by)->toBe('42')
        ->and($post->updated_by)->toBe('42');
});

it('uses a closure user resolver and custom user id attribute', function (): void {
    $centralUser = CentralUser::query()->create([
        'uuid' => 'user-uuid-123',
        'name' => 'Central User',
    ]);

    config()->set('blame.user_resolver', fn () => $centralUser);
    config()->set('blame.user_id_attribute', 'uuid');

    $post = TenantPost::query()->create(['name' => 'Tenant post']);

    expect($post->created_by)->toBe('user-uuid-123')
        ->and($post->updated_by)->toBe('user-uuid-123');
});

it('uses a class based user resolver', function (): void {
    CentralUser::query()->create([
        'uuid' => 'class-resolver-user',
        'name' => 'Resolved User',
    ]);

    config()->set('blame.user_resolver_class', CentralUserResolver::class);
    config()->set('blame.user_id_attribute', 'uuid');

    $post = TenantPost::query()->create(['name' => 'Tenant post']);

    expect($post->created_by)->toBe('class-resolver-user')
        ->and($post->updated_by)->toBe('class-resolver-user');
});

it('uses a closure system user resolver', function (): void {
    config()->set('blame.system_user_resolver', fn () => 'system-uuid');

    $post = TenantPost::query()->create(['name' => 'System tenant post']);

    expect($post->created_by)->toBe('system-uuid')
        ->and($post->updated_by)->toBe('system-uuid');
});
