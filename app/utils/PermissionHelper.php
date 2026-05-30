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
        // Avoid global Cache::forget as it causes race conditions on file cache in production

        // Reload the user's roles & permissions
        $user->load('roles.permissions');

        if (! $user->can($permissionName)) {
            throw new HttpException(403, "Missing permission: {$permissionName}");
        }
    }
}
