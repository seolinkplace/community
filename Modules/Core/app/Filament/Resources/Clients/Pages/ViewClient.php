<?php

namespace Modules\Core\Filament\Resources\Clients\Pages;

use Modules\Core\Filament\Resources\Clients\ClientResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewClient extends ViewRecord
{
    protected static string $resource = ClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
