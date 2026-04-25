<?php

namespace Modules\Support\Filament\Resources\SupportTickets;

use Modules\Support\Filament\Resources\SupportTickets\Pages\CreateSupportTicket;
use Modules\Support\Filament\Resources\SupportTickets\Pages\EditSupportTicket;
use Modules\Support\Filament\Resources\SupportTickets\Pages\ListSupportTickets;
use Modules\Support\Filament\Resources\SupportTickets\Pages\ViewSupportTicket;
use Modules\Support\Filament\Resources\SupportTickets\Schemas\SupportTicketForm;
use Modules\Support\Filament\Resources\SupportTickets\Schemas\SupportTicketInfolist;
use Modules\Support\Filament\Resources\SupportTickets\Tables\SupportTicketsTable;
use App\Models\SupportTicket;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SupportTicketResource extends Resource
{
    public static function getNavigationGroup(): ?string { return 'Система'; }
    public static function getNavigationSort(): ?int { return 2; }

    protected static ?string $model = SupportTicket::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;
    protected static ?string $navigationLabel = 'Тікети підтримки';
    protected static string|\UnitEnum|null $navigationGroup = 'Support';

    public static function form(Schema $schema): Schema
    {
        return SupportTicketForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SupportTicketInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SupportTicketsTable::configure($table);
    }

    public static function getNavigationBadge(): ?string
    {
        $count = \App\Models\SupportTicket::whereIn('status', ['open', 'in_progress'])->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string
    {
        return 'danger';
    }

    public static function getRelations(): array
    {
        return [
            \Modules\Support\Filament\Resources\SupportTickets\RelationManagers\MessagesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSupportTickets::route('/'),
            'create' => CreateSupportTicket::route('/create'),
            'view' => ViewSupportTicket::route('/{record}'),
            'edit' => EditSupportTicket::route('/{record}/edit'),
        ];
    }
}
