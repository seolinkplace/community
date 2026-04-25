<?php
namespace Modules\Support\Models;
use Illuminate\Database\Eloquent\Model;

class ContactRequest extends Model
{
    protected $fillable = ['name', 'email', 'message', 'token', 'reply', 'replied_at', 'ip'];

    protected $casts = [
        'replied_at' => 'datetime',
    ];
}
