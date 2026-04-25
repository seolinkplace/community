<?php

namespace Modules\Support\Filament\Resources\SupportTickets\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SupportTicketInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user.name')
                    ->label('User'),
                TextEntry::make('role'),
                TextEntry::make('subject'),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('priority')
                    ->badge(),
                TextEntry::make('assigned_to')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('last_reply_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
