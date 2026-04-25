<?php
/**
 * SEOLinkPlace PHP Client v1.1 (Legacy — PHP 5.3+)
 * https://seolinkplace.com
 *
 * Installation:
 * 1. Copy this file to a separate folder on your site (e.g. /includes/seolinkplace-client.php)
 * 2. define('SEOLINKPLACE_TOKEN', 'your_token_here');
 * 3. require_once 'seolinkplace-client-legacy.php';
 * 4. $sh = new SeoHands();
 *    echo $sh->render(3);              // auto mode from dashboard settings
 *    echo $sh->return_links(3);        // plain links
 *    echo $sh->return_block_links(3);  // styled block
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
    const CACHE_TTL     = 3600;
    const CACHE_RETRY   = 600;

    var $_token;
    var $_url;
    var $_cache_dir;
    var $_links          = array();
    var $_block_settings = array();
    var $_errors         = array();
    var $_debug          = false;
    var $_fetch_type     = '';

    function __construct($options = array())
    {
        $this->_token     = defined('SEOLINKPLACE_TOKEN') ? SEOLINKPLACE_TOKEN : '';
        $this->_debug     = isset($options['debug']) && $options['debug'];
        $this->_cache_dir = isset($options['cache_dir'])
            ? rtrim($options['cache_dir'], '/')
            : __DIR__ . DIRECTORY_SEPARATOR . '.' . md5(defined('SEOLINKPLACE_TOKEN') ? SEOLINKPLACE_TOKEN : 'slp');

        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host   = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
        $uri    = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
        $this->_url = strtolower(trim($scheme . '://' . $host . $uri, '/'));

        // Create cache directory if it does not exist
        if (!is_dir($this->_cache_dir)) {
            @mkdir($this->_cache_dir, 0755, true);
        }

        $this->_load();
        $this->_ping_pageview();
    }

    /**
     * Render links according to display_mode from dashboard settings.
     * Recommended method.
     */
    function render($n = 5, $options = array())
    {
        $s    = array_merge($this->_default_settings(), $this->_block_settings, $options);
        $mode = isset($s['display_mode']) ? $s['display_mode'] : 'plain';
        if ($mode === 'block') {
            return $this->return_block_links($n, $options);
        }
        return $this->return_links($n);
    }

    /**
     * Return N plain links using delimiter from dashboard settings
     */
    function return_links($n = 5)
    {
        if (empty($this->_links)) return '';

        $s         = array_merge($this->_default_settings(), $this->_block_settings);
        $delimiter = isset($s['delimiter']) ? $s['delimiter'] : ' | ';
        $css_class = isset($s['link_css_class']) ? trim($s['link_css_class']) : '';
        $class_attr = $css_class ? ' class="' . htmlspecialchars($css_class, ENT_QUOTES, 'UTF-8') . '"' : '';

        $items = array_slice($this->_links, 0, $n);
        $parts = array();
        foreach ($items as $link) {
            $rel    = (isset($link['link_type']) && $link['link_type'] === 'nofollow') ? ' rel="nofollow"' : '';
            $url    = htmlspecialchars(isset($link['target_url']) ? $link['target_url'] : '', ENT_QUOTES, 'UTF-8');
            $anchor = htmlspecialchars(isset($link['anchor']) ? $link['anchor'] : $url, ENT_QUOTES, 'UTF-8');
            $before = htmlspecialchars(isset($link['anchor_before']) ? $link['anchor_before'] : '', ENT_QUOTES, 'UTF-8');
            $after  = htmlspecialchars(isset($link['anchor_after'])  ? $link['anchor_after']  : '', ENT_QUOTES, 'UTF-8');
            $id     = isset($link['id']) ? (int)$link['id'] : 0;

            $parts[] = ($before ? $before . ' ' : '')
                     . '<a href="' . $url . '"' . $rel . $class_attr . ' data-sh="' . $id . '">' . $anchor . '</a>'
                     . ($after ? ' ' . $after : '');
        }

        return implode($delimiter, $parts) . "\n" . $this->_tracking_js();
    }

    /**
     * Return N links as styled block using dashboard settings
     */
    function return_block_links($n = 5, $options = array())
    {
        if (empty($this->_links)) return '';

        $items = array_slice($this->_links, 0, $n);
        $s     = array_merge($this->_default_settings(), $this->_block_settings, $options);

        $p   = preg_replace('/[^a-zA-Z0-9_-]/', '', isset($s['css_prefix']) ? $s['css_prefix'] : 'slp_block');
        if (!$p) $p = 'slp_block';
        $bg  = $this->_hex(isset($s['bg_color'])     ? $s['bg_color']     : '', '#ffffff');
        $bc  = $this->_hex(isset($s['border_color']) ? $s['border_color'] : '', '#dddddd');
        $bw  = max(0, (int)(isset($s['border_width']) ? $s['border_width'] : 1));
        $br  = !empty($s['border_radius']) ? '8px' : '0';
        $hc  = $this->_hex(isset($s['header_color']) ? $s['header_color'] : '', '#000066');
        $hs  = max(8, (int)(isset($s['header_size'])  ? $s['header_size']  : 13));
        $hd  = (isset($s['header_decoration']) && $s['header_decoration'] === 'underline') ? 'underline' : 'none';
        $tc  = $this->_hex(isset($s['text_color'])   ? $s['text_color']   : '', '#000000');
        $ts  = max(8, (int)(isset($s['text_size'])    ? $s['text_size']    : 11));
        $uc  = $this->_hex(isset($s['url_color'])    ? $s['url_color']    : '', '#006600');
        $us  = max(8, (int)(isset($s['url_size'])     ? $s['url_size']     : 11));
        $ff  = isset($s['font_family']) && $s['font_family'] ? $s['font_family'] : 'Verdana,sans-serif';
        $ta  = in_array(isset($s['text_align']) ? $s['text_align'] : '', array('left','center','right')) ? $s['text_align'] : 'left';
        $w   = $this->_block_width(isset($s['block_width']) ? $s['block_width'] : '');
        $vertical    = (isset($s['orientation']) && $s['orientation'] === 'vertical');
        $show_header = !isset($s['show_header']) || $s['show_header'];
        $show_url    = !isset($s['show_url'])    || $s['show_url'];
        $sign        = htmlspecialchars(isset($s['sign_text']) ? $s['sign_text'] : '', ENT_QUOTES, 'UTF-8');

        $css = '<style>'
             . '.'. $p .'{font-family:'. $ff .';font-size:11px;background:'. $bg .';'
             . 'border:'. $bw .'px solid '. $bc .';border-radius:'. $br .';padding:8px;'
             . 'display:block;box-sizing:border-box;width:'. $w .';text-align:'. $ta .';}'
             . '.'. $p .' *{box-sizing:border-box;}'
             . '.'. $p .'_row{display:-webkit-box;display:-ms-flexbox;display:flex;-ms-flex-wrap:wrap;flex-wrap:wrap;}'
             . '.'. $p .'_cell{padding:6px 8px;vertical-align:top;-webkit-box-flex:1;-ms-flex:1;flex:1;cursor:pointer;}'
             . '.'. $p .'_header,.'. $p .'_header a{color:'. $hc .';font-size:'. $hs .'px;font-weight:bold;'
             . 'text-decoration:'. $hd .';display:block;margin-bottom:3px;}'
             . '.'. $p .'_text,.'. $p .'_text a{color:'. $tc .';font-size:'. $ts .'px;'
             . 'text-decoration:none;display:block;margin-bottom:2px;}'
             . '.'. $p .'_url{color:'. $uc .';font-size:'. $us .'px;display:block;}'
             . '.'. $p .'_sign{color:#999;font-size:10px;display:block;margin-top:4px;}'
             . '</style>';

        $cells = '';
        foreach ($items as $link) {
            $rel       = (isset($link['link_type']) && $link['link_type'] === 'nofollow') ? ' rel="nofollow"' : '';
            $url       = htmlspecialchars(isset($link['target_url']) ? $link['target_url'] : '', ENT_QUOTES, 'UTF-8');
            $anchor    = htmlspecialchars(isset($link['anchor'])     ? $link['anchor']     : $url, ENT_QUOTES, 'UTF-8');
            $before    = htmlspecialchars(isset($link['anchor_before']) ? $link['anchor_before'] : '', ENT_QUOTES, 'UTF-8');
            $after     = htmlspecialchars(isset($link['anchor_after'])  ? $link['anchor_after']  : '', ENT_QUOTES, 'UTF-8');
            $full_text = trim(($before ? $before . ' ' : '') . $anchor . ($after ? ' ' . $after : ''));
            $domain    = htmlspecialchars(parse_url(isset($link['target_url']) ? $link['target_url'] : '', PHP_URL_HOST) ?: $url, ENT_QUOTES, 'UTF-8');
            $id        = isset($link['id']) ? (int)$link['id'] : 0;

            $cell = '';
            if ($show_header) $cell .= '<span class="'. $p .'_header"><a href="'. $url .'" target="_blank"'. $rel .'>'. $anchor .'</a></span>';
            $cell .= '<span class="'. $p .'_text">'. $full_text .'</span>';
            if ($show_url)    $cell .= '<span class="'. $p .'_url">'. $domain .'</span>';

            $cells .= '<div class="'. $p .'_cell" onclick="window.open(\''. $url .'\',\'_blank\');return false;">'. $cell .'</div>' . "\n";
        }

        $inner = $vertical
            ? '<div>' . $cells . '</div>'
            : '<div class="'. $p .'_row">' . $cells . '</div>';

        if ($sign) $inner .= '<span class="'. $p .'_sign">'. $sign .'</span>' . "\n";

        return $css . '<div class="'. $p .'">' . "\n" . $inner . '</div>' . "\n"
             . $this->_tracking_js();
    }

    /**
     * Return N links as plain text URLs (no HTML)
     */
    function return_plain_links($n = 5, $delimiter = "\n")
    {
        if (empty($this->_links)) return '';

        $items = array_slice($this->_links, 0, $n);
        $parts = array();
        foreach ($items as $link) {
            if (!empty($link['target_url'])) {
                $parts[] = $link['target_url'];
            }
        }
        return implode($delimiter, array_filter($parts));
    }

    // -------------------------------------------------------------------------
    // Private
    // -------------------------------------------------------------------------

    function _default_settings()
    {
        return array(
            'display_mode'      => 'plain',
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
        );
    }

    function _hex($val, $default)
    {
        return preg_match('/^#[0-9a-fA-F]{3,6}$/', $val) ? $val : $default;
    }

    function _block_width($val)
    {
        $val = trim($val);
        if ($val === '') return '100%';
        if (preg_match('/^\d+$/', $val)) return $val . 'px';
        if (preg_match('/^\d+(%|px|em|rem)$/', $val)) return $val;
        return '100%';
    }

    function _cache_file()
    {
        return $this->_cache_dir . '/slp_' . md5($this->_token . $this->_url) . '.json';
    }

    function _load()
    {
        $file = $this->_cache_file();

        $need_refresh = !file_exists($file)
            || (filemtime($file) < (time() - self::CACHE_TTL));

        if ($need_refresh) {
            @touch($file, time() - self::CACHE_TTL + self::CACHE_RETRY);

            $path = self::API_PATH
                . '?token=' . urlencode($this->_token)
                . '&url='   . urlencode($this->_url);

            $data = $this->_fetch(self::API_HOST, $path);

            if ($data) {
                $decoded = json_decode($data, true);
                if (isset($decoded['links'])) {
                    @file_put_contents($file, $data);
                } else {
                    $this->_error('Invalid API response');
                }
            } else {
                $this->_error('Failed to fetch links from API');
            }
        }

        if (file_exists($file)) {
            $cached = json_decode(@file_get_contents($file), true);
            $this->_links          = isset($cached['links'])          ? $cached['links']          : array();
            $this->_block_settings = isset($cached['block_settings']) ? $cached['block_settings'] : array();
        }
    }

    function _ping_pageview()
    {
        $path = self::PAGEVIEW_PATH
            . '?token=' . urlencode($this->_token)
            . '&url='   . urlencode($this->_url);

        if (function_exists('curl_init')) {
            $ch = curl_init('https://' . self::API_HOST . $path);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
            curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
            curl_exec($ch);
            curl_close($ch);
        }
    }

    function _tracking_js()
    {
        $token = htmlspecialchars($this->_token, ENT_QUOTES, 'UTF-8');
        $base  = 'https://' . self::API_HOST . '/api/v1/track';
        return '<script>
(function(){
    var b="' . $base . '",t="' . $token . '";
    var links=document.querySelectorAll("a[data-sh]");
    for(var i=0;i<links.length;i++){
        (function(a){
            var id=a.getAttribute("data-sh");
            a.onclick=function(){
                try{new Image().src=b+"/click?token="+t+"&link_id="+id+"&page="+encodeURIComponent(location.href);}catch(e){}
            };
        })(links[i]);
    }
})();
</script>' . "\n";
    }

    function _fetch($host, $path)
    {
        $url = 'https://' . $host . $path;

        if (function_exists('curl_init')) {
            $this->_fetch_type = 'curl';
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
            curl_setopt($ch, CURLOPT_USERAGENT, 'SEOLinkPlace-PHP-Legacy/' . self::VERSION);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            $data = curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($data && $code === 200) return $data;
        }

        if (function_exists('file_get_contents') && ini_get('allow_url_fopen')) {
            $this->_fetch_type = 'file_get_contents';
            $ctx = stream_context_create(array('http' => array(
                'timeout'    => 5,
                'user_agent' => 'SEOLinkPlace-PHP-Legacy/' . self::VERSION,
            )));
            $data = @file_get_contents($url, false, $ctx);
            if ($data) return $data;
        }

        $this->_fetch_type = 'socket';
        $fp = @fsockopen('ssl://' . $host, 443, $errno, $errstr, 5);
        if ($fp) {
            $req = "GET " . $path . " HTTP/1.0\r\n"
                 . "Host: " . $host . "\r\n"
                 . "User-Agent: SEOLinkPlace-PHP-Legacy/" . self::VERSION . "\r\n"
                 . "Connection: close\r\n\r\n";
            fwrite($fp, $req);
            $response = '';
            while (!feof($fp)) $response .= fread($fp, 4096);
            fclose($fp);
            $parts = explode("\r\n\r\n", $response, 2);
            return isset($parts[1]) ? $parts[1] : null;
        }

        return null;
    }

    function _error($msg)
    {
        $this->_errors[] = $msg;
        if ($this->_debug) {
            error_log('[SEOLinkPlace] ' . $msg);
        }
    }
}
