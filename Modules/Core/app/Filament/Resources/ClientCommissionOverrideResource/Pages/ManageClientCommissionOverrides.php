<?php
namespace Modules\Core\Filament\Resources\ClientCommissionOverrideResource\Pages;

use Modules\Core\Filament\Resources\ClientCommissionOverrideResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageClientCommissionOverrides extends ManageRecords
{
    protected static string $resource = ClientCommissionOverrideResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
