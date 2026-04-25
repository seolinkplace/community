<?php
namespace Modules\Core\Models;

use App\Models\Link;
use Modules\Core\Models\TenantToken;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Client extends Authenticatable
{
    use Notifiable;


    protected $fillable = [
        'name', 'email', 'password',
        'chat_banned_at',
        'company_name', 'plan', 'status', 'trial_ends_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'trial_ends_at'     => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function links(): HasMany
    {
        return $this->hasMany(Link::class);
    }


    public function tenantTokens(): HasMany
    {
        return $this->hasMany(TenantToken::class);
    }


}
