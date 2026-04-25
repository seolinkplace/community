<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Modules\Core\Models\UnifiedUser;
use Modules\Core\Models\UserRole;
use Modules\Core\Helpers\AuthHelper;

class UnifiedUserTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(array $attrs = []): UnifiedUser
    {
        return UnifiedUser::create(array_merge([
            'name'     => 'Test User',
            'email'    => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'status'   => 'active',
            'locale'   => 'uk',
        ], $attrs));
    }

    private function addRole(UnifiedUser $user, string $role): UserRole
    {
        return UserRole::create([
            'user_id' => $user->id,
            'role'    => $role,
            'status'  => 'active',
        ]);
    }

    // ─── hasRole ─────────────────────────────────────────────────────────────

    public function test_has_role_returns_true_when_role_exists(): void
    {
        $user = $this->makeUser();
        $this->addRole($user, 'client');
        $this->assertTrue($user->hasRole('client'));
    }

    public function test_has_role_returns_false_when_role_missing(): void
    {
        $user = $this->makeUser();
        $this->assertFalse($user->hasRole('client'));
    }

    public function test_has_role_returns_false_for_suspended_role(): void
    {
        $user = $this->makeUser();
        UserRole::create(['user_id' => $user->id, 'role' => 'webmaster', 'status' => 'suspended']);
        $this->assertFalse($user->hasRole('webmaster'));
    }

    public function test_user_can_have_multiple_roles(): void
    {
        $user = $this->makeUser();
        $this->addRole($user, 'client');
        $this->addRole($user, 'webmaster');
        $this->assertTrue($user->hasRole('client'));
        $this->assertTrue($user->hasRole('webmaster'));
        $this->assertFalse($user->hasRole('performer'));
    }

    // ─── status ──────────────────────────────────────────────────────────────

    public function test_banned_user_has_banned_status(): void
    {
        $user = $this->makeUser(['status' => 'banned']);
        $this->assertEquals('banned', $user->status);
    }

    public function test_active_user_is_not_banned(): void
    {
        $user = $this->makeUser(['status' => 'active']);
        $this->assertEquals('active', $user->status);
    }

    // ─── GDPR ────────────────────────────────────────────────────────────────

    public function test_gdpr_deleted_flag_defaults_to_false(): void
    {
        $user = $this->makeUser();
        $this->assertFalse((bool) $user->gdpr_deleted);
    }

    public function test_gdpr_deleted_can_be_set(): void
    {
        $user = $this->makeUser();
        $user->update(['gdpr_deleted' => true, 'gdpr_deleted_at' => now()]);
        $this->assertTrue((bool) $user->fresh()->gdpr_deleted);
        $this->assertNotNull($user->fresh()->gdpr_deleted_at);
    }

    // ─── locale ──────────────────────────────────────────────────────────────

    public function test_user_defaults_to_uk_locale(): void
    {
        $user = $this->makeUser();
        $this->assertEquals('uk', $user->locale);
    }

    public function test_user_locale_can_be_set_to_en(): void
    {
        $user = $this->makeUser(['locale' => 'en']);
        $this->assertEquals('en', $user->locale);
    }

    // ─── AuthHelper ──────────────────────────────────────────────────────────

    public function test_auth_helper_returns_null_when_not_authenticated(): void
    {
        $this->assertNull(AuthHelper::client());
        $this->assertNull(AuthHelper::webmaster());
        $this->assertNull(AuthHelper::performer());
        $this->assertNull(AuthHelper::clientId());
        $this->assertNull(AuthHelper::webmasterId());
        $this->assertNull(AuthHelper::performerId());
    }

    public function test_auth_helper_client_returns_user_with_client_role(): void
    {
        $user = $this->makeUser();
        $this->addRole($user, 'client');
        $this->actingAs($user, 'unified');
        $resolved = AuthHelper::client();
        $this->assertNotNull($resolved);
        $this->assertEquals($user->id, $resolved->id);
    }

    public function test_auth_helper_webmaster_returns_user_with_webmaster_role(): void
    {
        $user = $this->makeUser();
        $this->addRole($user, 'webmaster');
        $this->actingAs($user, 'unified');
        $resolved = AuthHelper::webmaster();
        $this->assertNotNull($resolved);
        $this->assertEquals($user->id, $resolved->id);
    }

    public function test_auth_helper_client_returns_null_for_webmaster_only_user(): void
    {
        $user = $this->makeUser();
        $this->addRole($user, 'webmaster');
        $this->actingAs($user, 'unified');
        $this->assertNull(AuthHelper::client());
    }

    public function test_auth_helper_performer_returns_user_with_performer_role(): void
    {
        $user = $this->makeUser();
        $this->addRole($user, 'performer');
        $this->actingAs($user, 'unified');
        $resolved = AuthHelper::performer();
        $this->assertNotNull($resolved);
        $this->assertEquals($user->id, $resolved->id);
    }

    public function test_auth_helper_client_id_returns_correct_id(): void
    {
        $user = $this->makeUser();
        $this->addRole($user, 'client');
        $this->actingAs($user, 'unified');
        $this->assertEquals($user->id, AuthHelper::clientId());
    }

    public function test_auth_helper_webmaster_id_returns_correct_id(): void
    {
        $user = $this->makeUser();
        $this->addRole($user, 'webmaster');
        $this->actingAs($user, 'unified');
        $this->assertEquals($user->id, AuthHelper::webmasterId());
    }
}
