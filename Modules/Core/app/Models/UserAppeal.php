<?php
namespace Modules\Core\Models;

use Modules\Core\Models\UnifiedUser;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAppeal extends Model
{
    protected $fillable = [
        'user_id',
        'appeal_type',
        'reference_id',
        'message',
        'status',
        'admin_note',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(UnifiedUser::class, 'user_id');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(UnifiedUser::class, 'reviewed_by');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
