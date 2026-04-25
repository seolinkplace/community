<?php
namespace Modules\Core\Filament\Resources\PagePrices\Pages;

use Modules\Core\Filament\Resources\PagePrices\PagePriceResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPagePrice extends EditRecord
{
    protected static string $resource = PagePriceResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
