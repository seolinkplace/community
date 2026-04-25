<?php
/**
 * Plugin Name: seolinkplace
 * Plugin URI: https://seolinkplace.com
 * Description: seolinkplace.com integration — SEO links, articles, click tracking
 * Version: 2.2.0
 * Author: seolinkplace.com
 * Text Domain: seolinkplace
 */

if (!defined('ABSPATH')) exit;

define('SEOLINKPLACE_API', 'https://seolinkplace.com/api/v1');
define('SEOLINKPLACE_VERSION', '2.2.0');

// ─── Helpers ─────────────────────────────────────────────────────────────────

function seolinkplace_token(): string
{
    return get_option('seolinkplace_token', '');
}

function seolinkplace_api(string $method, string $endpoint, array $data = []): ?array
{
    $token = seolinkplace_token();
    if (!$token) return null;

    $url  = SEOLINKPLACE_API . $endpoint;
    $args = [
        'method'  => strtoupper($method),
        'timeout' => 10,
        'headers' => [
            'X-Seohands-Token' => $token,
            'Content-Type'     => 'application/json',
            'Accept'           => 'application/json',
        ],
        'sslverify' => false,
    ];

    if (!empty($data) && in_array($args['method'], ['POST', 'PUT', 'PATCH'])) {
        $args['body'] = json_encode($data);
    } elseif (!empty($data)) {
        $url = add_query_arg($data, $url);
    }

    $response = wp_remote_request($url, $args);
    if (is_wp_error($response)) {
        error_log('[seolinkplace] API error: ' . $response->get_error_message());
        return null;
    }
    return json_decode(wp_remote_retrieve_body($response), true);
}

// ─── REST endpoint: flush cache для конкретного URL ──────────────────────────

add_action('rest_api_init', function () {
    register_rest_route('seolinkplace/v1', '/flush', [
        'methods'             => 'POST',
        'callback'            => 'seolinkplace_flush_handler',
        'permission_callback' => 'seolinkplace_flush_permission',
    ]);
});

function seolinkplace_flush_permission(WP_REST_Request $request): bool
{
    // Перевіряємо підпис запиту від seolinkplace
    $token = get_option('seolinkplace_token', '');
    if (!$token) return false;
    $sig = $request->get_header('X-Seohands-Signature');
    $body = $request->get_body();
    $expected = hash_hmac('sha256', $body, $token);
    return hash_equals($expected, (string)$sig);
}

function seolinkplace_flush_handler(WP_REST_Request $request): WP_REST_Response
{
    $url = sanitize_url($request->get_param('url'));
    if (!$url) {
        return new WP_REST_Response(['ok' => false, 'error' => 'no url'], 400);
    }

    // Чистимо transient
    $cache_key = 'seolinkplace_links_' . md5($url);
    delete_transient($cache_key);

    // Чистимо page cache — підтримка популярних плагінів
    seolinkplace_clear_page_cache($url);

    return new WP_REST_Response(['ok' => true, 'url' => $url]);
}

function seolinkplace_clear_page_cache(string $url): void
{
    // WP Super Cache
    if (function_exists('wpsc_delete_url_cache')) {
        wpsc_delete_url_cache($url);
    }
    // W3 Total Cache
    if (function_exists('w3tc_flush_url')) {
        w3tc_flush_url($url);
    }
    // WP Rocket
    if (function_exists('rocket_clean_post')) {
        $post_id = url_to_postid($url);
        if ($post_id) rocket_clean_post($post_id);
    }
    // LiteSpeed Cache
    if (class_exists('LiteSpeed_Cache_API')) {
        LiteSpeed_Cache_API::purge($url);
    }
    // Autoptimize
    if (class_exists('autoptimizeCache')) {
        autoptimizeCache::clearall();
    }
    // Nginx Helper
    if (function_exists('rt_nginx_helper_purge_url')) {
        rt_nginx_helper_purge_url($url);
    }
}


