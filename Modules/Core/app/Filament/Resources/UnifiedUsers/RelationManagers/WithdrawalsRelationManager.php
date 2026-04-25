<?php

namespace Modules\Core\Filament\Resources\UnifiedUsers\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WithdrawalsRelationManager extends RelationManager
{
    protected static string $relationship = 'withdrawals';
    protected static ?string $title = 'Виводи';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('amount')
                    ->label('Сума')
                    ->money('USD'),
                TextColumn::make('commission')
                    ->label('Комісія')
                    ->money('USD'),
                TextColumn::make('commission_pct')
                    ->label('Комісія %')
                    ->suffix('%'),
                TextColumn::make('method')
                    ->label('Метод')
                    ->placeholder('—'),
                TextColumn::make('status')
                    ->label('Статус')
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'completed' => 'success',
                        'pending'   => 'warning',
                        'rejected'  => 'danger',
                        default     => 'gray',
                    }),
                TextColumn::make('admin_note')
                    ->label('Примітка')
                    ->limit(40)
                    ->placeholder('—'),
                TextColumn::make('processed_at')
                    ->label('Оброблено')
                    ->dateTime('d.m.Y H:i')
                    ->placeholder('—'),
                TextColumn::make('created_at')
                    ->label('Запит')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25]);
    }
}
