<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\FormTemplate;
use Illuminate\Database\Seeder;

class FormTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $dental = Department::where('code', 'dental')->firstOrFail();
        $derm   = Department::where('code', 'dermatology')->firstOrFail();

        // Each row: [dept_id, code, category_code, name_ar, name_en, is_bilingual, is_ar_only, sort_order]
        $templates = [
            [$dental->id, 'dental',          null,          'الخطة العلاجية',            'Treatment Plan',                    false, false,  1],
            [$dental->id, 'medhist',         null,          'السجل الصحي',               'Medical History',                   true,  false,  2],
            [$dental->id, 'medreport',       null,          'التقرير الطبي',             'Medical Report',                    true,  false,  3],
            [$dental->id, 'surgery',         'extraction',  'موافقة الإجراءات الطبية',   'Medical Procedures Consent',        true,  false,  4],
            [$dental->id, 'afinash',         'endo',        'موافقة معالجة الجذور',      'Apexification Consent',             false, true,   5],
            [$dental->id, 'rct',             'endo',        'نهاية علاج العصب',          'Root Canal Completion',             false, true,   6],
            [$dental->id, 'veneer',          'prostho',     'موافقة الفينير',            'Veneer Consent',                    false, true,   7],
            [$dental->id, 'crown_rct',       'prostho',     'تاج زركون/إيماكس (RCT خارجي)', 'Zirconia/Emax Crown (External RCT)', false, true, 8],
            [$dental->id, 'crown_implant',   'prostho',     'تاج على زرعة خارجية',      'Crown on External Implant',         false, true,   9],
            [$dental->id, 'ortho',           'ortho',       'عقد التقويم',               'Orthodontic Contract',              false, true,  10],
            [$dental->id, 'ortho_end',       'ortho',       'إقرار إنهاء التقويم',       'Treatment Completion',              false, true,  11],
            [$dental->id, 'ortho_photo',     'ortho',       'موافقة تصوير التقويم',      'Orthodontic Photo Consent',         false, true,  12],
            [$dental->id, 'ortho_extraction','ortho',       'تحويل لخلع الأسنان',        'Extraction Referral',               true,  false, 13],
            [$derm->id,   'proc_card',       'derm_clinic', 'كرت الإجراءات والحقن',      'Procedures & Injections Card',      true,  false, 14],
            [$derm->id,   'roaccutane',      'derm_clinic', 'موافقة الروكتان',           'Roaccutane Consent',                true,  false, 15],
            [$derm->id,   'derm_photo',      'derm_clinic', 'موافقة التصوير',            'Photo Consent',                     false, true,  16],
            [$derm->id,   'laser_hair',      'derm_devices','موافقة الليزر',             'Laser Hair Removal',                true,  false, 17],
            [$derm->id,   'laser_bleach',    'derm_devices','موافقة التشقير',            'Laser Bleaching',                   true,  false, 18],
            [$derm->id,   'self_laser',      'derm_devices','إقرار الليزر الذاتي',       'Self Laser Consent',                false, true,  19],
            [$derm->id,   'morpheus',        'derm_devices','موافقة المورفيس',           'Morpheus Consent',                  true,  false, 20],
        ];

        foreach ($templates as [$deptId, $code, $category, $nameAr, $nameEn, $bilingual, $arOnly, $sort]) {
            FormTemplate::query()->updateOrCreate(
                ['code' => $code],
                [
                    'department_id' => $deptId,
                    'category_code' => $category,
                    'name_ar'       => $nameAr,
                    'name_en'       => $nameEn,
                    'is_bilingual'  => $bilingual,
                    'is_ar_only'    => $arOnly,
                    'sort_order'    => $sort,
                    'is_active'     => true,
                ],
            );
        }
    }
}
