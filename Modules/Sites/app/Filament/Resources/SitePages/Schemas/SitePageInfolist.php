<?php

namespace Modules\Sites\Filament\Resources\SitePages\Schemas;

use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;

class SitePageInfolist
{
    public static function configure(Schema $infolist): Schema
    {
        return $infolist
            ->components([
                Section::make('Сторінка')->schema([
                    TextEntry::make('wpSite.wp_url')->label('Сайт'),
                    TextEntry::make('url')->label('URL')->copyable(),
                    TextEntry::make('title')->label('Заголовок'),
                    TextEntry::make('post_type')->label('Тип')->badge(),
                    TextEntry::make('status')->label('Статус')->badge(),
                    TextEntry::make('wp_post_id')->label('WP Post ID'),
                    TextEntry::make('published_at')->label('Опубліковано')->dateTime('d.m.Y H:i'),
                    TextEntry::make('synced_at')->label('Синхронізовано')->dateTime('d.m.Y H:i'),
                ])->columns(2),

                Section::make('Анкори')->schema([
                    RepeatableEntry::make('anchors')->label('')->schema([
                        TextEntry::make('text')->label('Анкор'),
                        TextEntry::make('href')->label('URL')->copyable(),
                    ])->columns(2),
                ]),
            ]);
    }
}
