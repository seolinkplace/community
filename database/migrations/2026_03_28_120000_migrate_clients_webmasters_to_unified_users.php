<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        // ─── 1. Переносимо clients ───────────────────────────────────────────
        $clients = DB::table('clients')->get();

        foreach ($clients as $client) {
            // Перевіряємо чи вже є такий email
            $existing = DB::table('unified_users')
                ->where('email', $client->email)
                ->first();

            if (!$existing) {
                $userId = DB::table('unified_users')->insertGetId([
                    'name'       => $client->name,
                    'email'      => $client->email,
                    'password'   => $client->password,
                    'status'     => $client->status ?? 'active',
                    'chat_banned_at' => $client->chat_banned_at ?? null,
                    'created_at' => $client->created_at,
                    'updated_at' => $client->updated_at,
                ]);
            } else {
                $userId = $existing->id;
            }

            // Роль client
            DB::table('user_roles')->insertOrIgnore([
                'user_id'    => $userId,
                'role'       => 'client',
                'status'     => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Профіль клієнта
            DB::table('client_profiles')->insertOrIgnore([
                'user_id'       => $userId,
                'company_name'  => $client->company_name ?? null,
                'plan'          => $client->plan ?? 'free',
                'trial_ends_at' => $client->trial_ends_at ?? null,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            // Зберігаємо маппінг old client_id → new user_id
            DB::table('_migration_map')->insertOrIgnore([
                'old_type' => 'client',
                'old_id'   => $client->id,
                'new_user_id' => $userId,
            ]);
        }

        // ─── 2. Переносимо webmasters ────────────────────────────────────────
        $webmasters = DB::table('webmasters')->get();

        foreach ($webmasters as $wm) {
            $existing = DB::table('unified_users')
                ->where('email', $wm->email)
                ->first();

            if (!$existing) {
                $userId = DB::table('unified_users')->insertGetId([
                    'name'       => $wm->name,
                    'email'      => $wm->email,
                    'password'   => $wm->password,
                    'status'     => $wm->status ?? 'active',
                    'chat_banned_at' => $wm->chat_banned_at ?? null,
                    'created_at' => $wm->created_at,
                    'updated_at' => $wm->updated_at,
                ]);
            } else {
                $userId = $existing->id;
            }

            // Роль webmaster
            DB::table('user_roles')->insertOrIgnore([
                'user_id'    => $userId,
                'role'       => 'webmaster',
                'status'     => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Профіль вебмастера
            DB::table('webmaster_profiles')->insertOrIgnore([
                'user_id'          => $userId,
                'website'          => null,
                'payment_details'  => null,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);

            DB::table('_migration_map')->insertOrIgnore([
                'old_type'    => 'webmaster',
                'old_id'      => $wm->id,
                'new_user_id' => $userId,
            ]);
        }
    }

    public function down(): void
    {
        DB::table('unified_users')->truncate();
        DB::table('user_roles')->truncate();
        DB::table('client_profiles')->truncate();
        DB::table('webmaster_profiles')->truncate();
        DB::table('_migration_map')->truncate();
    }
};
