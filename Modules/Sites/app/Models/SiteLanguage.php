<?php
namespace Modules\Sites\Models;

use Modules\Sites\Models\Site;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiteLanguage extends Model
{
    public $timestamps = false;
    protected $fillable = ['site_id', 'language_code'];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }
}
