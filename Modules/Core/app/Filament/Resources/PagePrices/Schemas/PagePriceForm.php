<?php
namespace Modules\Core\Filament\Resources\PagePrices\Schemas;

use Filament\Forms;
use Filament\Schemas\Schema;

class PagePriceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\Select::make('site_id')
                ->label('Сайт')
                ->options(\App\Models\Site::pluck('domain', 'id'))
                ->required()
                ->searchable(),
            Forms\Components\Select::make('site_page_id')
                ->label('Сторінка (null = дефолт для сайту)')
                ->options(\App\Models\SitePage::pluck('url', 'id'))
                ->searchable()
                ->nullable(),
            Forms\Components\TextInput::make('base_price_per_day')
                ->label('Базова ціна/день ($)')
                ->numeric()
                ->nullable(),
            Forms\Components\TextInput::make('price_link_per_day')
                ->label('Ціна посилання/день ($)')
                ->numeric()
                ->nullable(),
            Forms\Components\TextInput::make('price_onclick_per_day')
                ->label('Ціна onclick/день ($)')
                ->numeric()
                ->nullable(),
            Forms\Components\TextInput::make('price_article_once')
                ->label('Ціна статті разова ($)')
                ->numeric()
                ->nullable(),
            Forms\Components\TextInput::make('price_article_per_day')
                ->label('Ціна статті/день ($)')
                ->numeric()
                ->nullable(),
            Forms\Components\TextInput::make('coef_link')
                ->label('Коефіцієнт посилання')
                ->numeric()
                ->default(1.00),
            Forms\Components\TextInput::make('coef_onclick')
                ->label('Коефіцієнт onclick')
                ->numeric()
                ->default(1.20),
            Forms\Components\TextInput::make('coef_article_once')
                ->label('Коефіцієнт статті разової')
                ->numeric()
                ->default(5.00),
            Forms\Components\TextInput::make('coef_article_daily')
                ->label('Коефіцієнт статті щоденної')
                ->numeric()
                ->default(2.00),
            Forms\Components\TextInput::make('max_placements')
                ->label('Макс. розміщень на сторінці')
                ->numeric()
                ->default(5),
            Forms\Components\Toggle::make('is_public')
                ->label('Публічна сторінка')
                ->default(true),
        ]);
    }
}
