<?php

namespace App\utils;

use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PermissionHelper
{
    /**
     * Ensure the given user has the specified permission.
     * Clears Spatie permission cache for the request to avoid stale data.
     */
    public static function ensurePermission($user, string $permissionName): void
    {
        // Clear Spatie permission cache (cheap for a single request)
        Cache::forget('spatie.permission.cache');

        // Reload the user's roles & permissions
        $user->load('roles.permissions');

        if (! $user->can($permissionName)) {
            throw new HttpException(403, "Missing permission: {$permissionName}");
        }
    }
}
