<?php

namespace Modules\Sites\Filament\Resources\SiteConnections\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SiteConnectionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenantToken.id')
                    ->label('Tenant token'),
                TextEntry::make('webmaster.name')
                    ->label('Webmaster'),
                TextEntry::make('site.id')
                    ->label('Site'),
                TextEntry::make('wp_url'),
                TextEntry::make('wp_username')
                    ->placeholder('-'),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('wp_version')
                    ->placeholder('-'),
                TextEntry::make('pages_count')
                    ->numeric(),
                TextEntry::make('last_sync_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('last_error')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
