<?php

namespace Modules\Sites\Filament\Resources\SitePages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use App\Models\SiteConnection;

class SitePagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('wpSite.wp_url')
                    ->label('Сайт')
                    ->sortable(),
                TextColumn::make('url')
                    ->label('URL')
                    ->searchable()
                    ->limit(60),
                TextColumn::make('title')
                    ->label('Заголовок')
                    ->searchable()
                    ->limit(50),
                TextColumn::make('post_type')
                    ->label('Тип')
                    ->badge(),
                TextColumn::make('status')
                    ->label('Статус')
                    ->badge(),
                TextColumn::make('published_at')
                    ->label('Опубліковано')
                    ->dateTime('d.m.Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('site_id')
                    ->label('Сайт')
                    ->options(SiteConnection::pluck('wp_url', 'id'))
                    ->searchable(),
                SelectFilter::make('post_type')
                    ->label('Тип')
                    ->options(['post' => 'Post', 'page' => 'Page']),
            ])
            ->defaultSort('published_at', 'desc')
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
