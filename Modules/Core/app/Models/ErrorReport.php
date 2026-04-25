<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ErrorReport extends Model
{
    protected $fillable = [
        'uuid',
        'message',
        'exception_class',
        'file',
        'line',
        'trace',
        'url',
        'method',
        'input',
        'ip',
        'user_agent',
        'user_id',
        'user_type',
        'status',
        'admin_note',
    ];

    protected function casts(): array
    {
        return [
            'input' => 'array',
            'line'  => 'integer',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (self $model): void {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function isNew(): bool
    {
        return $this->status === 'new';
    }

    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    public function scopeUnresolved($query)
    {
        return $query->whereIn('status', ['new', 'seen']);
    }
}