// ─── REST endpoint: homepage info ────────────────────────────────────────────

add_action('rest_api_init', function () {
    register_rest_route('seolinkplace/v1', '/homepage', [
        'methods'             => 'GET',
        'callback'            => 'seolinkplace_homepage_handler',
        'permission_callback' => 'seolinkplace_flush_permission',
    ]);
});

function seolinkplace_homepage_handler(WP_REST_Request $request): WP_REST_Response
{
    $url = home_url('/');
    return new WP_REST_Response([
        'ok'  => true,
        'page' => [
            'url'          => $url,
            'title'        => get_bloginfo('name'),
            'wp_post_id'   => 0,
            'post_type'    => 'homepage',
            'status'       => 'publish',
            'published_at' => get_option('bloginfo') ?: date('c'),
        ],
    ]);
}

// ─── Кнопка "Налаштування" в списку плагінів ─────────────────────────────────

add_filter('plugin_action_links_' . plugin_basename(__FILE__), function (array $links): array {
    array_unshift($links, '<a href="' . admin_url('options-general.php?page=seolinkplace') . '">Налаштування</a>');
    return $links;
});

// ─── Меню налаштувань ─────────────────────────────────────────────────────────

add_action('admin_menu', function () {
    add_options_page('seolinkplace', 'seolinkplace', 'manage_options', 'seolinkplace', 'seolinkplace_settings_page');
});

