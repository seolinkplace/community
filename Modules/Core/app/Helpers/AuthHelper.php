<?php
namespace Modules\Core\Helpers;

use App\Models\Client;
use App\Models\Webmaster;
use Modules\Core\Models\UnifiedUser;

class AuthHelper
{
    public static function client(): UnifiedUser|null
    {
        if (auth('unified')->check()) {
            $u = auth('unified')->user();
            if ($u->hasRole('client') || $u->hasRole('performer')) {
                return $u;
            }
        }
        return null;
    }

    public static function webmaster(): UnifiedUser|null
    {
        if (auth('unified')->check() && auth('unified')->user()->hasRole('webmaster')) {
            return auth('unified')->user();
        }
        return null;
    }

    public static function clientId(): ?int
    {
        if (auth('unified')->check()) {
            $u = auth('unified')->user();
            if ($u->hasRole('client') || $u->hasRole('performer')) {
                return auth('unified')->id();
            }
        }
        return null;
    }

    public static function webmasterId(): ?int
    {
        if (auth('unified')->check() && auth('unified')->user()->hasRole('webmaster')) {
            return auth('unified')->id();
        }
        return null;
    }

    public static function performer(): UnifiedUser|null
    {
        if (auth('unified')->check() && auth('unified')->user()->hasRole('performer')) {
            return auth('unified')->user();
        }
        return null;
    }

    public static function performerId(): ?int
    {
        if (auth('unified')->check() && auth('unified')->user()->hasRole('performer')) {
            return auth('unified')->id();
        }
        return null;
    }
}
