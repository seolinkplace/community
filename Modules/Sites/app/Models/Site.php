<?php

namespace Modules\Sites\Models;

use App\Models\Webmaster;
use App\Models\Article;
use App\Models\TenantToken;
use App\Models\CampaignLink;
use App\Models\Link;
use Modules\Core\Models\UnifiedUser;
use Modules\Sites\Models\SiteLanguage;
use Modules\Sites\Models\SiteConnection;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Site extends Model
{
    use SoftDeletes;

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $model): void {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    protected $fillable = [
        'webmaster_id', 'user_id', 'domain', 'uuid', 'niche', 'language',
        'dr', 'traffic', 'content_type', 'price',
        'description', 'contact', 'status',
        'verification_token',
        'verified_at', 'visibility',
        'metrics_source', 'metrics_updated_at',
        'platform_type', 'platform_url', 'followers_count', 'first_post_published', 'first_post_url', 'first_post_required',
        'domain_registered_at', 'domain_expires_at', 'spam_score', 'pages_count',
        'link_block_settings',
        'deleted_by',
    ];

    public function getRouteKeyName(): string
    {
        return 'domain';
    }

    public function resolveRouteBinding($value, $field = null): ?static
    {
        return static::withTrashed()->where($field ?? $this->getRouteKeyName(), $value)->first();
    }


    protected function casts(): array
    {
        return [
            'metrics_updated_at' => 'datetime',
            'price' => 'decimal:2',
            'first_post_published' => 'boolean',
            'first_post_required' => 'boolean',
            'followers_count' => 'integer',
            'domain_registered_at' => 'date',
            'domain_expires_at' => 'date',
            'spam_score' => 'integer',
            'pages_count' => 'integer',
            'link_block_settings' => 'array',
            'verified_at' => 'datetime',
        ];
    }

    public function siteLanguages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SiteLanguage::class);
    }

    public function webmaster(): BelongsTo
    {
        return $this->belongsTo(Webmaster::class);
    }

    public function unifiedUser(): BelongsTo
    {
        return $this->belongsTo(UnifiedUser::class, 'user_id');
    }

    public function links(): HasMany
    {
        return $this->hasMany(Link::class);
    }

    public function campaignLinks(): HasMany
    {
        return $this->hasMany(CampaignLink::class);
    }


    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function tenantTokens(): HasMany
    {
        return $this->hasMany(TenantToken::class);
    }
    public function wpSite(): HasOne
    {
        return $this->hasOne(SiteConnection::class);
    }

}
