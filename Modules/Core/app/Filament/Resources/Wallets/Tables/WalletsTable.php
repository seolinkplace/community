<?php
namespace Modules\Core\Filament\Resources\Wallets\Tables;

use App\Models\Wallet;
use App\Models\WalletTransaction;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WalletsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('unifiedUser.name')
                    ->label('Клієнт')
                    ->searchable()
                    ->sortable()
                    ->getStateUsing(fn ($record) => $record->unifiedUser?->name
                        ?? $record->unifiedUser?->email
                        ?? $record->client?->email
                        ?? '—'),
                TextColumn::make('balance')->label('Баланс')->money('USD')->sortable(),
                TextColumn::make('reserved')->label('Зарезервовано')->money('USD'),
                TextColumn::make('updated_at')->label('Оновлено')->dateTime('d.m.Y H:i')->sortable(),
            ])
            ->defaultSort('balance', 'desc')
            ->recordActions([
                ViewAction::make(),
                Action::make('deposit')
                    ->label('Поповнити')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->form([
                        Forms\Components\TextInput::make('amount')
                            ->label('Сума ($)')
                            ->numeric()
                            ->required()
                            ->minValue(0.01),
                        Forms\Components\TextInput::make('description')
                            ->label('Опис')
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        $record->balance += $data['amount'];
                        $record->save();
                        WalletTransaction::create([
                            'wallet_id'     => $record->id,
                            'amount'        => $data['amount'],
                            'balance_after' => $record->balance,
                            'type'          => 'deposit',
                            'description'   => $data['description'],
                        ]);
                    }),
            ])
            ->toolbarActions([]);
    }
}
