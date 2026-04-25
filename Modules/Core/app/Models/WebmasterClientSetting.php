<?php
namespace Modules\Core\Models;

use Modules\Core\Models\Webmaster;
use Modules\Core\Models\Client;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebmasterClientSetting extends Model
{
    protected $fillable = [
        'webmaster_id', 'client_id',
        'grace_hours', 'auto_restore', 'granted_balance',
    ];

    protected $casts = [
        'auto_restore'    => 'boolean',
        'granted_balance' => 'decimal:2',
    ];

    public function webmaster(): BelongsTo
    {
        return $this->belongsTo(Webmaster::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
