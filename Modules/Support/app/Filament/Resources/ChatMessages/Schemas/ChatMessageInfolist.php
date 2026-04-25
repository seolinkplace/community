<?php

namespace Modules\Support\Filament\Resources\ChatMessages\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ChatMessageInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('campaignLink.id')
                    ->label('Campaign link'),
                TextEntry::make('sender_type')
                    ->badge(),
                TextEntry::make('sender_id')
                    ->numeric(),
                TextEntry::make('body')
                    ->columnSpanFull(),
                TextEntry::make('read_at')
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
