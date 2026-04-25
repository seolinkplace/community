<?php
namespace Modules\Core\Filament\Resources\PagePrices;

use Modules\Core\Filament\Resources\PagePrices\Pages\CreatePagePrice;
use Modules\Core\Filament\Resources\PagePrices\Pages\EditPagePrice;
use Modules\Core\Filament\Resources\PagePrices\Pages\ListPagePrices;
use Modules\Core\Filament\Resources\PagePrices\Schemas\PagePriceForm;
use Modules\Core\Filament\Resources\PagePrices\Tables\PagePricesTable;
use App\Models\PagePrice;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PagePriceResource extends Resource
{
    public static function getNavigationGroup(): ?string { return 'Сайти'; }
    public static function getNavigationSort(): ?int { return 4; }

    protected static ?string $model = PagePrice::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::CurrencyDollar;
    protected static ?string $navigationLabel = 'Page Prices';
    
    public static function form(Schema $schema): Schema
    {
        return PagePriceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PagePricesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListPagePrices::route('/'),
            'create' => CreatePagePrice::route('/create'),
            'edit'   => EditPagePrice::route('/{record}/edit'),
        ];
    }
}
