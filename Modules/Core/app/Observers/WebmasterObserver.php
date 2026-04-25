<?php
namespace Modules\Core\Observers;

use App\Models\Webmaster;
use App\Models\WebmasterWallet;

class WebmasterObserver
{
    public function created(Webmaster $webmaster): void
    {
        WebmasterWallet::firstOrCreate(
            ['webmaster_id' => $webmaster->id],
            ['balance' => 0, 'pending' => 0]
        );
    }
}
