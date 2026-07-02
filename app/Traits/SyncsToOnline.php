<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

trait SyncsToOnline
{
    /**
     * Boot the trait and register Eloquent event listeners.
     */
    public static function bootSyncsToOnline(): void
    {
        static::creating(function (Model $model) {
            if ($model->getConnectionName() !== 'mysql_online') {
                $model->synced_to_online = false;
            }
        });

        static::updating(function (Model $model) {
            // Only flag as unsynced if dirty and the connection is local
            if ($model->getConnectionName() !== 'mysql_online') {
                // If synced_to_online itself is being updated to true, don't revert it
                if ($model->isDirty() && !$model->isDirty('synced_to_online')) {
                    $model->synced_to_online = false;
                }
            }
        });
    }
}
