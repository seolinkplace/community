<?php

namespace Modules\Support\Models;

use Modules\Core\Models\UnifiedUser;
use Modules\Support\Models\SupportTicketMessage;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportTicket extends Model
{


    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $model): void {
            if (empty($model->uuid)) {
                $model->uuid = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function getRouteKey(): mixed
    {
        return $this->uuid;
    }

    protected $fillable = [
        'user_id', 'role', 'subject', 'status', 'priority', 'assigned_to', 'last_reply_at',
    ];

    protected $casts = [
        'last_reply_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(UnifiedUser::class, 'user_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(UnifiedUser::class, 'assigned_to');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(SupportTicketMessage::class, 'ticket_id')->orderBy('created_at');
    }

    public function unreadCount(int $userId): int
    {
        return $this->messages()
            ->where('sender_id', '!=', $userId)
            ->where('is_read', false)
            ->count();
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'open'        => 'text-green-600',
            'in_progress' => 'text-blue-600',
            'resolved'    => 'text-gray-500',
            'closed'      => 'text-gray-400',
            default       => 'text-gray-500',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'open'        => 'Відкрито',
            'in_progress' => 'В роботі',
            'resolved'    => 'Вирішено',
            'closed'      => 'Закрито',
            default       => $this->status,
        };
    }
}
