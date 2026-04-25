<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
class Setting extends Model
{
    protected $primaryKey = 'key';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['key', 'value', 'type', 'label'];

    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember("setting_{$key}", 300, function() use ($key, $default) {
            $s = static::find($key);
            if (!$s) return $default;
            return match($s->type) {
                'bool' => (bool)(int)$s->value,
                'int'  => (int)$s->value,
                default => $s->value,
            };
        });
    }

    public static function set(string $key, mixed $value): void
    {
        static::where('key', $key)->update(['value' => (string)(int)$value]);
        Cache::forget("setting_{$key}");
    }
}
