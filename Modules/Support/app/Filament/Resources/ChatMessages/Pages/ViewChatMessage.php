<?php

namespace Modules\Support\Filament\Resources\ChatMessages\Pages;

use Modules\Support\Filament\Resources\ChatMessages\ChatMessageResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewChatMessage extends ViewRecord
{
    protected static string $resource = ChatMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
