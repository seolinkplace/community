<?php

namespace Modules\Support\Filament\Resources\ChatMessages\Pages;

use Modules\Support\Filament\Resources\ChatMessages\ChatMessageResource;
use Filament\Resources\Pages\CreateRecord;

class CreateChatMessage extends CreateRecord
{
    protected static string $resource = ChatMessageResource::class;
}
