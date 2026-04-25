<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Modules\Core\Models\UnifiedUser;
use Modules\Core\Models\UserRole;
use Modules\Tasks\Models\Task;
use Modules\Tasks\Models\TaskCompletion;
use Modules\Tasks\Services\TaskModerationService;
use Modules\Wallet\Models\Wallet;
use Modules\Wallet\Models\WalletTransaction;
use App\Models\PlatformSetting;

class TaskModerationServiceTest extends TestCase
{
    use RefreshDatabase;

    private TaskModerationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TaskModerationService();
    }

    private function makeUser(): UnifiedUser
    {
        $user = UnifiedUser::create([
            'name'     => 'User',
            'email'    => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'status'   => 'active',
            'locale'   => 'uk',
        ]);
        UserRole::create(['user_id' => $user->id, 'role' => 'performer', 'status' => 'active']);
        return $user;
    }

    private function setPlatformModerationMode(string $mode): void
    {
        PlatformSetting::updateOrCreate(
            ['key' => 'task_moderation_mode'],
            ['value' => $mode]
        );
    }

    private function makeTask(UnifiedUser $creator, ?int $autoApproveHours = 24): Task
    {
        return Task::create([
            'uuid'                 => Str::uuid(),
            'creator_type'         => UnifiedUser::class,
            'creator_id'           => $creator->id,
            'title'                => 'Test Task',
            'url'                  => 'https://example.com',
            'reward'               => 10.00,
            'budget_reserved'      => 100.00,
            'max_completions'      => 10,
            'per_user_limit'       => 1,
            'per_user_daily_limit' => 1,
            'verification_type'    => 'none',
            'completions_count'    => 0,
            'status'               => 'active',
            'auto_approve_hours'   => $autoApproveHours,
        ]);
    }

    // ─── requiresModeration ──────────────────────────────────────────────────

    public function test_requires_moderation_returns_false_when_mode_is_off(): void
    {
        $this->setPlatformModerationMode('off');
        $user = $this->makeUser();
        $this->assertFalse($this->service->requiresModeration($user->id));
    }

    public function test_requires_moderation_returns_true_when_mode_is_all(): void
    {
        $this->setPlatformModerationMode('all');
        $user = $this->makeUser();
        $this->assertTrue($this->service->requiresModeration($user->id));
    }

    public function test_requires_moderation_returns_true_for_new_user_in_new_only_mode(): void
    {
        $this->setPlatformModerationMode('new_only');
        $user = $this->makeUser();
        $this->assertTrue($this->service->requiresModeration($user->id));
    }

    public function test_requires_moderation_returns_false_for_trusted_user_in_new_only_mode(): void
    {
        $this->setPlatformModerationMode('new_only');
        $user = $this->makeUser();
        $user->update(['is_trusted' => true]);
        $this->assertFalse($this->service->requiresModeration($user->id));
    }

    // ─── isTrusted ───────────────────────────────────────────────────────────

    public function test_is_trusted_returns_true_for_manually_trusted_user(): void
    {
        $user = $this->makeUser();
        $user->update(['is_trusted' => true]);
        $this->assertTrue($this->service->isTrusted($user->id));
    }

    public function test_is_trusted_returns_false_for_new_user(): void
    {
        $user = $this->makeUser();
        $this->assertFalse($this->service->isTrusted($user->id));
    }

    public function test_is_trusted_returns_true_when_total_deposits_exceed_threshold(): void
    {
        $user   = $this->makeUser();
        $wallet = Wallet::create(['user_id' => $user->id, 'balance' => 200, 'reserved' => 0]);
        WalletTransaction::create([
            'wallet_id'     => $wallet->id,
            'amount'        => 200.00,
            'type'          => 'deposit',
            'status'        => 'completed',
            'balance_after' => 200.00,
            'gateway'       => 'test',
        ]);
        $this->assertTrue($this->service->isTrusted($user->id));
    }

    public function test_is_trusted_returns_false_when_deposits_below_threshold(): void
    {
        $user   = $this->makeUser();
        $wallet = Wallet::create(['user_id' => $user->id, 'balance' => 50, 'reserved' => 0]);
        WalletTransaction::create([
            'wallet_id'     => $wallet->id,
            'amount'        => 50.00,
            'type'          => 'deposit',
            'status'        => 'completed',
            'balance_after' => 50.00,
            'gateway'       => 'test',
        ]);
        $this->assertFalse($this->service->isTrusted($user->id));
    }

    public function test_is_trusted_returns_false_for_nonexistent_user(): void
    {
        $this->assertFalse($this->service->isTrusted(99999));
    }
}

class ReleaseExpiredClaimsTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(): UnifiedUser
    {
        $user = UnifiedUser::create([
            'name'     => 'User',
            'email'    => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'status'   => 'active',
            'locale'   => 'uk',
        ]);
        UserRole::create(['user_id' => $user->id, 'role' => 'performer', 'status' => 'active']);
        return $user;
    }

    private function makeTask(UnifiedUser $creator): Task
    {
        return Task::create([
            'uuid'                 => Str::uuid(),
            'creator_type'         => UnifiedUser::class,
            'creator_id'           => $creator->id,
            'title'                => 'Test Task',
            'url'                  => 'https://example.com',
            'reward'               => 10.00,
            'budget_reserved'      => 100.00,
            'max_completions'      => 10,
            'per_user_limit'       => 1,
            'per_user_daily_limit' => 1,
            'verification_type'    => 'none',
            'completions_count'    => 0,
            'status'               => 'active',
        ]);
    }

    public function test_releases_expired_claimed_completions(): void
    {
        $creator   = $this->makeUser();
        $performer = $this->makeUser();
        $task      = $this->makeTask($creator);
        TaskCompletion::create([
            'uuid'             => Str::uuid(),
            'task_id'          => $task->id,
            'performer_type'   => UnifiedUser::class,
            'performer_id'     => $performer->id,
            'status'           => 'claimed',
            'claimed_at'       => now()->subHour(),
            'claim_expires_at' => now()->subMinutes(10),
        ]);

        Artisan::call('tasks:release-claims');

        $this->assertDatabaseCount('task_completions', 0);
    }

    public function test_does_not_release_non_expired_claims(): void
    {
        $creator   = $this->makeUser();
        $performer = $this->makeUser();
        $task      = $this->makeTask($creator);
        TaskCompletion::create([
            'uuid'             => Str::uuid(),
            'task_id'          => $task->id,
            'performer_type'   => UnifiedUser::class,
            'performer_id'     => $performer->id,
            'status'           => 'claimed',
            'claimed_at'       => now(),
            'claim_expires_at' => now()->addHour(),
        ]);

        Artisan::call('tasks:release-claims');

        $this->assertDatabaseCount('task_completions', 1);
    }

    public function test_does_not_release_pending_completions(): void
    {
        $creator   = $this->makeUser();
        $performer = $this->makeUser();
        $task      = $this->makeTask($creator);
        TaskCompletion::create([
            'uuid'             => Str::uuid(),
            'task_id'          => $task->id,
            'performer_type'   => UnifiedUser::class,
            'performer_id'     => $performer->id,
            'status'           => 'pending',
            'claimed_at'       => now()->subHour(),
            'claim_expires_at' => now()->subMinutes(10),
        ]);

        Artisan::call('tasks:release-claims');

        $this->assertDatabaseCount('task_completions', 1);
    }

    public function test_handles_empty_table_gracefully(): void
    {
        $exitCode = Artisan::call('tasks:release-claims');
        $this->assertEquals(0, $exitCode);
        $this->assertDatabaseCount('task_completions', 0);
    }
}

class AutoApproveCompletionsTest extends TestCase
{
    use RefreshDatabase;

