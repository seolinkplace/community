<?php

namespace Modules\Sites\Filament\Resources\SitePages;

use Modules\Sites\Filament\Resources\SitePages\Pages\ListSitePages;
use Modules\Sites\Filament\Resources\SitePages\Pages\ViewSitePage;
use Modules\Sites\Filament\Resources\SitePages\Schemas\SitePageInfolist;
use Modules\Sites\Filament\Resources\SitePages\Tables\SitePagesTable;
use App\Models\SitePage;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class SitePageResource extends Resource
{
    public static function getNavigationGroup(): ?string { return 'Сайти'; }
    public static function getNavigationSort(): ?int { return 3; }

    protected static ?string $model = SitePage::class;

    public static function infolist(Schema $infolist): Schema
    {
        return SitePageInfolist::configure($infolist);
    }

    public static function table(Table $table): Table
    {
        return SitePagesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSitePages::route('/'),
            'view'  => ViewSitePage::route('/{record}'),
        ];
    }
}
