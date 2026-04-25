<?php

namespace Modules\Core\Models;

use Modules\Core\Models\UnifiedUser;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebmasterProfile extends Model
{
    protected $fillable = [
        'user_id',
        'website',
        'payment_details',
        'usdt_address',
        'direct_payments_enabled',
    ];

    protected $casts = [
        'direct_payments_enabled' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(UnifiedUser::class, 'user_id');
    }
}
