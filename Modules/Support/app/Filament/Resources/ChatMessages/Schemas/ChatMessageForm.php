<?php

namespace Modules\Support\Filament\Resources\ChatMessages\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ChatMessageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('campaign_link_id')
                    ->relationship('campaignLink', 'id')
                    ->required(),
                Select::make('sender_type')
                    ->options(['client' => 'Client', 'webmaster' => 'Webmaster', 'admin' => 'Admin'])
                    ->required(),
                TextInput::make('sender_id')
                    ->required()
                    ->numeric(),
                Textarea::make('body')
                    ->required()
                    ->columnSpanFull(),
                DateTimePicker::make('read_at'),
            ]);
    }
}
