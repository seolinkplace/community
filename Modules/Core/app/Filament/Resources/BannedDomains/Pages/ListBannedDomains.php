<?php
namespace Modules\Core\Filament\Resources\BannedDomains\Pages;

use Modules\Core\Filament\Resources\BannedDomains\BannedDomainResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBannedDomains extends ListRecords
{
    protected static string $resource = BannedDomainResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()->label('Додати домен')];
    }
}