function seolinkplace_settings_page(): void
{
    if (!current_user_can('manage_options')) return;

    if (isset($_POST['seolinkplace_save'])) {
        check_admin_referer('seolinkplace_settings');
        update_option('seolinkplace_token',           sanitize_text_field($_POST['seolinkplace_token']));
        update_option('seolinkplace_link_position',   sanitize_text_field($_POST['seolinkplace_link_position']));
        update_option('seolinkplace_tracker_enabled', isset($_POST['seolinkplace_tracker_enabled']) ? '1' : '0');
        update_option('seolinkplace_cache_ttl',       (int)($_POST['seolinkplace_cache_ttl'] ?? 300));
        update_option('seolinkplace_author_id',       (int)($_POST['seolinkplace_author_id'] ?? get_current_user_id()));

        $result = seolinkplace_api('POST', '/wp/connect', [
            'wp_url'     => get_site_url(),
            'wp_version' => get_bloginfo('version'),
        ]);

        if ($result && !empty($result['ok'])) {
            echo '<div class="notice notice-success"><p>Підключено до seolinkplace.com!</p></div>';
        } else {
            echo '<div class="notice notice-warning"><p>Збережено, але підключення не вдалось. Перевірте токен.</p></div>';
        }
    }

    $token           = seolinkplace_token();
    $position        = get_option('seolinkplace_link_position', 'footer');
    $tracker_enabled = get_option('seolinkplace_tracker_enabled', '1');
    $cache_ttl       = get_option('seolinkplace_cache_ttl', 300);
    ?>
    <div class="wrap">
        <h1>seolinkplace налаштування</h1>
        <form method="POST">
            <?php wp_nonce_field('seolinkplace_settings'); ?>
            <table class="form-table">
                <tr>
                    <th>Tenant Token</th>
                    <td>
                        <input type="text" name="seolinkplace_token" value="<?php echo esc_attr($token); ?>" class="regular-text" required>
                        <p class="description">Отримайте токен в кабінеті seolinkplace.com → Токени</p>
                    </td>
                </tr>
                <tr>
                    <th>Позиція посилань</th>
                    <td>
                        <select name="seolinkplace_link_position">
                            <option value="footer" <?php selected($position, 'footer'); ?>>Футер статті</option>
                            <option value="header" <?php selected($position, 'header'); ?>>Початок статті</option>
                            <option value="random" <?php selected($position, 'random'); ?>>Випадкова позиція</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>Автор статей</th>
                    <td>
                        <select name="seolinkplace_author_id">
                            <?php foreach (get_users(['role__in' => ['administrator','editor','author']]) as $u): ?>
                            <option value="<?php echo $u->ID; ?>" <?php selected(get_option('seolinkplace_author_id', get_current_user_id()), $u->ID); ?>>
                                <?php echo esc_html($u->display_name . ' (' . $u->user_login . ')'); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description">Від якого користувача публікувати статті</p>
                    </td>
                </tr>
                <tr>
                    <th>Кеш посилань (секунди)</th>
                    <td>
                        <input type="number" name="seolinkplace_cache_ttl" value="<?php echo (int)$cache_ttl; ?>" min="60" max="3600" class="small-text">
                        <p class="description">Як часто оновлювати дані з seolinkplace.com (60-3600 сек)</p>
                    </td>
                </tr>
                <tr>
                    <th>JS Click Tracker</th>
                    <td>
                        <label>
                            <input type="checkbox" name="seolinkplace_tracker_enabled" value="1" <?php checked($tracker_enabled, '1'); ?>>
                            Відстежувати кліки по зовнішніх посиланнях сайту
                        </label>
                        <p class="description">Не трекаються статті опубліковані через seolinkplace.</p>
                    </td>
                </tr>
            </table>
            <?php submit_button('Зберегти та підключити', 'primary', 'seolinkplace_save'); ?>
        </form>

        <?php if ($token): ?>
        <h2>Статистика</h2>
        <div id="seolinkplace-stats"><span class="spinner is-active" style="float:none"></span> Завантаження...</div>
        <p><a href="https://seolinkplace.com/wm/orders" target="_blank">Детальна статистика на seolinkplace.com →</a></p>
        <script>
        jQuery(function($){
            $.post(ajaxurl, {action:'seolinkplace_get_stats', nonce:'<?php echo wp_create_nonce("seolinkplace_stats"); ?>'}, function(r){
                if(r.success && r.data){
                    var d = r.data;
                    $('#seolinkplace-stats').html(
                        '<table class="widefat" style="max-width:500px">' +
                        '<tr><td>Розміщено замовлень</td><td><strong>'+d.orders_placed+'</strong></td></tr>' +
                        '<tr><td>Кліків сьогодні</td><td><strong>'+d.clicks_today+'</strong></td></tr>' +
                        '<tr><td>Кліків за тиждень</td><td><strong>'+d.clicks_week+'</strong></td></tr>' +
                        '<tr><td>Сторінок синхронізовано</td><td><strong>'+d.pages_count+'</strong></td></tr>' +
                        '<tr><td>Остання синхронізація</td><td><strong>'+d.last_sync_at+'</strong></td></tr>' +
                        '</table>'
                    );
                } else {
                    $('#seolinkplace-stats').html('<p>Не вдалось завантажити статистику.</p>');
                }
            });
        });
        </script>

        <h2>Синхронізація сторінок</h2>
        <div id="seolinkplace-sync-status"></div>
        <button id="seolinkplace-sync-btn" class="button button-secondary">Синхронізувати сторінки зараз</button>
        <script>
        jQuery(function($){
            $('#seolinkplace-sync-btn').on('click', function(){
                var btn = $(this);
                btn.prop('disabled', true).text('Синхронізація...');
                $('#seolinkplace-sync-status').html('<span class="spinner is-active" style="float:none"></span>');

                function syncBatch(offset){
                    $.post(ajaxurl, {
                        action: 'seolinkplace_sync_batch',
                        nonce:  '<?php echo wp_create_nonce("seolinkplace_sync"); ?>',
                        offset: offset
                    }, function(r){
                        if(r.success){
                            if(r.data.done){
                                $('#seolinkplace-sync-status').html('<div class="notice notice-success inline"><p>Синхронізовано '+r.data.total+' сторінок!</p></div>');
                                btn.prop('disabled', false).text('Синхронізувати сторінки зараз');
                            } else {
                                $('#seolinkplace-sync-status').html('<span class="spinner is-active" style="float:none"></span> Синхронізовано: '+r.data.synced+'...');
                                syncBatch(r.data.next_offset);
                            }
                        } else {
                            $('#seolinkplace-sync-status').html('<div class="notice notice-error inline"><p>Помилка синхронізації.</p></div>');
                            btn.prop('disabled', false).text('Синхронізувати сторінки зараз');
                        }
                    });
                }
                syncBatch(0);
            });
        });
        </script>
        <?php endif; ?>
    </div>
    <?php
}


