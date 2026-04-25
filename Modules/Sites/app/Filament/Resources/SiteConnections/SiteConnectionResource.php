<?php

namespace Modules\Sites\Filament\Resources\SiteConnections;

use Modules\Sites\Filament\Resources\SiteConnections\Pages\ListSiteConnections;
use Modules\Sites\Filament\Resources\SiteConnections\Pages\ViewSiteConnection;
use Modules\Sites\Filament\Resources\SiteConnections\Schemas\SiteConnectionInfolist;
use Modules\Sites\Filament\Resources\SiteConnections\Tables\SiteConnectionsTable;
use App\Models\SiteConnection;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class SiteConnectionResource extends Resource
{
    public static function getNavigationGroup(): ?string { return 'Сайти'; }
    public static function getNavigationSort(): ?int { return 2; }

    protected static ?string $model = SiteConnection::class;

    public static function infolist(Schema $infolist): Schema
    {
        return SiteConnectionInfolist::configure($infolist);
    }

    public static function table(Table $table): Table
    {
        return SiteConnectionsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSiteConnections::route('/'),
            'view'  => ViewSiteConnection::route('/{record}'),
        ];
    }
}
