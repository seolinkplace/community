<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class PlatformSetting extends Model
{
    protected $fillable = ['key', 'value', 'description'];

    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember("platform_setting:{$key}", 300, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("platform_setting:{$key}");
    }

    public static function taskModerationMode(): string
    {
        return static::get('task_moderation_mode', 'new_only');
    }
}
