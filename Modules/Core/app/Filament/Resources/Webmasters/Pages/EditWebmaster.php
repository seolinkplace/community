<?php

namespace Modules\Core\Filament\Resources\Webmasters\Pages;

use Modules\Core\Filament\Resources\Webmasters\WebmasterResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditWebmaster extends EditRecord
{
    protected static string $resource = WebmasterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
