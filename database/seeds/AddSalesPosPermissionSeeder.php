<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddSalesPosPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Check if the permission already exists
        $existing = DB::table('permissions')->where('name', 'Sales_pos')->first();
        
        if (!$existing) {
            DB::table('permissions')->insert([
                'id'   => 139,
                'name' => 'Sales_pos',
            ]);
            $this->command->info('Sales_pos permission added successfully.');
        } else {
            $this->command->info('Sales_pos permission already exists.');
        }
    }
}
