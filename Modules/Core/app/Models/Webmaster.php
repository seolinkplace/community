<?php

namespace Modules\Core\Models;

use Modules\Sites\Models\Site;
use App\Models\WebmasterWithdrawal;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Webmaster extends Authenticatable
{
    use Notifiable;


    protected $fillable = [
        'name', 'email', 'password',
        'chat_banned_at',
        'status', 'verification_token', 'plan', 'freeze_disabled',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function sites(): HasMany
    {
        return $this->hasMany(Site::class);
    }

    public function withdrawals(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(WebmasterWithdrawal::class);
    }

}
