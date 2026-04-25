<?php
namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserRole extends Model
{
    protected $fillable = ['user_id', 'role', 'status'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(UnifiedUser::class, 'user_id');
    }
}
