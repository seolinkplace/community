<?php
namespace Modules\Blog\Filament\Resources\BlogPosts;

use Modules\Blog\Filament\Resources\BlogPosts\Pages;
use Modules\Blog\Models\BlogPost;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;

class BlogPostResource extends Resource
{
    protected static ?string $model = BlogPost::class;
    protected static ?string $navigationLabel = 'Блог';
    protected static ?string $modelLabel = 'Стаття';
    protected static ?string $pluralModelLabel = 'Статті блогу';

    public static function getNavigationIcon(): string|\Illuminate\Contracts\Support\Htmlable|null
    {
        return 'heroicon-o-document-text';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Контент';
    }

    public static function form(Schema $form): Schema
    {
        return $form->columns(1)->schema([
            Section::make('Основне (UK)')
                ->columns(2)
                ->schema([
                    TextInput::make('title')
                        ->label('Заголовок (UK)')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, Set $set) =>
                            $set('slug', \Str::slug($state))
                        )
                        ->columnSpan(1),
                    TextInput::make('slug')
                        ->label('Slug')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255)
                        ->columnSpan(1),
                    Textarea::make('excerpt')
                        ->label('Короткий опис (UK)')
                        ->rows(2)
                        ->columnSpanFull(),
                    RichEditor::make('content')
                        ->label('Контент (UK)')
                        ->required()
                        ->columnSpanFull(),
                ]),
            Section::make('English version')
                ->collapsed()
                ->schema([
                    TextInput::make('title_en')
                        ->label('Title (EN)')
                        ->maxLength(255),
                    Textarea::make('excerpt_en')
                        ->label('Excerpt (EN)')
                        ->rows(2),
                    RichEditor::make('content_en')
                        ->label('Content (EN)'),
                ]),
            Section::make('SEO')
                ->columns(2)
                ->collapsed()
                ->schema([
                    TextInput::make('meta_title')
                        ->label('Meta Title (UK)')
                        ->maxLength(255),
                    TextInput::make('meta_title_en')
                        ->label('Meta Title (EN)')
                        ->maxLength(255),
                    Textarea::make('meta_description')
                        ->label('Meta Description (UK)')
                        ->rows(2),
                    Textarea::make('meta_description_en')
                        ->label('Meta Description (EN)')
                        ->rows(2),
                ]),
            Section::make('Публікація')
                ->columns(2)
                ->schema([
                    TextInput::make('cover_image')
                        ->label('Cover Image URL')
                        ->url()
                        ->columnSpanFull(),
                    DateTimePicker::make('published_at')
                        ->label('Дата публікації')
                        ->helperText('Залиш порожнім — чернетка')
                        ->columnSpan(1),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Заголовок')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Опубліковано')
                    ->boolean()
                    ->getStateUsing(fn ($record) => $record->isPublished()),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Дата')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Оновлено')
                    ->dateTime('d.m.Y')
                    ->sortable(),
            ])
            ->defaultSort('published_at', 'desc')
            ->filters([
                Tables\Filters\Filter::make('published')
                    ->label('Тільки опубліковані')
                    ->query(fn ($query) => $query->published()),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListBlogPosts::route('/'),
            'create' => Pages\CreateBlogPost::route('/create'),
            'edit'   => Pages\EditBlogPost::route('/{record}/edit'),
        ];
    }
}
