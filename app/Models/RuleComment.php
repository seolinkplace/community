<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RuleComment extends Model
{
    protected $fillable = [
        'rule_id', 'user_id', 'parent_id', 'body', 'is_hidden',
    ];

    protected $casts = [
        'is_hidden' => 'boolean',
    ];

    public function rule(): BelongsTo
    {
        return $this->belongsTo(PlatformRule::class, 'rule_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(UnifiedUser::class, 'user_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(RuleComment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(RuleComment::class, 'parent_id');
    }
}
