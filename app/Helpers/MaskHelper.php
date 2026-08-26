<?php

namespace App\Helpers;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
class MaskHelper
{    
    public static function mask($value, $visible = 1)
    {
        if (empty($value)) {
            return $value;
        }

        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (!$user) {
            return $value;
        }
        $isAdmin = $user->is_admin == 1
            || $user->hasAnyRole(['webadmin', 'admin']);

        if ($isAdmin) {
            return $value;
        }
        $isReviewer = $user->hasRole('abstract-reviewer');
        if (!$isReviewer) {
            return $value;
        }
        $length = mb_strlen($value);
        if ($length <= $visible) {
            return $value;
        }
        return mb_substr($value, 0, $visible)
            . str_repeat('*', $length - $visible);
    }

    public static function maskLimit($value, $limit = 20, $visible = 2)
    {
        if (empty($value)) {
            return $value;
        }
        $value = Str::limit($value, $limit);
        return self::mask($value, $visible);
    }
}