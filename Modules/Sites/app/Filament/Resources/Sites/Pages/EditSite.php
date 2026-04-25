<?php

namespace Modules\Sites\Filament\Resources\Sites\Pages;

use Modules\Sites\Filament\Resources\Sites\SiteResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditSite extends EditRecord
{
    protected static string $resource = SiteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
