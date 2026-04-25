<?php
/**
 * SEOLinkPlace PHP Client v1.1
 * https://seolinkplace.com
 *
 * Installation:
 * 1. Copy this file to a separate folder on your site (e.g. /includes/seolinkplace-client.php)
 * 2. Include it in your pages: require_once '/path/to/seolinkplace.php';
 * 3. Define your token before including: define('SEOLINKPLACE_TOKEN', 'your_token_here');
 * 4. Call return_links() or return_block_links() where you want links to appear
 *
 * Usage:
 *   define('SEOLINKPLACE_TOKEN', 'your_token_here');
 *   require_once 'seolinkplace.php';
 *   $sh = new SeoHands();
 *   echo $sh->return_links(3);           // plain links
 *   echo $sh->return_block_links(3);     // styled block (uses settings from dashboard)
 */

if (!defined('SEOLINKPLACE_TOKEN')) {
    return;
}

class SeoHands
{
    const VERSION       = '1.1.0';
    const API_HOST      = 'seolinkplace.com';
    const API_PATH      = '/api/v1/snippet/links';
    const PAGEVIEW_PATH = '/api/v1/snippet/pageview';
    const CACHE_TTL     = 3600;  // 1 hour
    const CACHE_RETRY   = 600;   // retry after 10 min on error

    private $_token;
    private $_url;
    private $_cache_dir;
    private $_links         = [];
    private $_block_settings = [];
    private $_errors        = [];
    private $_debug         = false;
    private $_fetch_type    = ''; // curl|file_get_contents|socket

    public function __construct(array $options = [])
    {
        $this->_token     = defined('SEOLINKPLACE_TOKEN') ? SEOLINKPLACE_TOKEN : '';
        $this->_debug     = isset($options['debug']) && $options['debug'];
        $this->_cache_dir = isset($options['cache_dir'])
            ? rtrim($options['cache_dir'], '/')
            : __DIR__ . DIRECTORY_SEPARATOR . '.' . md5(defined('SEOLINKPLACE_TOKEN') ? SEOLINKPLACE_TOKEN : 'slp');

        // Detect current URL
        $scheme         = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host           = $_SERVER['HTTP_HOST'] ?? '';
        $uri            = $_SERVER['REQUEST_URI'] ?? '/';
        $this->_url     = strtolower(trim($scheme . '://' . $host . $uri, '/'));

        // Create cache directory if it does not exist
        if (!is_dir($this->_cache_dir)) {
            @mkdir($this->_cache_dir, 0755, true);
        }

        $this->_load();
        $this->_ping_pageview();
    }

    /**
     * Return N plain links separated by newline
     */
    public function return_links(int $n = 5): string
    {
        if (empty($this->_links)) return '';

        $items = array_slice($this->_links, 0, $n);
        $html  = '';
        foreach ($items as $link) {
            $rel    = ($link['link_type'] ?? '') === 'nofollow' ? ' rel="nofollow"' : '';
            $url    = htmlspecialchars($link['target_url'] ?? '', ENT_QUOTES, 'UTF-8');
            $anchor = htmlspecialchars($link['anchor']     ?? $url, ENT_QUOTES, 'UTF-8');
            $before = htmlspecialchars($link['anchor_before'] ?? '', ENT_QUOTES, 'UTF-8');
            $after  = htmlspecialchars($link['anchor_after']  ?? '', ENT_QUOTES, 'UTF-8');

            $html .= ($before ? $before . ' ' : '')
                   . "<a href=\"{$url}\"{$rel} data-sh=\"{$link['id']}\">{$anchor}</a>"
                   . ($after  ? ' ' . $after  : '')
                   . "\n";
        }

        return $html . $this->_tracking_js();
    }

