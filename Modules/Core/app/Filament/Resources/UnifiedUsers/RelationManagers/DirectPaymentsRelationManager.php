<?php

namespace Modules\Core\Filament\Resources\UnifiedUsers\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DirectPaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'directPaymentsAsSender';
    protected static ?string $title = 'Прямі платежі';

    public function table(Table $table): Table
    {
        $userId = $this->getOwnerRecord()->id;

        return $table
            ->query(fn() => \App\Models\DirectPayment::query()
                ->where(fn($q) => $q
                    ->where('client_id', $userId)
                    ->orWhere('webmaster_id', $userId)
                )
            )
            ->columns([
                TextColumn::make('uuid')
                    ->label('UUID')
                    ->limit(8)
                    ->tooltip(fn($record) => $record->uuid),
                TextColumn::make('direction')
                    ->label('Роль')
                    ->getStateUsing(fn($record) => $record->client_id === $userId ? 'Відправник' : 'Отримувач')
                    ->badge()
                    ->color(fn($state) => $state === 'Відправник' ? 'warning' : 'success'),
                TextColumn::make('amount')
                    ->label('Сума')
                    ->money('USD'),
                TextColumn::make('status')
                    ->label('Статус')
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'confirmed' => 'success',
                        'pending'   => 'warning',
                        'rejected'  => 'danger',
                        'expired'   => 'gray',
                        default     => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('Дата')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                TextColumn::make('expires_at')
                    ->label('Діє до')
                    ->dateTime('d.m.Y H:i')
                    ->placeholder('—'),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25]);
    }
}
