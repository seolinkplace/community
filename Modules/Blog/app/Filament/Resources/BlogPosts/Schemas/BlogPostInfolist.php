<?php

namespace Modules\Blog\Filament\Resources\BlogPosts\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class BlogPostInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('slug'),
                TextEntry::make('title'),
                TextEntry::make('title_en')
                    ->placeholder('-'),
                TextEntry::make('excerpt')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('excerpt_en')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('content')
                    ->columnSpanFull(),
                TextEntry::make('content_en')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('meta_title')
                    ->placeholder('-'),
                TextEntry::make('meta_title_en')
                    ->placeholder('-'),
                TextEntry::make('meta_description')
                    ->placeholder('-'),
                TextEntry::make('meta_description_en')
                    ->placeholder('-'),
                ImageEntry::make('cover_image')
                    ->placeholder('-'),
                TextEntry::make('published_at')
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
