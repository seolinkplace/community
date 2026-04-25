<?php

namespace Modules\Sites\Filament\Resources\SitePages\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class SitePageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('site_id')
                    ->relationship('wpSite', 'id')
                    ->required(),
                TextInput::make('url')
                    ->url()
                    ->required(),
                TextInput::make('title')
                    ->default(null),
                Textarea::make('anchors')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('wp_post_id')
                    ->numeric()
                    ->default(null),
                Select::make('post_type')
                    ->options(['post' => 'Post', 'page' => 'Page', 'custom' => 'Custom'])
                    ->default('post')
                    ->required(),
                Select::make('status')
                    ->options(['publish' => 'Publish', 'draft' => 'Draft', 'private' => 'Private'])
                    ->default('publish')
                    ->required(),
                DateTimePicker::make('published_at'),
                DateTimePicker::make('synced_at'),
            ]);
    }
}
