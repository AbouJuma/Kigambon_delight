<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class RolePermissionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Listen for role creation events
        Event::listen('eloquent.created: App\Models\Role', function ($role) {
            $this->assignSalesPosPermission($role);
        });
    }

    /**
     * Assign Sales_pos permission to a role
     */
    private function assignSalesPosPermission(Role $role): void
    {
        $salesPosPermission = DB::table('permissions')->where('name', 'Sales_pos')->first();
        
        if ($salesPosPermission && !DB::table('permission_role')
            ->where('permission_id', $salesPosPermission->id)
            ->where('role_id', $role->id)
            ->exists()) {
            
            DB::table('permission_role')->insert([
                'permission_id' => $salesPosPermission->id,
                'role_id' => $role->id,
            ]);
        }
    }
}
