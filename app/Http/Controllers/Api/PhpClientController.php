<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TenantToken;
use App\Models\CampaignLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;

class PhpClientController extends Controller
{
    private function resolveToken(Request $request): ?TenantToken
    {
        $token = $request->query('token');
        if (!$token) return null;

        $cacheKey = "token_valid:{$token}";
        $cached   = Cache::get($cacheKey);

        if (is_array($cached)) {
            $model = new TenantToken($cached);
            $model->exists = true;
            if (!empty($cached['site'])) {
                $model->setRelation('site', (new \App\Models\Site())->forceFill($cached['site']));
            }
            return $model;
        }

        $result = TenantToken::where('token', $token)
            ->where('status', 'active')
            ->with('site')
            ->first();

        if ($result) {
            Cache::put($cacheKey, $result->toArray(), 300);
        }

        return $result;
    }

    public function links(Request $request)
    {
        $tenantToken = $this->resolveToken($request);
        if (!$tenantToken) {
            return response()->json(['links' => [], 'error' => 'invalid_token'], 401)
                ->header('Access-Control-Allow-Origin', '*');
        }

        $url = trim(strtolower($request->query('url', '')), '/');

        $key = "php_client_links:{$tenantToken->token}:" . md5($url);
        $links = Cache::remember($key, 300, function () use ($tenantToken, $url) {
            $query = CampaignLink::where('site_id', $tenantToken->site_id)
                ->where('status', 'active')
                ->whereHas('campaign', fn($q) => $q->where('status', 'active'));

            if ($url) {
                // Посилання для конкретної сторінки
                $query->where('placement_type', 'link')
                    ->whereRaw("LOWER(TRIM(TRAILING '/' FROM donor_url)) = ?", [$url]);
            } else {
                // Посилання для всього сайту (homepage або загальний блок)
                $query->where('placement_type', 'link');
            }

            return $query->get(['id', 'target_url', 'anchor', 'anchor_before', 'anchor_after', 'link_type'])
                ->map(fn($l) => [
                    'id'            => $l->id,
                    'target_url'    => $l->target_url,
                    'anchor'        => $l->anchor,
                    'anchor_before' => $l->anchor_before ?? '',
                    'anchor_after'  => $l->anchor_after ?? '',
                    'link_type'     => $l->link_type,
                ]);
        });

        // Оновлюємо last_used_at асинхронно через queue
        $tokenId = $tenantToken->id;
        dispatch(function () use ($tokenId) {
            \App\Models\TenantToken::where('id', $tokenId)
                ->update(['last_used_at' => now()]);
        })->afterResponse();

        $settings = $tenantToken->site->link_block_settings ?? null;

        return response()->json([
            'links'          => $links,
            'version'        => '1.0',
            'block_settings' => $settings ? self::defaultSettings($settings) : self::defaultSettings([]),
        ])->header('Access-Control-Allow-Origin', '*')
          ->header('Cache-Control', 'no-store');
    }

    public function pageview(Request $request)
    {
        $token = $request->query('token');
        $url   = $request->query('url');

        if (!$token || !$url) {
            return response()->json(['ok' => false], 400)
                ->header('Access-Control-Allow-Origin', '*');
        }

        // Rate limit — 1 pageview per token+url per minute
        $key = 'pv:' . md5($token . $url);
        if (!RateLimiter::tooManyAttempts($key, 1)) {
            RateLimiter::hit($key, 60);

            $tenantToken = TenantToken::where('token', $token)
                ->where('status', 'active')
                ->first();

            if ($tenantToken) {
                \Illuminate\Support\Facades\Log::info("Pageview: site={$tenantToken->site_id} url={$url}");
                // TODO: зберігати в таблицю pageviews для статистики
            }
        }

        return response()->json(['ok' => true])
            ->header('Access-Control-Allow-Origin', '*');
    }

    public function blockSettings(Request $request)
    {
        $tenantToken = $this->resolveToken($request);
        if (!$tenantToken) {
            return response()->json(['error' => 'invalid_token'], 401)
                ->header('Access-Control-Allow-Origin', '*');
        }

        $settings = $tenantToken->site->link_block_settings ?? [];

        return response()->json([
            'settings' => self::defaultSettings($settings),
            'version'  => '1.0',
        ])->header('Access-Control-Allow-Origin', '*');
    }

