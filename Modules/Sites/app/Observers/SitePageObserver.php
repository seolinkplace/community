<?php
namespace Modules\Sites\Observers;

use App\Models\SitePage;
use App\Models\SiteConnection;

class SitePageObserver
{
    public function created(SitePage $page): void
    {
        $this->recalculate($page->site_id);
    }

    public function deleted(SitePage $page): void
    {
        $this->recalculate($page->site_id);
    }

    // site_pages.site_id = site_connections.id
    private function recalculate(int $connectionId): void
    {
        $conn = SiteConnection::find($connectionId);
        if (!$conn) return;

        \App\Models\Site::where('id', $conn->site_id)
            ->update(['pages_count' => SitePage::where('site_id', $connectionId)->count()]);
    }
}
