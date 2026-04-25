<?php

namespace Modules\Core\Filament\Resources\Clients\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClientsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('plan')->badge(),
                TextColumn::make('status')->badge(),
                TextColumn::make('created_at')->dateTime('d.m.Y')->sortable(),
            ])
            ->filters([])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('impersonate')
                    ->label('Увійти як')
                    ->icon('heroicon-o-arrow-right-on-rectangle')
                    ->color('warning')
                    ->action(function ($record) {
                        return redirect()->route('admin.impersonate.client', $record->id);
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Увійти як клієнт?')
                    ->modalDescription(fn($record) => 'Ви увійдете в акаунт: ' . $record->email),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
