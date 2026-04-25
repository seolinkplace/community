<?php

namespace Modules\Sites\Filament\Resources\SiteConnections\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SiteConnectionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenantToken.id')
                    ->searchable(),
                TextColumn::make('unifiedUser.name')
                    ->label('Webmaster')
                    ->searchable(),
                TextColumn::make('site.id')
                    ->searchable(),
                TextColumn::make('wp_url')
                    ->searchable(),
                TextColumn::make('wp_username')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('wp_version')
                    ->searchable(),
                TextColumn::make('pages_count')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('last_sync_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
