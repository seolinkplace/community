<?php
namespace Modules\Core\Models;

use Modules\Core\Models\UnifiedUser;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerformerProfile extends Model
{
    protected $fillable = ['user_id', 'rating', 'completions_count'];
    protected $casts = ['rating' => 'decimal:2'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(UnifiedUser::class, 'user_id');
    }
}
