<?php

namespace Modules\Blog\Filament\Resources\BlogPosts\Pages;

use Modules\Blog\Filament\Resources\BlogPosts\BlogPostResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewBlogPost extends ViewRecord
{
    protected static string $resource = BlogPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
