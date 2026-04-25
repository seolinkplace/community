<?php

namespace Modules\Sites\Filament\Resources\Sites\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Schemas\Schema;

class SiteInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('webmaster.name')
                    ->label('Webmaster'),
                TextEntry::make('domain'),
                TextEntry::make('platform_type')
                    ->badge()
                    ->placeholder('-'),
                TextEntry::make('platform_url')
                    ->placeholder('-')
                    ->url(fn ($record) => $record?->platform_url)
                    ->openUrlInNewTab(),
                TextEntry::make('followers_count')
                    ->numeric()
                    ->placeholder('-'),
                IconEntry::make('first_post_required')
                    ->boolean()
                    ->label('First post required'),
                IconEntry::make('first_post_published')
                    ->boolean()
                    ->label('First post published'),
                TextEntry::make('niche')
                    ->placeholder('-'),
                TextEntry::make('language')
                    ->placeholder('-'),
                TextEntry::make('dr')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('traffic')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('content_type')
                    ->badge(),
                TextEntry::make('price')
                    ->money()
                    ->placeholder('-'),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('contact')
                    ->placeholder('-'),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('visibility')
                    ->badge(),
                TextEntry::make('metrics_source')
                    ->placeholder('-'),
                TextEntry::make('metrics_updated_at')
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
