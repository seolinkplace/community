<?php

namespace Modules\Core\Filament\Resources\Webmasters\Pages;

use Modules\Core\Filament\Resources\Webmasters\WebmasterResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewWebmaster extends ViewRecord
{
    protected static string $resource = WebmasterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
