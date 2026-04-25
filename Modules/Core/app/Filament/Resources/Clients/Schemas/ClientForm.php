<?php

namespace Modules\Core\Filament\Resources\Clients\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ClientForm
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
                DateTimePicker::make('email_verified_at'),
                TextInput::make('password')
                    ->password()
                    ->required(),
                TextInput::make('company_name')
                    ->default(null),
                Select::make('plan')
                    ->options(['starter' => 'Starter', 'pro' => 'Pro', 'agency' => 'Agency'])
                    ->default('starter')
                    ->required(),
                Select::make('status')
                    ->options(['active' => 'Active', 'suspended' => 'Suspended', 'cancelled' => 'Cancelled'])
                    ->default('active')
                    ->required(),
                DateTimePicker::make('trial_ends_at'),
            ]);
    }
}
