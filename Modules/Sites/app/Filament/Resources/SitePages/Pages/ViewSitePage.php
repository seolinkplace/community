<?php

namespace Modules\Sites\Filament\Resources\SitePages\Pages;

use Modules\Sites\Filament\Resources\SitePages\SitePageResource;
use Filament\Resources\Pages\ViewRecord;

class ViewSitePage extends ViewRecord
{
    protected static string $resource = SitePageResource::class;

    protected function getHeaderActions(): array
    {
        return []; // Без кнопок
    }
}
