<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AdminUser;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default admin user
        AdminUser::create([
            'username' => 'noc_sejahtera',
            'name' => 'Admin NOC Sejahtera',
            'email' => 'noc@bmkg.go.id',
            'password' => 'nocbmkg123', // Will be automatically hashed by the model
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Optionally create a super admin
        AdminUser::create([
            'username' => 'superadmin',
            'name' => 'Super Administrator',
            'email' => 'admin@bmkg.go.id',
            'password' => 'admin123', // Will be automatically hashed by the model
            'role' => 'superadmin',
            'is_active' => true,
        ]);
    }
}
