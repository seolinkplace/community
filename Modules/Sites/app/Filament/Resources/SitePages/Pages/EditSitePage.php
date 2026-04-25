<?php

namespace Modules\Sites\Filament\Resources\SitePages\Pages;

use Modules\Sites\Filament\Resources\SitePages\SitePageResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditSitePage extends EditRecord
{
    protected static string $resource = SitePageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
