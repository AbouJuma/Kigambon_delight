<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add the Sales_pos permission if it doesn't exist
        DB::table('permissions')->insertOrIgnore([
            'id' => 139,
            'name' => 'Sales_pos',
        ]);

        // Assign Sales_pos permission to all existing roles
        $roles = DB::table('roles')->pluck('id');
        foreach ($roles as $roleId) {
            DB::table('permission_role')->insertOrIgnore([
                'permission_id' => 139,
                'role_id' => $roleId,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the permission and its assignments
        DB::table('permission_role')->where('permission_id', 139)->delete();
        DB::table('permissions')->where('id', 139)->delete();
    }
};