// ─── Render block HTML from links + settings ─────────────────────────────────
function seolinkplace_render_block(array $links, array $s): string
{
    $p   = preg_replace('/[^a-zA-Z0-9_-]/', '', $s['css_prefix'] ?? 'slp_block') ?: 'slp_block';
    $bg  = $s['bg_color']     ?? '#ffffff';
    $bc  = $s['border_color'] ?? '#dddddd';
    $bw  = (int)($s['border_width'] ?? 1);
    $br  = !empty($s['border_radius']) ? '8px' : '0';
    $hc  = $s['header_color'] ?? '#000066';
    $hs  = (int)($s['header_size'] ?? 13);
    $hd  = ($s['header_decoration'] ?? 'underline') === 'underline' ? 'underline' : 'none';
    $tc  = $s['text_color']   ?? '#000000';
    $ts  = (int)($s['text_size'] ?? 11);
    $uc  = $s['url_color']    ?? '#006600';
    $us  = (int)($s['url_size'] ?? 11);
    $ff  = $s['font_family']  ?? 'Verdana,sans-serif';
    $ta  = in_array($s['text_align'] ?? '', ['left','center','right']) ? $s['text_align'] : 'left';
    $w   = trim($s['block_width'] ?? '') ?: '100%';
    if (preg_match('/^\d+$/', $w)) $w .= 'px';
    $vertical    = ($s['orientation'] ?? 'horizontal') === 'vertical';
    $show_header = (bool)($s['show_header'] ?? true);
    $show_url    = (bool)($s['show_url']    ?? true);
    $sign        = esc_html($s['sign_text'] ?? '');

    $css = "<style>"
         . ".{$p}{font-family:{$ff};font-size:11px;background:{$bg};"
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

    $cells   = '';
    $col_w   = count($links) > 0 ? round(100 / count($links), 2) : 100;
    foreach ($links as $link) {
        $rel       = ($link['link_type'] ?? '') === 'nofollow' ? ' rel="nofollow"' : '';
        $url       = esc_url($link['target_url'] ?? '');
        $anchor    = esc_html($link['anchor'] ?? $url);
        $before    = !empty($link['anchor_before']) ? esc_html($link['anchor_before']) . ' ' : '';
        $after     = !empty($link['anchor_after'])  ? ' ' . esc_html($link['anchor_after']) : '';
        $full_text = trim($before . $anchor . $after);
        $domain    = esc_html(parse_url($link['target_url'] ?? '', PHP_URL_HOST) ?: $url);

        $cell = '';
        if ($show_header) $cell .= "<span class=\"{$p}_header\">{$anchor}</span>";
        $cell .= "<span class=\"{$p}_text\">{$full_text}</span>";
        if ($show_url)    $cell .= "<span class=\"{$p}_url\">{$domain}</span>";

        $cells .= "<div class=\"{$p}_cell\" onclick=\"window.open('{$url}','_blank');return false;\">{$cell}</div>";
    }

    $inner = $vertical
        ? "<div>{$cells}</div>"
        : "<div class=\"{$p}_row\">{$cells}</div>";

    if ($sign) $inner .= "<span class=\"{$p}_sign\">{$sign}</span>";

    return $css . "<div class=\"{$p}\">{$inner}</div>";
}

