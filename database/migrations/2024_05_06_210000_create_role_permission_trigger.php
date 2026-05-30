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
        // Create a database trigger to automatically assign Sales_pos permission to new roles
        $triggerSql = "
        CREATE TRIGGER assign_sales_pos_permission_after_role_insert
        AFTER INSERT ON roles
        FOR EACH ROW
        BEGIN
            INSERT IGNORE INTO permission_role (permission_id, role_id)
            SELECT id, NEW.id FROM permissions WHERE name = 'Sales_pos';
        END;
        ";
        
        DB::unprepared($triggerSql);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the trigger
        DB::unprepared('DROP TRIGGER IF EXISTS assign_sales_pos_permission_after_role_insert');
    }
};
