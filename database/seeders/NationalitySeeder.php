<?php

namespace Database\Seeders;

use App\Models\Nationality;
use Illuminate\Database\Seeder;

class NationalitySeeder extends Seeder
{
    public function run(): void
    {
        $nationalities = [
            ['name_ar' => 'سعودي', 'name_en' => 'Saudi', 'code' => 'SA'],
            ['name_ar' => 'مصري', 'name_en' => 'Egyptian', 'code' => 'EG'],
            ['name_ar' => 'سوري', 'name_en' => 'Syrian', 'code' => 'SY'],
            ['name_ar' => 'أردني', 'name_en' => 'Jordanian', 'code' => 'JO'],
            ['name_ar' => 'لبناني', 'name_en' => 'Lebanese', 'code' => 'LB'],
            ['name_ar' => 'فلسطيني', 'name_en' => 'Palestinian', 'code' => 'PS'],
            ['name_ar' => 'يمني', 'name_en' => 'Yemeni', 'code' => 'YE'],
            ['name_ar' => 'سوداني', 'name_en' => 'Sudanese', 'code' => 'SD'],
            ['name_ar' => 'عراقي', 'name_en' => 'Iraqi', 'code' => 'IQ'],
            ['name_ar' => 'كويتي', 'name_en' => 'Kuwaiti', 'code' => 'KW'],
            ['name_ar' => 'إماراتي', 'name_en' => 'Emirati', 'code' => 'AE'],
            ['name_ar' => 'عماني', 'name_en' => 'Omani', 'code' => 'OM'],
            ['name_ar' => 'بحريني', 'name_en' => 'Bahraini', 'code' => 'BH'],
            ['name_ar' => 'قطري', 'name_en' => 'Qatari', 'code' => 'QA'],
            ['name_ar' => 'مغربي', 'name_en' => 'Moroccan', 'code' => 'MA'],
            ['name_ar' => 'جزائري', 'name_en' => 'Algerian', 'code' => 'DZ'],
            ['name_ar' => 'تونسي', 'name_en' => 'Tunisian', 'code' => 'TN'],
            ['name_ar' => 'ليبي', 'name_en' => 'Libyan', 'code' => 'LY'],
            ['name_ar' => 'هندي', 'name_en' => 'Indian', 'code' => 'IN'],
            ['name_ar' => 'باكستاني', 'name_en' => 'Pakistani', 'code' => 'PK'],
            ['name_ar' => 'بنغلاديشي', 'name_en' => 'Bangladeshi', 'code' => 'BD'],
            ['name_ar' => 'فلبيني', 'name_en' => 'Filipino', 'code' => 'PH'],
        ];

        foreach ($nationalities as $nationality) {
            Nationality::updateOrCreate(['code' => $nationality['code']], $nationality);
        }
    }
}