// ─── SEO Посилання: тягнемо з API при показі сторінки ────────────────────────

add_filter('the_content', function(string $content): string {
    if (!is_single() && !is_page()) return $content;
    if (!seolinkplace_token()) return $content;

    $page_url = get_permalink();
    if (!$page_url) return $content;

    $cache_ttl = (int)get_option('seolinkplace_cache_ttl', 300);
    $cache_key = 'seolinkplace_render_' . md5($page_url);

    $html = get_transient($cache_key);
    if ($html === false) {
        $result = seolinkplace_api('GET', '/wp/page-links', ['url' => $page_url]);
        $links  = $result['links']          ?? [];
        $settings = $result['block_settings'] ?? [];
        $mode   = $settings['display_mode'] ?? 'plain';

        if (empty($links)) {
            set_transient($cache_key, '', $cache_ttl);
            return $content;
        }

        if ($mode === 'block') {
            $html = seolinkplace_render_block($links, $settings);
        } else {
            // plain — прості посилання
            $delimiter = $settings['delimiter'] ?? ' | ';
            $parts = [];
            foreach ($links as $link) {
                $rel       = ($link['link_type'] ?? '') === 'nofollow' ? ' rel="nofollow"' : '';
                $anchor    = esc_html($link['anchor'] ?? $link['target_url']);
                $url       = esc_url($link['target_url']);
                $before    = !empty($link['anchor_before']) ? esc_html($link['anchor_before']) . ' ' : '';
                $after     = !empty($link['anchor_after'])  ? ' ' . esc_html($link['anchor_after']) : '';
                $parts[]   = $before . '<a href="' . $url . '"' . $rel . '>' . $anchor . '</a>' . $after;
            }
            $html = '<p>' . implode($delimiter, $parts) . '</p>';
        }

        set_transient($cache_key, $html, $cache_ttl);
    }

    if (empty($html)) return $content;

    $position = get_option('seolinkplace_link_position', 'footer');
    if ($position === 'random') $position = rand(0, 1) ? 'header' : 'footer';
    return $position === 'header' ? $html . $content : $content . $html;
}, 10);


// ─── SEO Посилання на головній сторінці ──────────────────────────────────────

add_action('wp_footer', function(): void {
    if (!is_front_page() && !is_home()) return;
    if (!seolinkplace_token()) return;

    $page_url  = home_url('/');
    $cache_ttl = (int)get_option('seolinkplace_cache_ttl', 300);
    $cache_key = 'seolinkplace_render_' . md5($page_url);

    $html = get_transient($cache_key);
    if ($html === false) {
        $result   = seolinkplace_api('GET', '/wp/page-links', ['url' => $page_url]);
        $links    = $result['links']          ?? [];
        $settings = $result['block_settings'] ?? [];
        $mode     = $settings['display_mode'] ?? 'plain';

        if (empty($links)) {
            set_transient($cache_key, '', $cache_ttl);
            return;
        }

        if ($mode === 'block') {
            $render_url = SEOLINKPLACE_API . '/snippet/render?token=' . urlencode(seolinkplace_token()) . '&limit=' . count($links) . '&type=block';
            $r = wp_remote_get($render_url, ['timeout' => 5, 'sslverify' => false]);
            $html = is_wp_error($r) ? '' : wp_remote_retrieve_body($r);
        } else {
            // plain — прості видимі посилання
            $delimiter = $settings['delimiter'] ?? ' | ';
            $parts = [];
            foreach ($links as $link) {
                $rel    = ($link['link_type'] ?? '') === 'nofollow' ? ' rel="nofollow"' : '';
                $anchor = esc_html($link['anchor'] ?? $link['target_url']);
                $url    = esc_url($link['target_url']);
                $parts[] = '<a href="' . $url . '"' . $rel . '>' . $anchor . '</a>';
            }
            $html = '<p>' . implode($delimiter, $parts) . '</p>';
        }

        set_transient($cache_key, $html, $cache_ttl);
    }

    if (!empty($html)) echo $html;
}, 98);

