<?php
namespace Modules\Core\Models;
use Illuminate\Database\Eloquent\Model;
class ApplyRequest extends Model
{
    protected $fillable = ['name', 'email', 'role', 'site', 'message', 'locale'];
}
