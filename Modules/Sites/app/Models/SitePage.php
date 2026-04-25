<?php

namespace Modules\Sites\Models;

use Modules\Sites\Models\SiteConnection;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SitePage extends Model
{
    protected $fillable = [
        'site_id', 'url', 'title', 'anchors',
        'wp_post_id', 'post_type', 'status', 'link_limit',
        'published_at', 'synced_at',
    ];

    protected function casts(): array
    {
        return [
            'anchors'      => 'array',
            'published_at' => 'datetime',
            'synced_at'    => 'datetime',
        ];
    }

    public function siteConnection(): BelongsTo
    {
        return $this->belongsTo(SiteConnection::class, 'site_id');
    }
}
