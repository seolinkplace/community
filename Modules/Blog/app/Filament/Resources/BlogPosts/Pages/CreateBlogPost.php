<?php

namespace Modules\Blog\Filament\Resources\BlogPosts\Pages;

use Modules\Blog\Filament\Resources\BlogPosts\BlogPostResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBlogPost extends CreateRecord
{
    protected static string $resource = BlogPostResource::class;
}
