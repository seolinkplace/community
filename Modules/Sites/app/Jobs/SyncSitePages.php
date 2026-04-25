<?php

namespace Modules\Sites\Jobs;

use App\Models\SiteConnection;
use App\Models\SitePage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncSitePages implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300;
    public int $tries   = 3;

    public function __construct(public SiteConnection $wpSite) {}

    public function handle(): void
    {
        $wpSite = $this->wpSite;

        try {
            $wpSite->update(['status' => 'active', 'last_error' => null]);

            // Отримуємо всі пости через WP REST API
            $pages    = [];
            $page     = 1;
            $perPage  = 100;

            do {
                $response = Http::timeout(30)
                    ->withBasicAuth($wpSite->wp_username, $wpSite->wp_app_password)
                    ->get($wpSite->wp_url . '/wp-json/wp/v2/posts', [
                        'per_page' => $perPage,
                        'page'     => $page,
                        'status'   => 'publish',
                        '_fields'  => 'id,link,title,status,date,type,content',
                    ]);

                if (!$response->successful()) break;

                $posts = $response->json();
                if (empty($posts)) break;

                foreach ($posts as $post) {
                    // Витягуємо анкори з контенту
                    $anchors = [];
                    $content = $post['content']['rendered'] ?? '';
                    if (preg_match_all('/<a[^>]+href=["\']([^"\']+)["\'][^>]*>(.*?)<\/a>/is', $content, $matches)) {
                        foreach ($matches[1] as $i => $href) {
                            $text = strip_tags($matches[2][$i]);
                            if ($text) $anchors[] = ['href' => $href, 'text' => trim($text)];
                        }
                    }

                    SitePage::updateOrCreate(
                        ['site_id' => $wpSite->id, 'url' => $post['link']],
                        [
                            'title'        => $post['title']['rendered'] ?? '',
                            'wp_post_id'   => $post['id'],
                            'post_type'    => $post['type'] ?? 'post',
                            'status'       => $post['status'] ?? 'publish',
                            'published_at' => $post['date'] ?? null,
                            'anchors'      => $anchors,
                            'synced_at'    => now(),
                        ]
                    );
                    $pages[] = $post['id'];
                }

                $totalPages = (int) $response->header('X-WP-TotalPages');
                $page++;

            } while ($page <= $totalPages);

            // Повторимо для pages
            $pageNum = 1;
            do {
                $response = Http::timeout(30)
                    ->withBasicAuth($wpSite->wp_username, $wpSite->wp_app_password)
                    ->get($wpSite->wp_url . '/wp-json/wp/v2/pages', [
                        'per_page' => $perPage,
                        'page'     => $pageNum,
                        'status'   => 'publish',
                        '_fields'  => 'id,link,title,status,date,type',
                    ]);

                if (!$response->successful()) break;
                $wpPages = $response->json();
                if (empty($wpPages)) break;

                foreach ($wpPages as $post) {
                    SitePage::updateOrCreate(
                        ['site_id' => $wpSite->id, 'url' => $post['link']],
                        [
                            'title'      => $post['title']['rendered'] ?? '',
                            'wp_post_id' => $post['id'],
                            'post_type'  => 'page',
                            'status'     => $post['status'] ?? 'publish',
                            'published_at' => $post['date'] ?? null,
                            'anchors'    => [],
                            'synced_at'  => now(),
                        ]
                    );
                }

                $totalPages = (int) $response->header('X-WP-TotalPages');
                $pageNum++;

            } while ($pageNum <= $totalPages);

            $count = SitePage::where('site_id', $wpSite->id)->count();
            $wpSite->update([
                'pages_count'  => $count,
                'last_sync_at' => now(),
                'status'       => 'active',
            ]);

            Log::info("[seolinkplace] SiteConnection #{$wpSite->id} synced: {$count} pages");

        } catch (\Exception $e) {
            Log::error("[seolinkplace] SiteConnection #{$wpSite->id} sync error: " . $e->getMessage());
            $wpSite->update(['status' => 'error', 'last_error' => $e->getMessage()]);
        }
    }
}
