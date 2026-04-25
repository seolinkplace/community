<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlatformRule extends Model
{
    protected $fillable = [
        'slug', 'title_uk', 'title_en',
        'body_uk', 'body_en',
        'sort_order', 'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'sort_order'   => 'integer',
    ];

    public function comments(): HasMany
    {
        return $this->hasMany(RuleComment::class, 'rule_id');
    }

    public function publishedComments(): HasMany
    {
        return $this->hasMany(RuleComment::class, 'rule_id')
            ->where('is_hidden', false)
            ->whereNull('parent_id')
            ->with(['replies' => fn($q) => $q->where('is_hidden', false)->with('user')])
            ->with('user')
            ->latest();
    }

    public function title(): string
    {
        $locale = app()->getLocale();
        return $locale === 'en' ? $this->title_en : $this->title_uk;
    }

    public function body(): string
    {
        $locale = app()->getLocale();
        return $locale === 'en' ? $this->body_en : $this->body_uk;
    }
}
