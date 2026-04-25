<?php
namespace Modules\Core\Filament\Resources\Webmasters\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class WebmasterForm
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
                Select::make('status')
                    ->options(['pending' => 'Pending', 'verified' => 'Verified', 'rejected' => 'Rejected'])
                    ->default('pending')
                    ->required(),
                Select::make('plan')
                    ->options(['free' => 'Free', 'pro' => 'Pro'])
                    ->default('free')
                    ->required(),
                Toggle::make('freeze_disabled')
                    ->label('Disable 3-month earning freeze')
                    ->helperText('When enabled, this webmaster can withdraw earnings immediately without the 3-month hold.')
                    ->default(false),
            ]);
    }
}
