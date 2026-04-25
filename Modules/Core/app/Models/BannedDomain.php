<?php
namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;

class BannedDomain extends Model
{
    protected $fillable = ['domain', 'category', 'reason', 'banned_by'];

    public static function isBanned(string $domain): bool
    {
        $host = parse_url($domain, PHP_URL_HOST) ?? $domain;
        $host = preg_replace('/^www\./', '', strtolower($host));
        return static::where('domain', $host)->exists();
    }
}
