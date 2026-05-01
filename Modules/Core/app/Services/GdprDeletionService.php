<?php

declare(strict_types=1);

namespace Modules\Core\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Core\Models\UnifiedUser;

class GdprDeletionService
{
    public function anonymize(UnifiedUser $user): void
    {
        DB::transaction(function () use ($user) {
            $userId = $user->id;

            // Профілі
            $user->clientProfile()?->update([
                'phone'   => null,
                'company' => null,
                'website' => null,
            ]);
            $user->webmasterProfile()?->update([
                'phone'   => null,
                'company' => null,
                'website' => null,
                'bio'     => null,
            ]);
            $user->performerProfile()?->update([
                'phone' => null,
                'bio'   => null,
            ]);

            // Кампанії — анонімізуємо назви
            $user->campaigns()->update([
                'name' => DB::raw("CONCAT('deleted_', id)"),
            ]);

            // Статті — очищаємо контент і briefs
            $user->articles()->update([
                'title'         => DB::raw("CONCAT('deleted_', id)"),
                'content'       => '',
                'brief'         => null,
                'brief_comment' => null,
            ]);

            // Сайти — анонімізуємо контактні дані
            $user->sites()->update([
                'contact_email' => null,
                'contact_name'  => null,
                'notes'         => null,
            ]);

            // Support tickets
            DB::table('support_tickets')
                ->where('user_id', $userId)
                ->update(['subject' => DB::raw("CONCAT('deleted_', id)"), 'description' => '']);

            DB::table('support_ticket_messages')
                ->where('sender_id', $userId)
                ->update(['message' => '']);

            // Chat повідомлення
            DB::table('chat_messages')
                ->where('sender_id', $userId)
                ->update(['message' => '']);

            // Contact requests
            DB::table('contact_requests')
                ->where('email', $user->email)
                ->update([
                    'name'    => 'Deleted User',
                    'email'   => 'deleted@deleted.invalid',
                    'message' => '',
                ]);

            // TenantTokens — деактивуємо
            $user->tenantTokens()->update(['status' => 'revoked']);

            // Основний запис
            $user->update([
                'name'            => 'Deleted User',
                'email'           => 'deleted_' . $userId . '@deleted.invalid',
                'password'        => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(32)),
                'google_id'       => null,
                'google_email'    => null,
                'gdpr_deleted'    => true,
                'gdpr_deleted_at' => now(),
                'status'          => 'banned',
            ]);

            Log::info("GDPR: user #{$userId} anonymized");
        });
    }
}
