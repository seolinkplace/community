<?php
namespace Modules\Core\Filament\Resources\PagePrices\Pages;

use Modules\Core\Filament\Resources\PagePrices\PagePriceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPagePrices extends ListRecords
{
    protected static string $resource = PagePriceResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
