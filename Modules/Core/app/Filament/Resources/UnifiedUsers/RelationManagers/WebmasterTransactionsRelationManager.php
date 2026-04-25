<?php

namespace Modules\Core\Filament\Resources\UnifiedUsers\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WebmasterTransactionsRelationManager extends RelationManager
{
    protected static string $relationship = 'webmasterWallet';
    protected static ?string $title = 'Транзакції вебмастера';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn() => \App\Models\WebmasterTransaction::query()
                ->whereHas('wallet', fn($q) => $q->where('user_id', $this->getOwnerRecord()->id))
            )
            ->columns([
                TextColumn::make('amount')
                    ->label('Сума')
                    ->money('USD')
                    ->color(fn($state) => $state >= 0 ? 'success' : 'danger'),
                TextColumn::make('balance_after')
                    ->label('Баланс після')
                    ->money('USD'),
                TextColumn::make('type')
                    ->label('Тип')
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'earning'    => 'success',
                        'withdrawal' => 'warning',
                        'refund'     => 'info',
                        default      => 'gray',
                    }),
                TextColumn::make('description')
                    ->label('Опис')
                    ->limit(50)
                    ->placeholder('—'),
                TextColumn::make('created_at')
                    ->label('Дата')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([15, 25, 50]);
    }
}