    public static function defaultSettings(array $s): array
    {
        return [
            'display_mode'      => $s['display_mode']      ?? 'mixed',
            'delimiter'         => $s['delimiter']          ?? ' | ',
            'link_css_class'    => $s['link_css_class']     ?? '',
            'orientation'       => $s['orientation']        ?? 'horizontal',
            'show_header'       => $s['show_header']        ?? true,
            'show_url'          => $s['show_url']           ?? true,
            'sign_text'         => $s['sign_text']          ?? '',
            'block_width'       => $s['block_width']        ?? '',
            'text_align'        => $s['text_align']         ?? 'left',
            'header_color'      => $s['header_color']       ?? '#000066',
            'header_size'       => (int)($s['header_size']  ?? 13),
            'header_decoration' => $s['header_decoration']  ?? 'underline',
            'text_color'        => $s['text_color']         ?? '#000000',
            'text_size'         => (int)($s['text_size']    ?? 11),
            'url_color'         => $s['url_color']          ?? '#006600',
            'url_size'          => (int)($s['url_size']     ?? 11),
            'bg_color'          => $s['bg_color']           ?? '#ffffff',
            'border_color'      => $s['border_color']       ?? '#dddddd',
            'border_width'      => (int)($s['border_width'] ?? 1),
            'border_radius'     => $s['border_radius']      ?? false,
            'css_prefix'        => $s['css_prefix']         ?? 'slp_block',
            'font_family'       => $s['font_family']       ?? 'Verdana,sans-serif',
        ];
    }

