<?php
namespace Modules\Core\Services;

use App\Models\EmailSetting;
use App\Models\EmailLog;
use Illuminate\Support\Facades\Mail;
use Throwable;

class EmailService
{
    public static function send(
        string $type,
        string $recipientRole,
        string $to,
        string $subject,
        string $view,
        array $data = []
    ): bool {
        if (!EmailSetting::isEnabled($type)) {
            EmailLog::create(['type' => $type, 'recipient_role' => $recipientRole, 'status' => 'skipped']);
            return false;
        }

        try {
            Mail::send($view, $data, function ($m) use ($to, $subject) {
                $m->to($to)->subject($subject);
            });
            EmailLog::create(['type' => $type, 'recipient_role' => $recipientRole, 'status' => 'sent']);
            return true;
        } catch (Throwable $e) {
            EmailLog::create([
                'type'           => $type,
                'recipient_role' => $recipientRole,
                'status'         => 'failed',
                'error'          => substr($e->getMessage(), 0, 500),
            ]);
            return false;
        }
    }
}