    /**
     * Return N links as a styled block using dashboard settings
     *
     * @param int   $n       Number of links to show
     * @param array $options Override any block_settings keys locally
     */
    public function return_block_links(int $n = 5, array $options = []): string
    {
        if (empty($this->_links)) return '';

        $items = array_slice($this->_links, 0, $n);

        // Merge: defaults < dashboard settings < local $options override
        $s = array_merge($this->_default_settings(), $this->_block_settings, $options);

        $p   = preg_replace('/[^a-zA-Z0-9_-]/', '', $s['css_prefix'] ?: 'slp_block');
        $bg  = $this->_hex($s['bg_color'],     '#ffffff');
        $bc  = $this->_hex($s['border_color'], '#dddddd');
        $bw  = max(0, (int)($s['border_width'] ?? 1));
        $br  = !empty($s['border_radius']) ? '8px' : '0';
        $hc  = $this->_hex($s['header_color'], '#000066');
        $hs  = max(8, (int)($s['header_size']  ?? 13));
        $hd  = ($s['header_decoration'] ?? 'underline') === 'underline' ? 'underline' : 'none';
        $tc  = $this->_hex($s['text_color'],   '#000000');
        $ts  = max(8, (int)($s['text_size']    ?? 11));
        $uc  = $this->_hex($s['url_color'],    '#006600');
        $us  = max(8, (int)($s['url_size']     ?? 11));
        $ta  = in_array($s['text_align'] ?? '', ['left','center','right']) ? $s['text_align'] : 'left';
        $w   = $this->_block_width($s['block_width'] ?? '');
        $vertical   = ($s['orientation'] ?? 'horizontal') === 'vertical';
        $show_header = !isset($s['show_header']) || $s['show_header'];
        $show_url    = !isset($s['show_url'])    || $s['show_url'];
        $sign        = htmlspecialchars($s['sign_text'] ?? '', ENT_QUOTES, 'UTF-8');

        // CSS
        $css = "<style>\n"
             . ".{$p}{font-family:" . ($s['font_family'] ?: 'Verdana,sans-serif') . ";font-size:11px;background:{$bg};"
             . "border:{$bw}px solid {$bc};border-radius:{$br};padding:8px;"
             . "display:block;box-sizing:border-box;width:{$w};text-align:{$ta};}\n"
             . ".{$p} *{box-sizing:border-box;}\n"
             . ".{$p}_table{width:100%;border-collapse:collapse;}\n"
             . ".{$p}_cell{padding:6px 8px;vertical-align:top;}\n"
             . ".{$p}_header,.{$p}_header a{color:{$hc};font-size:{$hs}px;font-weight:bold;"
             . "text-decoration:{$hd};display:block;margin-bottom:3px;}\n"
             . ".{$p}_text,.{$p}_text a{color:{$tc};font-size:{$ts}px;"
             . "text-decoration:none;display:block;margin-bottom:2px;}\n"
             . ".{$p}_url{color:{$uc};font-size:{$us}px;display:block;}\n"
             . ".{$p}_sign{color:#999;font-size:10px;display:block;margin-top:4px;}\n"
             . "</style>\n";

        // Build items HTML
        $cells = '';
        foreach ($items as $link) {
            $rel    = ($link['link_type'] ?? '') === 'nofollow' ? ' rel="nofollow"' : '';
            $url    = htmlspecialchars($link['target_url'] ?? '', ENT_QUOTES, 'UTF-8');
            $anchor = htmlspecialchars($link['anchor']     ?? $url, ENT_QUOTES, 'UTF-8');
            $domain = htmlspecialchars(parse_url($link['target_url'] ?? '', PHP_URL_HOST) ?: $url, ENT_QUOTES, 'UTF-8');

            $cell  = '';
            if ($show_header) {
                $cell .= "<span class=\"{$p}_header\"><a href=\"{$url}\"{$rel} data-sh=\"{$link['id']}\">{$anchor}</a></span>";
            }
            $cell .= "<span class=\"{$p}_text\">{$anchor}</span>";
            if ($show_url) {
                $cell .= "<span class=\"{$p}_url\">{$domain}</span>";
            }

            if ($vertical) {
                $cells .= "<div class=\"{$p}_cell\">{$cell}</div>\n";
            } else {
                $col_w = round(100 / count($items), 2);
                $cells .= "<td class=\"{$p}_cell\" style=\"width:{$col_w}%\">{$cell}</td>\n";
            }
        }

        // Wrap
        if ($vertical) {
            $inner = $cells;
        } else {
            $inner = "<table class=\"{$p}_table\"><tr>\n{$cells}</tr></table>\n";
        }

        if ($sign) {
            $inner .= "<span class=\"{$p}_sign\">{$sign}</span>\n";
        }

        $html = $css . "<div class=\"{$p}\">\n{$inner}</div>\n";

        return $html . $this->_tracking_js();
    }

    // -------------------------------------------------------------------------
    // Private
    // -------------------------------------------------------------------------

    private function _default_settings(): array
    {
        return [
            'display_mode'      => 'mixed',
            'delimiter'         => ' | ',
            'link_css_class'    => '',
            'orientation'       => 'horizontal',
            'show_header'       => true,
            'show_url'          => true,
            'sign_text'         => '',
            'block_width'       => '',
            'text_align'        => 'left',
            'header_color'      => '#000066',
            'header_size'       => 13,
            'header_decoration' => 'underline',
            'text_color'        => '#000000',
            'text_size'         => 11,
            'url_color'         => '#006600',
            'url_size'          => 11,
            'bg_color'          => '#ffffff',
            'border_color'      => '#dddddd',
            'border_width'      => 1,
            'border_radius'     => false,
            'css_prefix'        => 'slp_block',
            'font_family'       => 'Verdana,sans-serif',
        ];
    }

    private function _hex(string $val, string $default): string
    {
        return preg_match('/^#[0-9a-fA-F]{3,6}$/', $val) ? $val : $default;
    }

