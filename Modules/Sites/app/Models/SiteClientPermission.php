<?php
namespace Modules\Sites\Models;

use App\Models\Client;
use Modules\Sites\Models\Site;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiteClientPermission extends Model
{
    protected $fillable = [
        'site_id',
        'client_id',
        'auto_approve_articles',
    ];

    protected $casts = [
        'auto_approve_articles' => 'boolean',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
