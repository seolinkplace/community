<?php
namespace Modules\Core\Filament\Resources\Wallets\Schemas;

use Filament\Infolists;
use Filament\Schemas\Schema;

class WalletInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Infolists\Components\TextEntry::make('client.email')->label('Клієнт'),
            Infolists\Components\TextEntry::make('balance')->label('Баланс')->money('USD'),
            Infolists\Components\TextEntry::make('reserved')->label('Зарезервовано')->money('USD'),
            Infolists\Components\TextEntry::make('updated_at')->label('Оновлено')->dateTime('d.m.Y H:i'),
        ]);
    }
}
