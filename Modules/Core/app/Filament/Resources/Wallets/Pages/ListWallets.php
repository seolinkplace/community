<?php
namespace Modules\Core\Filament\Resources\Wallets\Pages;

use Modules\Core\Filament\Resources\Wallets\WalletResource;
use Filament\Resources\Pages\ListRecords;

class ListWallets extends ListRecords
{
    protected static string $resource = WalletResource::class;
}
