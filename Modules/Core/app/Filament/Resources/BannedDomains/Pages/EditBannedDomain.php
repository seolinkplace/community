<?php
namespace Modules\Core\Filament\Resources\BannedDomains\Pages;

use Modules\Core\Filament\Resources\BannedDomains\BannedDomainResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBannedDomain extends EditRecord
{
    protected static string $resource = BannedDomainResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
