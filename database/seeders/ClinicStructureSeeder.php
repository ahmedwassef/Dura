<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Department;
use Illuminate\Database\Seeder;

class ClinicStructureSeeder extends Seeder
{
    public function run(): void
    {
        Branch::query()->updateOrCreate(
            ['code' => 'zulfi'],
            ['name_ar' => 'فرع الزلفي', 'name_en' => 'Zulfi Branch'],
        );

        Branch::query()->updateOrCreate(
            ['code' => 'dawadmi'],
            ['name_ar' => 'فرع الدوادمي', 'name_en' => 'Dawadmi Branch'],
        );

        Department::query()->updateOrCreate(
            ['code' => 'dental'],
            ['name_ar' => 'قسم الأسنان', 'name_en' => 'Dental Department', 'sort_order' => 1],
        );

        Department::query()->updateOrCreate(
            ['code' => 'dermatology'],
            ['name_ar' => 'قسم الجلدية', 'name_en' => 'Dermatology Department', 'sort_order' => 2],
        );
    }
}
