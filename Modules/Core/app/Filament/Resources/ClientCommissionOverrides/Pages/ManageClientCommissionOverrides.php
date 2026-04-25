<?php

namespace Modules\Core\Filament\Resources\ClientCommissionOverrides\Pages;

use Modules\Core\Filament\Resources\ClientCommissionOverrides\ClientCommissionOverrideResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageClientCommissionOverrides extends ManageRecords
{
    protected static string $resource = ClientCommissionOverrideResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
