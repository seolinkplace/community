<?php

namespace Modules\Core\Models;

use Modules\Core\Models\Client;
use Modules\Sites\Models\Site;
use App\Models\Link;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class TenantToken extends Model
{
    protected $fillable = [
        'client_id', 'user_id', 'site_id', 'token',
        'status', 'link_limit', 'link_type',
        'last_used_at', 'wp_enabled', 'wp_site_url',
    ];

    protected function casts(): array
    {
        return [
            'last_used_at' => 'datetime',
            'wp_enabled'   => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->token)) {
                $model->token = hash('sha256', Str::random(40));
            }
        });
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function links()
    {
        return $this->hasMany(Link::class, 'site_id', 'site_id')
            ->where('client_id', $this->client_id);
    }
}
