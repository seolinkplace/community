<?php
namespace Modules\Core\Filament\Resources\Wallets;

use Modules\Core\Filament\Resources\Wallets\Pages\ListWallets;
use Modules\Core\Filament\Resources\Wallets\Pages\ViewWallet;
use Modules\Core\Filament\Resources\Wallets\Schemas\WalletInfolist;
use Modules\Core\Filament\Resources\Wallets\Tables\WalletsTable;
use App\Models\Wallet;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class WalletResource extends Resource
{
    public static function getNavigationGroup(): ?string { return 'Фінанси'; }
    public static function getNavigationSort(): ?int { return 1; }

    protected static ?string $model = Wallet::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::CurrencyDollar;
    protected static ?string $navigationLabel = 'Wallets';
    
    public static function infolist(Schema $schema): Schema
    {
        return WalletInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WalletsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWallets::route('/'),
            'view'  => ViewWallet::route('/{record}'),
        ];
    }
}
