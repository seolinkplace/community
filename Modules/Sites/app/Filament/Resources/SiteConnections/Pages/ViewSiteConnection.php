<?php

namespace Modules\Sites\Filament\Resources\SiteConnections\Pages;

use Modules\Sites\Filament\Resources\SiteConnections\SiteConnectionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSiteConnection extends ViewRecord
{
    protected static string $resource = SiteConnectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