    public function render(Request $request)
    {
        $tenantToken = $this->resolveToken($request);
        if (!$tenantToken) {
            return response('', 401)->header('Access-Control-Allow-Origin', '*');
        }

        $limit    = max(1, min(20, (int)$request->query('limit', 5)));
        $type     = $request->query('type', 'block'); // block|plain
        $site     = $tenantToken->site;

        $links = Cache::remember(
            "render_links:{$tenantToken->token}",
            300,
            function () use ($tenantToken) {
                return CampaignLink::where('site_id', $tenantToken->site_id)
                    ->where('status', 'active')
                    ->whereHas('campaign', fn($q) => $q->where('status', 'active'))
                    ->where('placement_type', 'link')
                    ->get(['id', 'target_url', 'anchor', 'link_type'])
                    ->toArray();
            }
        );

        $links = array_slice($links, 0, $limit);
        if (empty($links)) {
            return response('')->header('Access-Control-Allow-Origin', '*');
        }

        $s = self::defaultSettings($site->link_block_settings ?? []);

        if ($type === 'plain') {
            $html = implode(
                htmlspecialchars($s['delimiter'], ENT_QUOTES, 'UTF-8'),
                array_map(function ($l) use ($s) {
                    $rel    = ($l['link_type'] ?? '') === 'nofollow' ? ' rel="nofollow"' : '';
                    $url    = htmlspecialchars($l['target_url'] ?? '', ENT_QUOTES, 'UTF-8');
                    $anchor = htmlspecialchars($l['anchor']     ?? $url, ENT_QUOTES, 'UTF-8');
                    $cls    = $s['link_css_class'] ? " class=\"{$s['link_css_class']}\"" : '';
                    return "<a href=\"{$url}\"{$rel}{$cls}>{$anchor}</a>";
                }, $links)
            );
            return response($html)
                ->header('Content-Type', 'text/html; charset=utf-8')
                ->header('Access-Control-Allow-Origin', '*');
        }

        // Block render
        $p   = preg_replace('/[^a-zA-Z0-9_-]/', '', $s['css_prefix'] ?: 'slp_block');
        $bg  = $s['bg_color'];
        $bc  = $s['border_color'];
        $bw  = (int)$s['border_width'];
        $br  = $s['border_radius'] ? '8px' : '0';
        $hc  = $s['header_color'];
        $hs  = (int)$s['header_size'];
        $hd  = $s['header_decoration'] === 'underline' ? 'underline' : 'none';
        $tc  = $s['text_color'];
        $ts  = (int)$s['text_size'];
        $uc  = $s['url_color'];
        $us  = (int)$s['url_size'];
        $ta  = in_array($s['text_align'], ['left','center','right']) ? $s['text_align'] : 'left';
        $w   = $this->_block_width_render($s['block_width'] ?? '');
        $vertical    = ($s['orientation'] ?? 'horizontal') === 'vertical';
        $show_header = (bool)$s['show_header'];
        $show_url    = (bool)$s['show_url'];
        $sign        = htmlspecialchars($s['sign_text'] ?? '', ENT_QUOTES, 'UTF-8');

        $css = "<style>"
             . ".{$p}{font-family:{$s['font_family']};font-size:11px;background:{$bg};"
             . "border:{$bw}px solid {$bc};border-radius:{$br};padding:8px;"
             . "display:block;box-sizing:border-box;width:{$w};text-align:{$ta};}"
             . ".{$p} *{box-sizing:border-box;}"
             . ".{$p}_row{display:flex;flex-wrap:wrap;}"
             . ".{$p}_cell{padding:6px 8px;vertical-align:top;flex:1;cursor:pointer;}"
             . ".{$p}_header,.{$p}_header a{color:{$hc};font-size:{$hs}px;font-weight:bold;"
             . "text-decoration:{$hd};display:block;margin-bottom:3px;}"
             . ".{$p}_text,.{$p}_text a{color:{$tc};font-size:{$ts}px;"
             . "text-decoration:none;display:block;margin-bottom:2px;}"
             . ".{$p}_url{color:{$uc};font-size:{$us}px;display:block;}"
             . ".{$p}_sign{color:#999;font-size:10px;display:block;margin-top:4px;}"
             . "</style>";

        $cells = '';
        $col_w = count($links) > 0 ? round(100 / count($links), 2) : 100;
        foreach ($links as $link) {
            $rel    = ($link['link_type'] ?? '') === 'nofollow' ? ' rel="nofollow"' : '';
            $url    = htmlspecialchars($link['target_url'] ?? '', ENT_QUOTES, 'UTF-8');
            $anchor = htmlspecialchars($link['anchor']     ?? $url, ENT_QUOTES, 'UTF-8');
            $before = htmlspecialchars($link['anchor_before'] ?? '', ENT_QUOTES, 'UTF-8');
            $after  = htmlspecialchars($link['anchor_after']  ?? '', ENT_QUOTES, 'UTF-8');
            $domain = htmlspecialchars(parse_url($link['target_url'] ?? '', PHP_URL_HOST) ?: $url, ENT_QUOTES, 'UTF-8');
            $full_text = trim(($before ? $before . ' ' : '') . $anchor . ($after ? ' ' . $after : ''));

            $cell = '';
            if ($show_header) $cell .= "<span class=\"{$p}_header\"><a href=\"{$url}\" target=\"_blank\"{$rel}>{$anchor}</a></span>";
            $cell .= "<span class=\"{$p}_text\">{$full_text}</span>";
            if ($show_url)    $cell .= "<span class=\"{$p}_url\">{$domain}</span>";

            $cells .= "<div class=\"{$p}_cell\" onclick=\"window.open('{$url}','_blank');return false;\">{$cell}</div>";
        }

        $inner = $vertical
            ? "<div>{$cells}</div>"
            : "<div class=\"{$p}_row\">{$cells}</div>";

        if ($sign) $inner .= "<span class=\"{$p}_sign\">{$sign}</span>";

        $html = $css . "<div class=\"{$p}\">{$inner}</div>";

        return response($html)
            ->header('Content-Type', 'text/html; charset=utf-8')
            ->header('Access-Control-Allow-Origin', '*');
    }

    private function _block_width_render(string $val): string
    {
        $val = trim($val);
        if ($val === '') return '100%';
        if (preg_match('/^\d+$/', $val)) return $val . 'px';
        if (preg_match('/^\d+(%|px|em|rem)$/', $val)) return $val;
        return '100%';
    }

}
