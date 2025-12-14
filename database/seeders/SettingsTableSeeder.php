<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('settings')->updateOrInsert(
            ['name' => 'system_title'],
            ['value' => 'Digi Docu']
        );

        DB::table('settings')->updateOrInsert(
            ['name' => 'system_description'],
            ['value' => 'School Records Management System']
        );

        DB::table('settings')->updateOrInsert(
            ['name' => 'system_email'],
            ['value' => 'admin@digidocu.local']
        );

        DB::table('settings')->updateOrInsert(
            ['name' => 'system_footer'],
            ['value' => '© ' . date('Y') . ' Digi Docu. All rights reserved.']
        );
    }
}
