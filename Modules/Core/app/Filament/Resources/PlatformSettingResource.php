<?php
namespace Modules\Core\Filament\Resources;

use Modules\Core\Filament\Resources\PlatformSettings\Pages\ManagePlatformSettings;
use App\Models\PlatformSetting;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Support\Icons\Heroicon;

class PlatformSettingResource extends Resource
{
    protected static ?string $model = PlatformSetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    public static function getNavigationGroup(): ?string
    {
        return 'Система';
    }

    public static function getNavigationLabel(): string
    {
        return 'Налаштування платформи';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('key')
                ->required()
                ->disabled()
                ->label('Ключ'),

            Select::make('value')
                ->label('Значення')
                ->visible(fn ($record) => $record?->key === 'task_moderation_mode')
                ->options([
                    'off'      => 'Вимкнено — всі публікують без перевірки',
                    'new_only' => 'Тільки нові користувачі (за замовчуванням)',
                    'all'      => 'Всі проходять модерацію',
                ])
                ->required(),

            TextInput::make('value')
                ->label('Значення')
                ->visible(fn ($record) => $record?->key !== 'task_moderation_mode')
                ->required(),

            Textarea::make('description')
                ->label('Опис')
                ->disabled()
                ->rows(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')
                    ->label('Ключ')
                    ->searchable(),
                TextColumn::make('value')
                    ->label('Значення')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'off'      => 'success',
                        'new_only' => 'warning',
                        'all'      => 'danger',
                        default    => 'gray',
                    }),
                TextColumn::make('description')
                    ->label('Опис')
                    ->wrap(),
                TextColumn::make('updated_at')
                    ->label('Оновлено')
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManagePlatformSettings::route('/'),
        ];
    }
}
