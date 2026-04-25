<?php

namespace Modules\Sites\Filament\Resources\SiteConnections\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class SiteConnectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_token_id')
                    ->relationship('tenantToken', 'id')
                    ->required(),
                Select::make('user_id')
                    ->label('Webmaster')
                    ->relationship('unifiedUser', 'name')
                    ->getOptionLabelFromRecordUsing(fn($record) => $record->name . ' (' . $record->email . ')')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('site_id')
                    ->relationship('site', 'id')
                    ->required(),
                TextInput::make('wp_url')
                    ->url()
                    ->required(),
                TextInput::make('wp_username')
                    ->default(null),
                Textarea::make('wp_app_password')
                    ->default(null)
                    ->columnSpanFull(),
                Select::make('status')
                    ->options(['active' => 'Active', 'error' => 'Error', 'disconnected' => 'Disconnected'])
                    ->default('active')
                    ->required(),
                TextInput::make('wp_version')
                    ->default(null),
                TextInput::make('pages_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                DateTimePicker::make('last_sync_at'),
                Textarea::make('last_error')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
