<?php

namespace Modules\Core\Filament\Resources\Webmasters;

use Modules\Core\Filament\Resources\Webmasters\Pages\CreateWebmaster;
use Modules\Core\Filament\Resources\Webmasters\Pages\EditWebmaster;
use Modules\Core\Filament\Resources\Webmasters\Pages\ListWebmasters;
use Modules\Core\Filament\Resources\Webmasters\Pages\ViewWebmaster;
use Modules\Core\Filament\Resources\Webmasters\Schemas\WebmasterForm;
use Modules\Core\Filament\Resources\Webmasters\Schemas\WebmasterInfolist;
use Modules\Core\Filament\Resources\Webmasters\Tables\WebmastersTable;
use App\Models\Webmaster;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class WebmasterResource extends Resource
{
    public static function shouldRegisterNavigation(): bool { return false; }
    public static function isGloballySearchable(): bool { return false; }
    public static function getNavigationGroup(): ?string { return 'Користувачі'; }
    public static function getNavigationSort(): ?int { return 3; }

    protected static ?string $model = Webmaster::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return WebmasterForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return WebmasterInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WebmastersTable::configure($table);
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
            'index' => ListWebmasters::route('/'),
            'create' => CreateWebmaster::route('/create'),
            'view' => ViewWebmaster::route('/{record}'),
            'edit' => EditWebmaster::route('/{record}/edit'),
        ];
    }
}
