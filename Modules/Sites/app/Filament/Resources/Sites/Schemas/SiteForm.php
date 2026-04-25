<?php

namespace Modules\Sites\Filament\Resources\Sites\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Modules\Core\Models\UnifiedUser;
use Filament\Schemas\Schema;

class SiteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('Webmaster')
                    ->relationship('unifiedUser', 'name')
                    ->getOptionLabelFromRecordUsing(fn($record) => $record->name . ' (' . $record->email . ')')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('domain')
                    ->required(),
                TextInput::make('platform_type')
                    ->label('Platform type')
                    ->default('website')
                    ->datalist([
                        'website', 'facebook', 'instagram', 'tiktok', 'linkedin',
                        'telegram', 'youtube', 'x', 'threads', 'pinterest', 'reddit', 'bluesky',
                    ])
                    ->required(),
                TextInput::make('platform_url')
                    ->url()
                    ->default(null),
                TextInput::make('followers_count')
                    ->numeric()
                    ->default(0),
                TextInput::make('niche')
                    ->default(null),
                TextInput::make('language')
                    ->default(null),
                TextInput::make('dr')
                    ->numeric()
                    ->default(null),
                TextInput::make('traffic')
                    ->numeric()
                    ->default(null),
                DatePicker::make('domain_registered_at')
                    ->label('Domain registered at')
                    ->default(null),
                DatePicker::make('domain_expires_at')
                    ->label('Domain expires at')
                    ->default(null),
                TextInput::make('spam_score')
                    ->label('Spam score (0-100)')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->default(null),
                TextInput::make('pages_count')
                    ->label('Pages in system')
                    ->numeric()
                    ->disabled()
                    ->default(0),
                Select::make('content_type')
                    ->options(['article' => 'Article', 'link_insert' => 'Link insert', 'both' => 'Both'])
                    ->default('both')
                    ->required(),
                TextInput::make('price')
                    ->numeric()
                    ->default(null)
                    ->prefix('$'),
                Textarea::make('description')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('contact')
                    ->default(null),
                Select::make('status')
                    ->options(['active' => 'Active', 'suspended' => 'Suspended', 'rejected' => 'Rejected'])
                    ->default('active')
                    ->required(),
                Select::make('visibility')
                    ->options(['public' => 'Public', 'private' => 'Private'])
                    ->default('public')
                    ->required(),
                TextInput::make('metrics_source')
                    ->default(null),
                DateTimePicker::make('metrics_updated_at'),
                Toggle::make('first_post_published')
                    ->label('First post published')
                    ->default(false),
                Toggle::make('first_post_required')
                    ->label('First post required')
                    ->default(true),
            ]);
    }
}
