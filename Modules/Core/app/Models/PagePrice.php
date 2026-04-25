<?php
namespace Modules\Core\Models;

use Modules\Core\Models\UnifiedUser;
use Modules\Sites\Models\Site;
use Modules\Sites\Models\SitePage;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PagePrice extends Model
{
    protected $fillable = [
        'site_id', 'site_page_id', 'price_type',
        'scope_type', 'scope_depth', 'scope_url', 'client_id',
        'base_price_per_day',
        'price_link_per_day', 'price_link_per_month',
        'price_onclick_per_day', 'price_onclick_per_month',
        'price_article_once', 'price_article_wm_once', 'price_article_per_day',
        'coef_link', 'coef_onclick', 'coef_article_once', 'coef_article_wm_once', 'coef_article_daily',
        'max_placements', 'is_public', 'adult_allowed',
    ];

    protected $casts = [
        'base_price_per_day'    => 'decimal:2',
        'price_link_per_day'    => 'decimal:6',
        'price_link_per_month'  => 'decimal:2',
        'price_onclick_per_day' => 'decimal:6',
        'price_onclick_per_month' => 'decimal:2',
        'price_article_once'     => 'decimal:2',
        'price_article_wm_once'  => 'decimal:2',
        'price_article_per_day'  => 'decimal:2',
        'coef_link'             => 'decimal:2',
        'coef_onclick'          => 'decimal:2',
        'coef_article_once'     => 'decimal:2',
        'coef_article_wm_once'  => 'decimal:2',
        'coef_article_daily'    => 'decimal:2',
        'is_public'             => 'boolean',
        'adult_allowed'         => 'boolean',
    ];

    // Scope type labels
    public const SCOPE_LABELS = [
        'site_default' => 'scope_site_default',
        'depth'        => 'scope_depth',
        'url'          => 'scope_url',
        'url_client'   => 'scope_url_client',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function wpPage(): BelongsTo
    {
        return $this->belongsTo(SitePage::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(UnifiedUser::class);
    }

    public function getPriceForType(string $type): ?float
    {
        return match($type) {
            'link'          => $this->price_link_per_day
                                ?? ($this->base_price_per_day ? (float)$this->base_price_per_day * (float)$this->coef_link : null),
            'onclick'       => $this->price_onclick_per_day
                                ?? ($this->base_price_per_day ? (float)$this->base_price_per_day * (float)$this->coef_onclick : null),
            'article_once'    => $this->price_article_once
                                  ?? ($this->base_price_per_day ? (float)$this->base_price_per_day * (float)$this->coef_article_once : null),
            'article_wm_once' => $this->price_article_wm_once
                                  ?? ($this->base_price_per_day ? (float)$this->base_price_per_day * (float)$this->coef_article_wm_once : null),
            'article_daily'   => $this->price_article_per_day
                                ?? ($this->base_price_per_day ? (float)$this->base_price_per_day * (float)$this->coef_article_daily : null),
            default         => null,
        };
    }

    public function getScopeLabel(): string
    {
        return match($this->scope_type) {
            'depth'      => __('client.scope_depth') . ' ' . $this->scope_depth,
            'url'        => $this->scope_url ?? '—',
            'url_client' => ($this->scope_url ?? '—') . ' [' . __('client.scope_url_client') . ']',
            default      => __('client.scope_site_default'),
        };
    }

    // Знаходить найкращу ціну для site+url+client за пріоритетом
    public static function resolveForUrl(int $siteId, string $url, ?int $clientId = null, ?string $priceType = null): ?self
    {
        $depth = self::calcDepth($url);

        $query = self::where('site_id', $siteId);

        if ($priceType) {
            $query->where('price_type', $priceType);
        }

        $candidates = $query->where(function($q) use ($url, $depth, $clientId) {
                $q->where('scope_type', 'site_default')
                  ->orWhere(fn($q2) => $q2->where('scope_type', 'depth')->where('scope_depth', $depth))
                  ->orWhere(fn($q2) => $q2->where('scope_type', 'url')->where('scope_url', $url))
                  ->orWhere(fn($q2) => $q2->where('scope_type', 'url_client')
                      ->where('scope_url', $url)
                      ->where('client_id', $clientId));
            })
            ->get();

        $priority = ['url_client' => 4, 'url' => 3, 'depth' => 2, 'site_default' => 1];

        return $candidates->sortByDesc(fn($p) => $priority[$p->scope_type] ?? 0)->first();
    }

    public static function calcDepth(string $url): int
    {
        $path = parse_url($url, PHP_URL_PATH) ?? '/';
        $parts = array_filter(explode('/', trim($path, '/')));
        return count($parts);
    }
}
