<?php

namespace Modules\Core\Filament\Resources\Webmasters\Pages;

use Modules\Core\Filament\Resources\Webmasters\WebmasterResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWebmasters extends ListRecords
{
    protected static string $resource = WebmasterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
