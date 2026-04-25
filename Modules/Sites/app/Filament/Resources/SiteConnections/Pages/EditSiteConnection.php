<?php

namespace Modules\Sites\Filament\Resources\SiteConnections\Pages;

use Modules\Sites\Filament\Resources\SiteConnections\SiteConnectionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditSiteConnection extends EditRecord
{
    protected static string $resource = SiteConnectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
