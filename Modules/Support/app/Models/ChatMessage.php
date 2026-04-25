<?php
namespace Modules\Support\Models;

use App\Models\CampaignLink;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    protected $fillable = [
        'campaign_link_id', 'sender_type', 'sender_id',
        'body', 'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function campaignLink(): BelongsTo
    {
        return $this->belongsTo(CampaignLink::class);
    }

    public function sender(): object
    {
        return match($this->sender_type) {
            'client'     => Client::find($this->sender_id),
            'webmaster'  => Webmaster::find($this->sender_id),
            default      => null,
        };
    }

    public function getSenderNameAttribute(): string
    {
        return match($this->sender_type) {
            'client'    => Client::find($this->sender_id)?->name ?? 'Клієнт',
            'webmaster' => Webmaster::find($this->sender_id)?->name ?? 'Вебмастер',
            'admin'     => 'Адмін',
            default     => '—',
        };
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }
}
