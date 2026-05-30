<?php

namespace App\Observers;

use App\Models\Role;
use Illuminate\Support\Facades\DB;

class RoleObserver
{
    /**
     * Handle the Role "created" event.
     */
    public function created(Role $role): void
    {
        // Automatically assign Sales_pos permission to new roles
        $salesPosPermission = DB::table('permissions')->where('name', 'Sales_pos')->first();
        
        if ($salesPosPermission) {
            DB::table('permission_role')->insert([
                'permission_id' => $salesPosPermission->id,
                'role_id' => $role->id,
            ]);
        }
    }

    /**
     * Handle the Role "updated" event.
     */
    public function updated(Role $role): void
    {
        // You can add logic here if needed for role updates
    }

    /**
     * Handle the Role "deleted" event.
     */
    public function deleted(Role $role): void
    {
        // Clean up permission assignments when role is deleted
        DB::table('permission_role')->where('role_id', $role->id)->delete();
    }

    /**
     * Handle the Role "restored" event.
     */
    public function restored(Role $role): void
    {
        // Restore permissions if role is restored
        $this->created($role);
    }

    /**
     * Handle the Role "force deleted" event.
     */
    public function forceDeleted(Role $role): void
    {
        // Clean up permission assignments when role is force deleted
        DB::table('permission_role')->where('role_id', $role->id)->delete();
    }
}
