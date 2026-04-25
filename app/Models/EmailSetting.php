<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class EmailSetting extends Model
{
    protected $primaryKey = 'key';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['key', 'enabled', 'label'];
    protected $casts = ['enabled' => 'boolean'];

    public static function isEnabled(string $type): bool
    {
        if (!static::find('all')?->enabled) return false;
        return static::find($type)?->enabled ?? true;
    }
}
