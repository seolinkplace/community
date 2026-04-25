<?php

namespace Modules\Core\Filament\Resources\UnifiedUsers\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CampaignsRelationManager extends RelationManager
{
    protected static string $relationship = 'campaigns';
    protected static ?string $title = 'Кампанії';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Назва')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Статус')
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'active'  => 'success',
                        'paused'  => 'warning',
                        'stopped' => 'danger',
                        default   => 'gray',
                    }),
                TextColumn::make('links_count')
                    ->label('Посилань')
                    ->counts('links'),
                TextColumn::make('created_at')
                    ->label('Створено')
                    ->dateTime('d.m.Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25]);
    }
}
