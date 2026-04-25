<?php

namespace Modules\Sites\Filament\Resources\SitePages\Pages;

use Modules\Sites\Filament\Resources\SitePages\SitePageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSitePages extends ListRecords
{
    protected static string $resource = SitePageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
