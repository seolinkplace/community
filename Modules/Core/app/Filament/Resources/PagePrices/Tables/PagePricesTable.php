<?php
namespace Modules\Core\Filament\Resources\PagePrices\Tables;

use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PagePricesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('site.domain')->label('Сайт')->sortable()->searchable(),
                TextColumn::make('wpPage.url')->label('Сторінка')->limit(50)->default('— дефолт сайту —'),
                TextColumn::make('base_price_per_day')->label('База/день')->money('USD'),
                TextColumn::make('price_link_per_day')->label('Посилання/день')->money('USD'),
                TextColumn::make('price_onclick_per_day')->label('Onclick/день')->money('USD'),
                TextColumn::make('price_article_once')->label('Стаття разова')->money('USD'),
                TextColumn::make('max_placements')->label('Макс.'),
                IconColumn::make('is_public')->label('Публічна')->boolean(),
            ])
            ->filters([
                SelectFilter::make('site_id')
                    ->label('Сайт')
                    ->relationship('site', 'domain'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
