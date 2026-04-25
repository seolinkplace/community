<?php

namespace Modules\Core\Filament\Resources;

use Modules\Core\Filament\Resources\SubscriptionPlans\Pages\ManageSubscriptionPlans;
use App\Models\SubscriptionPlan;
use BackedEnum;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Support\Icons\Heroicon;

class SubscriptionPlanResource extends Resource
{
    protected static ?string $model = SubscriptionPlan::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    public static function getNavigationGroup(): ?string
    {
        return 'Система';
    }

    public static function getNavigationLabel(): string
    {
        return 'Тарифні плани';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('slug')
                ->label('Slug')
                ->required()
                ->unique(ignoreRecord: true)
                ->alphaDash()
                ->maxLength(64),

            Select::make('role')
                ->label('Роль')
                ->options([
                    'client'    => 'Клієнт / Агенція',
                    'webmaster' => 'Вебмастер',
                ])
                ->required(),

            TextInput::make('name.uk')
                ->label('Назва (UK)')
                ->required()
                ->maxLength(100),

            TextInput::make('name.en')
                ->label('Назва (EN)')
                ->required()
                ->maxLength(100),

            TextInput::make('description.uk')
                ->label('Опис (UK)')
                ->maxLength(255),

            TextInput::make('description.en')
                ->label('Опис (EN)')
                ->maxLength(255),

            TextInput::make('price_monthly')
                ->label('Ціна / міс ($)')
                ->numeric()
                ->minValue(0)
                ->step(0.01)
                ->required()
                ->default(0),

            TextInput::make('sort_order')
                ->label('Порядок сортування')
                ->numeric()
                ->default(0),

            Toggle::make('is_active')
                ->label('Активний')
                ->default(true),

            Toggle::make('is_purchasable')
                ->label('Можна оформити')
                ->helperText('Якщо вимкнено — план видно, але підписатись неможливо')
                ->default(true),

            KeyValue::make('features')
                ->label('Features (JSON)')
                ->keyLabel('Ключ')
                ->valueLabel('Значення')
                ->reorderable()
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('role')
                    ->label('Роль')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'client'    => 'info',
                        'webmaster' => 'success',
                        default     => 'gray',
                    }),

                TextColumn::make('name.uk')
                    ->label('Назва'),

                TextColumn::make('price_monthly')
                    ->label('$/міс')
                    ->money('USD')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Активний')
                    ->boolean(),

                IconColumn::make('is_purchasable')
                    ->label('Оформлення')
                    ->boolean(),

                TextColumn::make('sort_order')
                    ->label('Порядок')
                    ->sortable(),

                TextColumn::make('subscriptions_count')
                    ->label('Підписок')
                    ->counts('subscriptions')
                    ->sortable(),
            ])
            ->defaultSort('role')
            ->filters([
                SelectFilter::make('role')
                    ->label('Роль')
                    ->options([
                        'client'    => 'Клієнт',
                        'webmaster' => 'Вебмастер',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->hidden(fn ($record) => $record->subscriptions_count > 0),
            ])
            ->headerActions([
                CreateAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageSubscriptionPlans::route('/'),
        ];
    }
}
