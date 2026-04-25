<?php
namespace Modules\Core\Filament\Resources\BannedDomains\Pages;

use Modules\Core\Filament\Resources\BannedDomains\BannedDomainResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBannedDomain extends CreateRecord
{
    protected static string $resource = BannedDomainResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['banned_by'] = auth()->id();
        $host = parse_url($data['domain'], PHP_URL_HOST) ?? $data['domain'];
        $data['domain'] = preg_replace('/^www\./', '', strtolower($host));
        return $data;
    }
}
