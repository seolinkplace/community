<?php

namespace Modules\Sites\Filament\Resources\Sites;

use Modules\Sites\Filament\Resources\Sites\Pages\CreateSite;
use Modules\Sites\Filament\Resources\Sites\Pages\EditSite;
use Modules\Sites\Filament\Resources\Sites\Pages\ListSites;
use Modules\Sites\Filament\Resources\Sites\Pages\ViewSite;
use Modules\Sites\Filament\Resources\Sites\Schemas\SiteForm;
use Modules\Sites\Filament\Resources\Sites\Schemas\SiteInfolist;
use Modules\Sites\Filament\Resources\Sites\Tables\SitesTable;
use App\Models\Site;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SiteResource extends Resource
{
    public static function getNavigationGroup(): ?string { return 'Сайти'; }
    public static function getNavigationSort(): ?int { return 1; }

    protected static ?string $model = Site::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'domain';

    public static function form(Schema $schema): Schema
    {
        return SiteForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SiteInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SitesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSites::route('/'),
            'create' => CreateSite::route('/create'),
            'view' => ViewSite::route('/{record}'),
            'edit' => EditSite::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            \Illuminate\Database\Eloquent\SoftDeletingScope::class,
        ]);
    }
}
