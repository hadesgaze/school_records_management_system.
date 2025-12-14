<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\User; // since your model is App\User

class SyncUserRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            if (!empty($user->role)) {
                // Sync Spatie role with users.role
                $user->syncRoles([$user->role]);
                $this->command->info("Synced role '{$user->role}' for user: {$user->username}");
            }
        }
    }
}