    private function _block_width(string $val): string
    {
        $val = trim($val);
        if ($val === '') return '100%';
        if (preg_match('/^\d+$/', $val)) return $val . 'px';
        if (preg_match('/^\d+(%|px|em|rem)$/', $val)) return $val;
        return '100%';
    }

    private function _cache_file(): string
    {
        return $this->_cache_dir . '/slp_' . md5($this->_token . $this->_url) . '.json';
    }

    private function _load(): void
    {
        $file = $this->_cache_file();

        $need_refresh = !file_exists($file)
            || (filemtime($file) < (time() - self::CACHE_TTL));

        if ($need_refresh) {
            // Prevent thundering herd — touch with short TTL
            @touch($file, time() - self::CACHE_TTL + self::CACHE_RETRY);

            $path = self::API_PATH
                . '?token=' . urlencode($this->_token)
                . '&url='   . urlencode($this->_url);

            $data = $this->_fetch(self::API_HOST, $path);

            if ($data) {
                $decoded = json_decode($data, true);
                if (isset($decoded['links'])) {
                    @file_put_contents($file, $data, LOCK_EX);
                } else {
                    $this->_error('Invalid API response');
                }
            } else {
                $this->_error('Failed to fetch links from API');
            }
        }

        // Read from cache
        if (file_exists($file)) {
            $cached = json_decode(@file_get_contents($file), true);
            $this->_links          = $cached['links']          ?? [];
            $this->_block_settings = $cached['block_settings'] ?? [];
        }
    }

    private function _ping_pageview(): void
    {
        $path = self::PAGEVIEW_PATH
            . '?token=' . urlencode($this->_token)
            . '&url='   . urlencode($this->_url);

        if (function_exists('curl_init')) {
            $ch = curl_init('https://' . self::API_HOST . $path);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 1,
                CURLOPT_CONNECTTIMEOUT => 1,
                CURLOPT_NOSIGNAL       => 1,
            ]);
            curl_exec($ch);
            curl_close($ch);
        }
    }

    private function _tracking_js(): string
    {
        $token = htmlspecialchars($this->_token, ENT_QUOTES, 'UTF-8');
        $base  = 'https://' . self::API_HOST . '/api/v1/track';
        return "<script>\n"
             . "(function(){\n"
             . "    var b='" . $base . "',t='" . $token . "';\n"
             . "    document.querySelectorAll('a[data-sh]').forEach(function(a){\n"
             . "        var id=a.getAttribute('data-sh');\n"
             . "        a.addEventListener('click',function(){\n"
             . "            try{new Image().src=b+'/click?token='+t+'&link_id='+id+'&page='+encodeURIComponent(location.href);}catch(e){}\n"
             . "        });\n"
             . "    });\n"
             . "})();\n"
             . "</script>\n";
    }

    private function _fetch(string $host, string $path): ?string
    {
        $url = 'https://' . $host . $path;

        if ($this->_fetch_type === '' || $this->_fetch_type === 'curl') {
            if (function_exists('curl_init')) {
                $this->_fetch_type = 'curl';
                $ch = curl_init($url);
                curl_setopt_array($ch, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_TIMEOUT        => 5,
                    CURLOPT_CONNECTTIMEOUT => 3,
                    CURLOPT_USERAGENT      => 'SEOLinkPlace-PHP/' . self::VERSION,
                    CURLOPT_SSL_VERIFYPEER => true,
                ]);
                $data = curl_exec($ch);
                $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                if ($data && $code === 200) return $data;
            }
        }

        if ($this->_fetch_type === '' || $this->_fetch_type === 'file_get_contents') {
            if (function_exists('file_get_contents') && ini_get('allow_url_fopen')) {
                $this->_fetch_type = 'file_get_contents';
                $ctx  = stream_context_create(['http' => [
                    'timeout'    => 5,
                    'user_agent' => 'SEOLinkPlace-PHP/' . self::VERSION,
                ]]);
                $data = @file_get_contents($url, false, $ctx);
                if ($data) return $data;
            }
        }

        // Socket fallback
        $this->_fetch_type = 'socket';
        $fp = @fsockopen('ssl://' . $host, 443, $errno, $errstr, 5);
        if ($fp) {
            $req = "GET {$path} HTTP/1.0\r\n"
                 . "Host: {$host}\r\n"
                 . "User-Agent: SeoHands-PHP/" . self::VERSION . "\r\n"
                 . "Connection: close\r\n\r\n";
            fwrite($fp, $req);
            $response = '';
            while (!feof($fp)) $response .= fread($fp, 4096);
            fclose($fp);
            $parts = explode("\r\n\r\n", $response, 2);
            return $parts[1] ?? null;
        }

        return null;
    }

    private function _error(string $msg): void
    {
        $this->_errors[] = $msg;
        if ($this->_debug) {
            error_log('[SeoHands] ' . $msg);
        }
    }
}