    private function makeWalletForUser(UnifiedUser $user): void
    {
        // payReward looks for wallet via whereHas('client') or webmaster_wallets
        // Simplest: create wallet with user_id directly via DB to bypass FK
        \Illuminate\Support\Facades\DB::table('clients')->insertOrIgnore([
            'id'         => $user->id,
            'name'       => $user->name,
            'plan'       => 'starter',
            'status'     => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        \Modules\Wallet\Models\Wallet::firstOrCreate(
            ['client_id' => $user->id],
            ['user_id' => $user->id, 'balance' => 0, 'reserved' => 0]
        );
    }

    private function makeUser(): UnifiedUser
    {
        $user = UnifiedUser::create([
            'name'     => 'User',
            'email'    => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'status'   => 'active',
            'locale'   => 'uk',
        ]);
        UserRole::create(['user_id' => $user->id, 'role' => 'performer', 'status' => 'active']);
        return $user;
    }

    private function makeTask(UnifiedUser $creator, ?int $autoApproveHours = 24): Task
    {
        return Task::create([
            'uuid'                 => Str::uuid(),
            'creator_type'         => UnifiedUser::class,
            'creator_id'           => $creator->id,
            'title'                => 'Test Task',
            'url'                  => 'https://example.com',
            'reward'               => 10.00,
            'budget_reserved'      => 100.00,
            'max_completions'      => 10,
            'per_user_limit'       => 1,
            'per_user_daily_limit' => 1,
            'verification_type'    => 'none',
            'completions_count'    => 0,
            'status'               => 'active',
            'auto_approve_hours'   => $autoApproveHours,
        ]);
    }

    public function test_auto_approves_pending_completion_past_deadline(): void
    {
        $creator    = $this->makeUser();
        $performer  = $this->makeUser();
        $task       = $this->makeTask($creator, 24);
        $this->makeWalletForUser($performer);
        $completion = new TaskCompletion([
            'uuid'           => Str::uuid(),
            'task_id'        => $task->id,
            'performer_type' => UnifiedUser::class,
            'performer_id'   => $performer->id,
            'status'         => 'pending',
        ]);
        $completion->timestamps = false;
        $completion->created_at = now()->subHours(48);
        $completion->updated_at = now()->subHours(48);
        $completion->save();

        Artisan::call('tasks:auto-approve');
        $out = Artisan::output();

        $this->assertEquals('approved', $completion->fresh()->status, "Command output: $out");
    }

    public function test_does_not_auto_approve_recent_completion(): void
    {
        $creator    = $this->makeUser();
        $performer  = $this->makeUser();
        $task       = $this->makeTask($creator, 24);
        $this->makeWalletForUser($performer);
        $completion = TaskCompletion::create([
            'uuid'           => Str::uuid(),
            'task_id'        => $task->id,
            'performer_type' => UnifiedUser::class,
            'performer_id'   => $performer->id,
            'status'         => 'pending',
            'created_at'     => now()->subHours(12),
            'updated_at'     => now()->subHours(12),
        ]);

        Artisan::call('tasks:auto-approve');

        $this->assertEquals('pending', $completion->fresh()->status);
    }

    public function test_does_not_auto_approve_when_task_has_no_auto_approve_hours(): void
    {
        $creator    = $this->makeUser();
        $performer  = $this->makeUser();
        $task       = $this->makeTask($creator, null);
        $this->makeWalletForUser($performer);
        $completion = TaskCompletion::create([
            'uuid'           => Str::uuid(),
            'task_id'        => $task->id,
            'performer_type' => UnifiedUser::class,
            'performer_id'   => $performer->id,
            'status'         => 'pending',
            'created_at'     => now()->subDays(7),
            'updated_at'     => now()->subDays(7),
        ]);

        Artisan::call('tasks:auto-approve');

        $this->assertEquals('pending', $completion->fresh()->status);
    }

    public function test_does_not_change_already_approved_completion(): void
    {
        $creator    = $this->makeUser();
        $performer  = $this->makeUser();
        $task       = $this->makeTask($creator, 24);
        $this->makeWalletForUser($performer);
        $completion = TaskCompletion::create([
            'uuid'           => Str::uuid(),
            'task_id'        => $task->id,
            'performer_type' => UnifiedUser::class,
            'performer_id'   => $performer->id,
            'status'         => 'approved',
            'created_at'     => now()->subDays(3),
            'updated_at'     => now()->subDays(3),
        ]);

        Artisan::call('tasks:auto-approve');
        $out = Artisan::output();

        $this->assertEquals('approved', $completion->fresh()->status, "Command output: $out");
    }
}
