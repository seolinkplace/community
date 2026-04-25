<?php
namespace Modules\Support\Observers;

use App\Models\SupportTicket;
use Illuminate\Support\Str;

class SupportTicketObserver
{
    public function creating(SupportTicket $model): void
    {
        if (empty($model->uuid)) {
            $model->uuid = Str::uuid();
        }
    }
}
