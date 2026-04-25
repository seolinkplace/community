<?php

namespace Modules\Sites\Filament\Resources\Sites\Pages;

use Modules\Sites\Filament\Resources\Sites\SiteResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSite extends ViewRecord
{
    protected static string $resource = SiteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
