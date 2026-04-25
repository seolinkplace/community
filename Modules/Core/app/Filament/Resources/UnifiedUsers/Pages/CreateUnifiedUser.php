<?php

namespace Modules\Core\Filament\Resources\UnifiedUsers\Pages;

use Modules\Core\Filament\Resources\UnifiedUsers\UnifiedUserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUnifiedUser extends CreateRecord
{
    protected static string $resource = UnifiedUserResource::class;
}
