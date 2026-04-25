<?php

namespace Modules\Core\Filament\Resources\UnifiedUsers\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UnifiedUserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('password')
                    ->password()
                    ->required(),
                DateTimePicker::make('email_verified_at'),
                Select::make('status')
                    ->options(['active' => 'Active', 'banned' => 'Banned', 'pending' => 'Pending'])
                    ->default('active')
                    ->required(),
                DateTimePicker::make('chat_banned_at'),
                Toggle::make('is_trusted')
                    ->label('Trusted user (skip task moderation)')
                    ->helperText('If enabled, this user can publish tasks without admin review.'),
            ]);
    }
}
