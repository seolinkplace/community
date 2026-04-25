<?php
namespace Modules\Core\Observers;

use App\Models\Client;
use App\Models\Wallet;

class ClientObserver
{
    public function created(Client $client): void
    {
        Wallet::firstOrCreate(
            ['client_id' => $client->id],
            ['balance' => 0, 'reserved' => 0]
        );
    }
}
