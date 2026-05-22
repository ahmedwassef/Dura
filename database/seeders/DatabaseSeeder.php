<?php

namespace Database\Seeders;

use App\Enums\RoleName;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ClinicStructureSeeder::class,
            FormCategorySeeder::class,
            FormTemplateSeeder::class,
            FormFieldSeeder::class,
            RolesAndPermissionsSeeder::class,
            SuperAdminSeeder::class,
            ServiceCatalogSeeder::class,
        ]);

        $user = User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
            ]
        );

        $user->assignRole(RoleName::Admin->value);
    }
}
