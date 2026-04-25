<?php

namespace Modules\Sites\Filament\Resources\SiteConnections\Pages;

use Modules\Sites\Filament\Resources\SiteConnections\SiteConnectionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSiteConnections extends ListRecords
{
    protected static string $resource = SiteConnectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
