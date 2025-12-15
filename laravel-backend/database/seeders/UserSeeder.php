<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin user
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@sid.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Operator user
        User::create([
            'name' => 'Operator',
            'email' => 'operator@sid.com',
            'password' => Hash::make('password'),
            'role' => 'operator',
            'is_active' => true,
        ]);

        // Viewer user
        User::create([
            'name' => 'Viewer',
            'email' => 'viewer@sid.com',
            'password' => Hash::make('password'),
            'role' => 'viewer',
            'is_active' => true,
        ]);

        $this->command->info('✅ 3 users seeded successfully');
        $this->command->info('   - Admin: admin@sid.com / password');
        $this->command->info('   - Operator: operator@sid.com / password');
        $this->command->info('   - Viewer: viewer@sid.com / password');
    }
}
