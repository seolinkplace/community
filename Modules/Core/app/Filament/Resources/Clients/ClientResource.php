<?php

namespace Modules\Core\Filament\Resources\Clients;

use Modules\Core\Filament\Resources\Clients\Pages\CreateClient;
use Modules\Core\Filament\Resources\Clients\Pages\EditClient;
use Modules\Core\Filament\Resources\Clients\Pages\ListClients;
use Modules\Core\Filament\Resources\Clients\Pages\ViewClient;
use Modules\Core\Filament\Resources\Clients\Schemas\ClientForm;
use Modules\Core\Filament\Resources\Clients\Schemas\ClientInfolist;
use Modules\Core\Filament\Resources\Clients\Tables\ClientsTable;
use App\Models\Client;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ClientResource extends Resource
{
    public static function shouldRegisterNavigation(): bool { return false; }
    public static function isGloballySearchable(): bool { return false; }
    public static function getNavigationGroup(): ?string { return 'Користувачі'; }
    public static function getNavigationSort(): ?int { return 2; }

    protected static ?string $model = Client::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ClientForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ClientInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClientsTable::configure($table);
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
            'index' => ListClients::route('/'),
            'create' => CreateClient::route('/create'),
            'view' => ViewClient::route('/{record}'),
            'edit' => EditClient::route('/{record}/edit'),
        ];
    }
}