// ─── JS Click Tracker ─────────────────────────────────────────────────────────

add_action('wp_footer', function (): void {
    if (get_option('seolinkplace_tracker_enabled') !== '1') return;
    $token = seolinkplace_token();
    if (!$token) return;
    if (is_single() || is_page()) {
        global $post;
        if ($post && get_post_meta($post->ID, '_seolinkplace_article_id', true)) return;
    }
    ?>
    <script>
    (function(){
        var token='<?php echo esc_js($token); ?>';
        var base='https://seolinkplace.com/api/v1/track/anchor-click';
        function track(href, text){
            try {
                var img = new Image();
                img.src = base + '?token=' + encodeURIComponent(token)
                    + '&href=' + encodeURIComponent(href)
                    + '&text=' + encodeURIComponent((text||'').substring(0, 100))
                    + '&page=' + encodeURIComponent(location.href);
            } catch(e) {}
        }
        document.addEventListener('DOMContentLoaded', function(){
            document.querySelectorAll('a[href]').forEach(function(a){
                var href = a.getAttribute('href') || '';
                if (href.indexOf('http') !== 0) return;
                if (href.indexOf(location.hostname) !== -1) return;
                a.addEventListener('click', function(){ track(href, a.textContent); });
            });
        });
    })();
    </script>
    <?php
}, 99);


