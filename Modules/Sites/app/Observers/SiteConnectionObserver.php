<?php
namespace Modules\Sites\Observers;

use App\Models\SiteConnection;

class SiteConnectionObserver
{
    public function created(SiteConnection $page): void
    {
        $this->recalculate($page->site_id);
    }

    public function deleted(SiteConnection $page): void
    {
        $this->recalculate($page->site_id);
    }

    private function recalculate(int $siteId): void
    {
        \App\Models\Site::where('id', $siteId)
            ->update(['pages_count' => SiteConnection::where('site_id', $siteId)->count()]);
    }
}
