<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\FormCategory;
use Illuminate\Database\Seeder;

class FormCategorySeeder extends Seeder
{
    public function run(): void
    {
        $dental = Department::where('code', 'dental')->firstOrFail();
        $derm   = Department::where('code', 'dermatology')->firstOrFail();

        $categories = [
            // Dental sub-categories
            [$dental->id, 'extraction', 'جراحة وخلع', 'Surgery & Extraction', 1],
            [$dental->id, 'endo',       'معالجة العصب',     'Endodontics',         2],
            [$dental->id, 'prostho',    'التركيبات',         'Prosthodontics',      3],
            [$dental->id, 'ortho',      'تقويم الأسنان',    'Orthodontics',        4],

            // Dermatology sub-categories
            [$derm->id,   'derm_clinic',   'العيادة الجلدية',  'Dermatology Clinic',   1],
            [$derm->id,   'derm_devices',  'أجهزة الجلدية',   'Dermatology Devices',  2],
        ];

        foreach ($categories as [$deptId, $code, $nameAr, $nameEn, $sort]) {
            FormCategory::query()->updateOrCreate(
                ['code' => $code],
                [
                    'department_id' => $deptId,
                    'name_ar'       => $nameAr,
                    'name_en'       => $nameEn,
                    'sort_order'    => $sort,
                    'is_active'     => true,
                ],
            );
        }
    }
}
