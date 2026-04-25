<?php

namespace Modules\Blog\Filament\Resources\BlogPosts\Pages;

use Modules\Blog\Filament\Resources\BlogPosts\BlogPostResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditBlogPost extends EditRecord
{
    protected static string $resource = BlogPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
