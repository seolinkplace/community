<?php

namespace Modules\Core\Filament\Resources\PlatformRules;

use Modules\Core\Filament\Resources\PlatformRules\Pages\CreatePlatformRule;
use Modules\Core\Filament\Resources\PlatformRules\Pages\EditPlatformRule;
use Modules\Core\Filament\Resources\PlatformRules\Pages\ListPlatformRules;
use App\Models\PlatformRule;
use BackedEnum;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;

class PlatformRuleResource extends Resource
{
    protected static ?string $model = PlatformRule::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::DocumentText;
    protected static ?string $navigationLabel = 'Правила';
    protected static string|\UnitEnum|null $navigationGroup = 'Контент';
    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title_uk')
                ->label('Заголовок (UK)')
                ->required()
                ->maxLength(255)
                ->live(onBlur: true)
                ->afterStateUpdated(fn($state, callable $set) =>
                    $set('slug', \Illuminate\Support\Str::slug($state))
                ),
            TextInput::make('title_en')
                ->label('Заголовок (EN)')
                ->required()
                ->maxLength(255),
            TextInput::make('slug')
                ->label('Slug')
                ->required()
                ->unique(PlatformRule::class, 'slug', ignoreRecord: true)
                ->maxLength(255),
            TextInput::make('sort_order')
                ->label('Порядок')
                ->numeric()
                ->default(0),
            Toggle::make('is_published')
                ->label('Опубліковано')
                ->default(true),
            RichEditor::make('body_uk')
                ->label('Текст (UK)')
                ->required()
                ->columnSpanFull(),
            RichEditor::make('body_en')
                ->label('Текст (EN)')
                ->required()
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort_order')->label('#')->sortable(),
                TextColumn::make('title_uk')->label('Заголовок')->searchable(),
                TextColumn::make('slug')->label('Slug')->searchable(),
                IconColumn::make('is_published')->label('Опубліковано')->boolean(),
                TextColumn::make('comments_count')
                    ->label('Коментарів')
                    ->counts('comments'),
                TextColumn::make('updated_at')->label('Оновлено')->dateTime('d.m.Y')->sortable(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListPlatformRules::route('/'),
            'create' => CreatePlatformRule::route('/create'),
            'edit'   => EditPlatformRule::route('/{record}/edit'),
        ];
    }
}
