<?php
namespace Modules\Core\Models;

use Modules\Core\Models\UnifiedUser;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientProfile extends Model
{
    protected $fillable = ['user_id', 'company_name', 'plan', 'trial_ends_at'];
    protected $casts = ['trial_ends_at' => 'datetime'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(UnifiedUser::class, 'user_id');
    }
}
