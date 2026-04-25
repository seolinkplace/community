<?php

namespace Modules\Support\Models;

use Modules\Core\Models\UnifiedUser;
use Modules\Support\Models\SupportTicket;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportTicketMessage extends Model
{
    protected $fillable = [
        'ticket_id', 'sender_id', 'sender_role', 'message', 'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(SupportTicket::class, 'ticket_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(UnifiedUser::class, 'sender_id');
    }
}
