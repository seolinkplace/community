<?php

namespace Modules\Core\Helpers;

use Modules\Core\Models\UnifiedUser;

class AuthHelper
{
    private static bool $resolved = false;
    private static ?UnifiedUser $user = null;

    private static function resolvedUser(): ?UnifiedUser
    {
        if (!self::$resolved) {
            self::$resolved = true;
            if (auth('unified')->check()) {
                self::$user = auth('unified')->user()->loadMissing('roles');
            }
        }

        return self::$user;
    }

    public static function client(): ?UnifiedUser
    {
        $u = self::resolvedUser();
        if ($u && ($u->hasRole('client') || $u->hasRole('performer'))) {
            return $u;
        }
        return null;
    }

    public static function webmaster(): ?UnifiedUser
    {
        $u = self::resolvedUser();
        return ($u && $u->hasRole('webmaster')) ? $u : null;
    }

    public static function performer(): ?UnifiedUser
    {
        $u = self::resolvedUser();
        return ($u && $u->hasRole('performer')) ? $u : null;
    }

    public static function clientId(): ?int
    {
        return self::client()?->id;
    }

    public static function webmasterId(): ?int
    {
        return self::webmaster()?->id;
    }

    public static function performerId(): ?int
    {
        return self::performer()?->id;
    }
}
