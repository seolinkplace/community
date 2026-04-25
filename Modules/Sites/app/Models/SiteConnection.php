<?php

namespace Modules\Sites\Models;

use App\Models\Webmaster;
use App\Models\TenantToken;
use Modules\Core\Models\UnifiedUser;
use Modules\Sites\Models\Site;
use Modules\Sites\Models\SitePage;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Crypt;

class SiteConnection extends Model
{
    protected $fillable = [
        'tenant_token_id', 'webmaster_id', 'site_id',
        'wp_url', 'wp_username', 'wp_app_password',
        'status', 'wp_version', 'pages_count',
        'free_revisions',
        'revision_price',
        'last_sync_at', 'last_error',
    ];

    protected function casts(): array
    {
        return [
            'last_sync_at' => 'datetime',
        ];
    }

    public function setWpAppPasswordAttribute(?string $value): void
    {
        if ($value === null) { $this->attributes['wp_app_password'] = null; return; }
        $this->attributes['wp_app_password'] = Crypt::encryptString($value);
    }

    public function getWpAppPasswordAttribute(?string $value): ?string
    {
        if (!$value) return null;
        try {
            return Crypt::decryptString($value);
        } catch (\Exception) {
            return null;
        }
    }

    public function tenantToken(): BelongsTo
    {
        return $this->belongsTo(TenantToken::class);
    }

    public function webmaster(): BelongsTo
    {
        return $this->belongsTo(Webmaster::class);
    }

    public function unifiedUser(): BelongsTo
    {
        return $this->belongsTo(UnifiedUser::class, 'user_id');
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function pages(): HasMany
    {
        return $this->hasMany(SitePage::class, 'site_id');
    }
}
