<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            ModuleSeeder::class,
            WargaSeeder::class,
        ]);

        $this->command->info('🎉 Database seeding completed!');
        $this->command->info('');
        $this->command->info('📊 Seeded data:');
        $this->command->info('   - 3 Users (Admin, Operator, Viewer)');
        $this->command->info('   - 3 Modules (Jamban, RTLH, PAH)');
        $this->command->info('   - 10 Module Questions');
        $this->command->info('   - 5 Warga (dummy data)');
        $this->command->info('');
        $this->command->info('🚀 You can now login and test the system!');
    }
}
