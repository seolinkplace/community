<?php
namespace Modules\Sites\Observers;

use App\Models\Site;
use Illuminate\Support\Str;

class SiteObserver
{
    public function creating(Site $model): void
    {
        if (empty($model->uuid)) {
            $model->uuid = Str::uuid();
        }
    }
}
