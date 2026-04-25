<?php
namespace Modules\Sites\Console\Commands;

use App\Models\Site;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class WhoisSyncCommand extends Command
{
    protected $signature = 'whois:sync {--limit=50 : Sites per run} {--force : Re-sync all sites}';
    protected $description = 'Sync domain registration/expiry dates via whois';

    public function handle(): void
    {
        // Process oldest-synced sites first, 50 per run to avoid load
        $query = Site::whereNotNull('domain')
            ->where('platform_type', 'website')
            ->orderBy('metrics_updated_at', 'asc')
            ->limit((int) $this->option('limit'));

        if (!$this->option('force')) {
            // Skip recently synced (less than 7 days ago)
            $query->where(function ($q) {
                $q->whereNull('metrics_updated_at')
                  ->orWhere('metrics_updated_at', '<', now()->subDays(7));
            });
        }

        $sites = $query->get();
        $this->info("Processing {$sites->count()} sites...");

        foreach ($sites as $site) {
            $this->syncSite($site);
            // Small delay to avoid hammering whois servers
            usleep(500000); // 0.5s
        }

        $this->info('Done.');
    }

    private function syncSite(Site $site): void
    {
        try {
            $output = shell_exec("whois " . escapeshellarg($site->domain) . " 2>/dev/null");
            if (!$output) return;

            $registered = $this->parseDate($output, [
                '/created:\s*(.+)/i',
                '/Creation Date:\s*(.+)/i',
                '/created-date:\s*(.+)/i',
                '/domain-registration-time:\s*(.+)/i',
            ]);

            $expires = $this->parseDate($output, [
                '/expires:\s*(.+)/i',
                '/Registry Expiry Date:\s*(.+)/i',
                '/Expiry Date:\s*(.+)/i',
                '/expiration-date:\s*(.+)/i',
                '/paid-till:\s*(.+)/i',
            ]);

            $site->update([
                'domain_registered_at' => $registered,
                'domain_expires_at'    => $expires,
                'metrics_updated_at'   => now(),
            ]);

            $this->line("✓ {$site->domain}: reg={$registered} exp={$expires}");
        } catch (\Exception $e) {
            Log::warning("whois:sync failed for {$site->domain}: " . $e->getMessage());
            $this->warn("✗ {$site->domain}: " . $e->getMessage());
        }
    }

    private function parseDate(string $output, array $patterns): ?string
    {
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $output, $m)) {
                $raw = trim($m[1]);
                try {
                    return Carbon::parse($raw)->toDateString();
                } catch (\Exception $e) {
                    continue;
                }
            }
        }
        return null;
    }
}