// ─── Onclick Rules ────────────────────────────────────────────────────────────
add_action('wp_footer', function (): void {
    $token = seolinkplace_token();
    if (!$token) return;

    $cache_key = 'seolinkplace_onclick_' . md5($token . $_SERVER['REQUEST_URI']);
    $rules = get_transient($cache_key);

    if ($rules === false) {
        $response = seolinkplace_api('GET', '/wp/onclick-rules');
        $rules = $response['rules'] ?? [];
        set_transient($cache_key, $rules, 300); // кеш 5 хв
    }

    if (empty($rules)) return;
    ?>
    <script>
    (function(){
        var rules = <?php echo json_encode($rules); ?>;
        if (!rules || !rules.length) return;

        function applyOnclick(rule) {
            document.querySelectorAll('a[href]').forEach(function(a) {
                var href = a.getAttribute('href') || '';
                var text = (a.textContent || '').trim();

                var matchHref   = rule.href_match   && href.indexOf(rule.href_match) !== -1;
                var matchAnchor = rule.anchor_match  && text.toLowerCase().indexOf(rule.anchor_match.toLowerCase()) !== -1;

                if (!matchHref && !matchAnchor) return;
                if (a.dataset.seolinkplaceOnclick) return; // не вішати двічі

                a.dataset.seolinkplaceOnclick = '1';
                a.addEventListener('click', function(e) {
                    try {
                        window.open(rule.target_url, '_blank', 'noopener,noreferrer');
                    } catch(err) {}
                    // оригінальний клік не блокуємо
                });
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            rules.forEach(function(rule) { applyOnclick(rule); });
        });
    })();
    </script>
    <?php
}, 100);


// ─── Article Deleted Hook ─────────────────────────────────────────────────────
add_action('before_delete_post', function (int $postId): void {
    $articleId = get_post_meta($postId, '_seolinkplace_article_id', true);
    if (!$articleId) return;

    $token = seolinkplace_token();
    if (!$token) return;

    seolinkplace_api('POST', '/wp/article-deleted', [
        'wp_post_id' => $postId,
    ]);
}, 10);


// ─── Синхронізація сторінок (крон) ───────────────────────────────────────────

add_filter('cron_schedules', function(array $schedules): array {
    $schedules['seolinkplace_6h'] = ['interval' => 21600, 'display' => 'Every 6 hours'];
    return $schedules;
});

add_action('seolinkplace_sync', function(): void {
    $offset   = 0;
    $per_page = 50; // невеликими порціями щоб не перевищити memory limit

    // Синхронізуємо головну сторінку
    seolinkplace_api('POST', '/wp/pages', ['pages' => [[
        'url'          => home_url('/'),
        'title'        => get_bloginfo('name'),
        'wp_post_id'   => 0,
        'post_type'    => 'homepage',
        'status'       => 'publish',
        'published_at' => date('c'),
    ]]]);

    do {
        $query = new WP_Query([
            'post_type'      => ['post', 'page'],
            'post_status'    => 'publish',
            'posts_per_page' => $per_page,
            'offset'         => $offset,
            'no_found_rows'  => false,
            'fields'         => 'all',
        ]);
        if (!$query->have_posts()) break;
        $pages = [];
        foreach ($query->posts as $p) {
            $pages[] = [
                'url'          => get_permalink($p->ID),
                'title'        => $p->post_title,
                'wp_post_id'   => $p->ID,
                'post_type'    => $p->post_type,
                'status'       => $p->post_status,
                'published_at' => get_post_time('c', true, $p->ID),
            ];
        }
        seolinkplace_api('POST', '/wp/pages', ['pages' => $pages]);
        $offset += $per_page;
        wp_reset_postdata();
    } while ($query->found_posts > $offset);
});

if (!wp_next_scheduled('seolinkplace_sync')) {
    wp_schedule_event(time(), 'seolinkplace_6h', 'seolinkplace_sync');
}

// ─── Shortcode ────────────────────────────────────────────────────────────────

add_shortcode('seolinkplace', function(array $atts): string {
    $atts = shortcode_atts(['token' => seolinkplace_token(), 'limit' => 5], $atts);
    $url  = SEOLINKPLACE_API . '/snippet/render?token=' . urlencode($atts['token']) . '&limit=' . (int)$atts['limit'];
    $r    = wp_remote_get($url, ['timeout' => 3, 'sslverify' => false]);
    return is_wp_error($r) ? '' : wp_remote_retrieve_body($r);
});

// ─── Публікація статей (крон) ────────────────────────────────────────────────

add_filter('cron_schedules', function(array $schedules): array {
    $schedules['seolinkplace_5m'] = ['interval' => 300, 'display' => 'Every 5 minutes'];
    return $schedules;
}, 20);

add_action('seolinkplace_publish_articles', function(): void {
    if (!seolinkplace_token()) return;

    $result = seolinkplace_api('GET', '/wp/articles');
    if (empty($result['articles'])) return;

    $reports = [];
    foreach ($result['articles'] as $article) {
        // Перевіряємо чи не опублікована вже
        $existing = get_posts([
            'meta_key'    => '_seolinkplace_article_id',
            'meta_value'  => $article['seolinkplace_id'],
            'post_status' => 'any',
            'numberposts' => 1,
        ]);

        // Якщо wp_post_id передано — оновлюємо напряму
        if (!empty($article['wp_post_id'])) {
            wp_update_post([
                'ID'           => (int)$article['wp_post_id'],
                'post_title'   => wp_strip_all_tags($article['title']),
                'post_content' => $article['content'],
            ]);
            $reports[] = [
                'seolinkplace_id'   => $article['seolinkplace_id'],
                'wp_post_id'    => (int)$article['wp_post_id'],
                'published_url' => get_permalink((int)$article['wp_post_id']),
                'status'        => 'publish',
            ];
            continue;
        }

        // Якщо вже є по meta — оновлюємо контент
        if (!empty($existing)) {
            $existing_post = $existing[0];
            wp_update_post([
                'ID'           => $existing_post->ID,
                'post_title'   => wp_strip_all_tags($article['title']),
                'post_content' => $article['content'],
            ]);
            $reports[] = [
                'seolinkplace_id'   => $article['seolinkplace_id'],
                'wp_post_id'    => $existing_post->ID,
                'published_url' => get_permalink($existing_post->ID),
                'status'        => 'publish',
            ];
            continue;
        }

        // Публікуємо
        $author_id = (int)get_option('seolinkplace_author_id', get_current_user_id());
        $post_id = wp_insert_post([
            'post_title'   => wp_strip_all_tags($article['title']),
            'post_content' => $article['content'],
            'post_status'  => 'publish',
            'post_type'    => 'post',
            'post_author'  => $author_id,
        ]);

        if (is_wp_error($post_id)) {
            error_log('[seolinkplace] Failed to publish article ' . $article['seolinkplace_id'] . ': ' . $post_id->get_error_message());
            continue;
        }

        // Зберігаємо мета-дані
        update_post_meta($post_id, '_seolinkplace_article_id', $article['seolinkplace_id']);

        $reports[] = [
            'seolinkplace_id'   => $article['seolinkplace_id'],
            'wp_post_id'    => $post_id,
            'published_url' => get_permalink($post_id),
            'status'        => 'publish',
        ];
    }

    // Звітуємо назад
    if (!empty($reports)) {
        seolinkplace_api('POST', '/wp/report', ['articles' => $reports]);
    }
});

if (!wp_next_scheduled('seolinkplace_publish_articles')) {
    wp_schedule_event(time(), 'seolinkplace_5m', 'seolinkplace_publish_articles');
}


// ─── AJAX: статистика ─────────────────────────────────────────────────────────
add_action('wp_ajax_seolinkplace_get_stats', function(): void {
    check_ajax_referer('seolinkplace_stats', 'nonce');
    $stats = seolinkplace_api('GET', '/wp/stats');
    if ($stats && !empty($stats['data'])) {
        wp_send_json_success($stats['data']);
    } else {
        wp_send_json_error('Failed to load stats');
    }
});

// ─── AJAX: синхронізація батчами ──────────────────────────────────────────────
add_action('wp_ajax_seolinkplace_sync_batch', function(): void {
    check_ajax_referer('seolinkplace_sync', 'nonce');

    $offset   = (int)($_POST['offset'] ?? 0);
    $per_page = 50;

    $query = new WP_Query([
        'post_type'      => ['post', 'page'],
        'post_status'    => 'publish',
        'posts_per_page' => $per_page,
        'offset'         => $offset,
        'no_found_rows'  => false,
    ]);

    if (!$query->have_posts()) {
        wp_send_json_success(['done' => true, 'total' => $offset]);
        return;
    }

    $pages = [];
    // При першому батчі додаємо головну сторінку
    if ($offset === 0) {
        $pages[] = [
            'url'          => home_url('/'),
            'title'        => get_bloginfo('name'),
            'wp_post_id'   => 0,
            'post_type'    => 'homepage',
            'status'       => 'publish',
            'published_at' => date('c'),
        ];
    }
    foreach ($query->posts as $p) {
        $pages[] = [
            'url'          => get_permalink($p->ID),
            'title'        => $p->post_title,
            'wp_post_id'   => $p->ID,
            'post_type'    => $p->post_type,
            'status'       => $p->post_status,
            'published_at' => get_post_time('c', true, $p->ID),
        ];
    }
    wp_reset_postdata();

    $result = seolinkplace_api('POST', '/wp/pages', ['pages' => $pages]);

    $synced      = $offset + count($pages);
    $total_found = $query->found_posts;
    $done        = $synced >= $total_found;

    if ($result) {
        wp_send_json_success([
            'done'        => $done,
            'synced'      => $synced,
            'total'       => $total_found,
            'next_offset' => $synced,
        ]);
    } else {
        wp_send_json_error('API error');
    }
});
// ─── Деактивація ──────────────────────────────────────────────────────────────

register_deactivation_hook(__FILE__, function(): void {
    wp_clear_scheduled_hook('seolinkplace_sync');
    wp_clear_scheduled_hook('seolinkplace_publish_articles');
});
