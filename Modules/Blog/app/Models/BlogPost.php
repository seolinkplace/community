<?php
namespace Modules\Blog\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;
class BlogPost extends Model
{
    protected $fillable = [
        'slug', 'title', 'title_en', 'excerpt', 'excerpt_en',
        'content', 'content_en', 'meta_title', 'meta_title_en',
        'meta_description', 'meta_description_en', 'cover_image', 'published_at',
    ];
    protected $casts = [
        'published_at' => 'datetime',
    ];
    public function scopePublished(Builder $query): Builder
    {
        return $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }
    public function isPublished(): bool
    {
        return $this->published_at !== null && $this->published_at->isPast();
    }
    public function getTitle(?string $locale = null): string
    {
        $locale = $locale ?? App::getLocale();
        $col = $locale !== 'uk' ? "title_{$locale}" : null;
        return ($col && isset($this->$col) && $this->$col) ? $this->$col : $this->title;
    }
    public function getExcerpt(?string $locale = null): string
    {
        $locale = $locale ?? App::getLocale();
        $col = $locale !== 'uk' ? "excerpt_{$locale}" : null;
        return ($col && isset($this->$col) && $this->$col) ? $this->$col : ($this->excerpt ?? '');
    }
    public function getContent(?string $locale = null): string
    {
        $locale = $locale ?? App::getLocale();
        $col = $locale !== 'uk' ? "content_{$locale}" : null;
        return ($col && isset($this->$col) && $this->$col) ? $this->$col : $this->content;
    }
    public function getMetaTitle(?string $locale = null): string
    {
        $locale = $locale ?? App::getLocale();
        $col = $locale !== 'uk' ? "meta_title_{$locale}" : null;
        return ($col && isset($this->$col) && $this->$col) ? $this->$col : ($this->meta_title ?? $this->getTitle($locale));
    }
    public function getMetaDescription(?string $locale = null): string
    {
        $locale = $locale ?? App::getLocale();
        $col = $locale !== 'uk' ? "meta_description_{$locale}" : null;
        return ($col && isset($this->$col) && $this->$col) ? $this->$col : ($this->meta_description ?? $this->getExcerpt($locale));
    }
}
