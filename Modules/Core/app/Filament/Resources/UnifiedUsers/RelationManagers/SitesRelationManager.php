<?php

namespace Modules\Core\Filament\Resources\UnifiedUsers\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SitesRelationManager extends RelationManager
{
    protected static string $relationship = 'sites';
    protected static ?string $title = 'Сайти';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('domain')
            ->columns([
                TextColumn::make('domain')
                    ->label('Домен')
                    ->searchable()
                    ->url(fn($record) => 'https://' . $record->domain, true),
                TextColumn::make('status')
                    ->label('Статус')
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'active'   => 'success',
                        'inactive' => 'danger',
                        'pending'  => 'warning',
                        default    => 'gray',
                    }),
                TextColumn::make('platform_type')
                    ->label('Тип')
                    ->placeholder('—'),
                TextColumn::make('dr')
                    ->label('DR')
                    ->placeholder('—'),
                TextColumn::make('traffic')
                    ->label('Трафік')
                    ->numeric()
                    ->placeholder('—'),
                TextColumn::make('verified_at')
                    ->label('Верифіковано')
                    ->dateTime('d.m.Y')
                    ->placeholder('—'),
                TextColumn::make('created_at')
                    ->label('Додано')
                    ->dateTime('d.m.Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25]);
    }
}
