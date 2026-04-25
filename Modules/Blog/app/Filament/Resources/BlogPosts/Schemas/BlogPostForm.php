<?php

namespace Modules\Blog\Filament\Resources\BlogPosts\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class BlogPostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('slug')
                    ->required(),
                TextInput::make('title')
                    ->required(),
                TextInput::make('title_en')
                    ->default(null),
                Textarea::make('excerpt')
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('excerpt_en')
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('content')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('content_en')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('meta_title')
                    ->default(null),
                TextInput::make('meta_title_en')
                    ->default(null),
                TextInput::make('meta_description')
                    ->default(null),
                TextInput::make('meta_description_en')
                    ->default(null),
                FileUpload::make('cover_image')
                    ->image(),
                DateTimePicker::make('published_at'),
            ]);
    }
}
