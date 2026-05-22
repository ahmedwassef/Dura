<?php

namespace Database\Seeders;

use App\Models\FormTemplate;
use App\Models\FormField;
use Illuminate\Database\Seeder;

class FormFieldSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Surgery Form Fields (Medical Procedures Consent)
        $surgery = FormTemplate::where('code', 'surgery')->first();
        if ($surgery) {
            $surgery->fields()->delete();
            $surgery->fields()->createMany([
                [
                    'type' => 'section',
                    'label_ar' => 'تفاصيل الإجراء الجراحي',
                    'label_en' => 'Surgical Procedure Details',
                    'content_ar' => 'الرجاء ملء تفاصيل الإجراء الجراحي وتحديد خيارات التخدير.',
                    'content_en' => 'Please fill in the procedure details and select anesthesia options.',
                    'sort_order' => 10,
                ],
                [
                    'type' => 'text',
                    'key' => 'procedure_name',
                    'label_ar' => 'اسم الإجراء الجراحي المقترح',
                    'label_en' => 'Proposed Surgical Procedure Name',
                    'placeholder_ar' => 'مثال: خلع سن جراحي',
                    'placeholder_en' => 'e.g. Surgical tooth extraction',
                    'is_required' => true,
                    'settings' => ['width' => 'half'],
                    'sort_order' => 20,
                ],
                [
                    'type' => 'date',
                    'key' => 'procedure_date',
                    'label_ar' => 'تاريخ العملية المقترح',
                    'label_en' => 'Proposed Procedure Date',
                    'is_required' => true,
                    'settings' => ['width' => 'half'],
                    'sort_order' => 30,
                ],
                [
                    'type' => 'select',
                    'key' => 'anesthesia_type',
                    'label_ar' => 'نوع التخدير المطلوب',
                    'label_en' => 'Required Anesthesia Type',
                    'is_required' => true,
                    'options' => [
                        ['value' => 'local', 'label_ar' => 'تخدير موضعي', 'label_en' => 'Local Anesthesia'],
                        ['value' => 'general', 'label_ar' => 'تخدير كلي', 'label_en' => 'General Anesthesia'],
                        ['value' => 'sedation', 'label_ar' => 'تنويم مغناطيسي / إرخاء', 'label_en' => 'Conscious Sedation'],
                    ],
                    'settings' => ['width' => 'full'],
                    'sort_order' => 40,
                ],
                [
                    'type' => 'section',
                    'label_ar' => 'الإقرار الجراحي والتواقيع',
                    'label_en' => 'Surgical Consent & Signatures',
                    'sort_order' => 50,
                ],
                [
                    'type' => 'consent',
                    'key' => 'surgical_consent_declaration',
                    'label_ar' => 'إقرار المريض بالعملية الجراحية والمخاطر',
                    'label_en' => 'Patient surgical consent and risk acknowledgment',
                    'content_ar' => 'أقر أنا الموقع أدناه بموافقتي الكاملة على إجراء العملية الجراحية الموضحة أعلاه، وقد تم إيضاح طبيعة الإجراء الجراحي ونوع التخدير والمخاطر أو المضاعفات الجانبية المحتملة لي من قبل الطبيب المعالج.',
                    'content_en' => 'I, the undersigned, hereby consent to the proposed surgical procedure, having had its nature, anesthesia type, and potential complications explained to me by the treating physician.',
                    'sort_order' => 60,
                ],
                [
                    'type' => 'signature',
                    'key' => 'patient',
                    'label_ar' => 'توقيع المريض أو ولي أمره',
                    'label_en' => 'Signature of Patient / Guardian',
                    'is_required' => true,
                    'settings' => ['width' => 'half'],
                    'sort_order' => 70,
                ],
                [
                    'type' => 'signature',
                    'key' => 'doctor',
                    'label_ar' => 'توقيع الطبيب المعالج والختم',
                    'label_en' => 'Signature & Stamp of Doctor',
                    'is_required' => true,
                    'settings' => ['width' => 'half'],
                    'sort_order' => 80,
                ],
            ]);
        }

        // 2. Laser Hair Removal Consent Fields
        $laser = FormTemplate::where('code', 'laser_hair')->first();
        if ($laser) {
            $laser->fields()->delete();
            $laser->fields()->createMany([
                [
                    'type' => 'section',
                    'label_ar' => 'سجل الحالة والتاريخ الصحي لليزر',
                    'label_en' => 'Laser Health History & Status',
                    'sort_order' => 10,
                ],
                [
                    'type' => 'select',
                    'key' => 'skin_type',
                    'label_ar' => 'درجة ولون البشرة (Skin Type)',
                    'label_en' => 'Fitzpatrick Skin Type',
                    'is_required' => true,
                    'options' => [
                        ['value' => 'type_1', 'label_ar' => 'بشرة بيضاء جداً (تحترق دائماً)', 'label_en' => 'Type I (Very fair, always burns)'],
                        ['value' => 'type_2', 'label_ar' => 'بشرة بيضاء (تحترق بسهولة)', 'label_en' => 'Type II (Fair, burns easily)'],
                        ['value' => 'type_3', 'label_ar' => 'بشرة حنطية فاتحة', 'label_en' => 'Type III (Medium, sometimes burns)'],
                        ['value' => 'type_4', 'label_ar' => 'بشرة حنطية داكنة (نادراً ما تحترق)', 'label_en' => 'Type IV (Olive, rarely burns)'],
                        ['value' => 'type_5', 'label_ar' => 'بشرة سمراء', 'label_en' => 'Type V (Brown)'],
                    ],
                    'settings' => ['width' => 'half'],
                    'sort_order' => 20,
                ],
                [
                    'type' => 'radio',
                    'key' => 'tanned_recently',
                    'label_ar' => 'هل تعرضت للتسمير (تسمير الشمس أو سولاريوم) خلال الـ 4 أسابيع الماضية؟',
                    'label_en' => 'Have you had active sun exposure or tanning beds in the last 4 weeks?',
                    'is_required' => true,
                    'options' => [
                        ['value' => 'yes', 'label_ar' => 'نعم', 'label_en' => 'Yes'],
                        ['value' => 'no', 'label_ar' => 'لا', 'label_en' => 'No'],
                    ],
                    'settings' => ['width' => 'half'],
                    'sort_order' => 30,
                ],
                [
                    'type' => 'textarea',
                    'key' => 'medications_active',
                    'label_ar' => 'أدرج أي أدوية تستخدمها حالياً (خاصة أدوية الحساسية للضوء أو كورتيزون)',
                    'label_en' => 'List any medications you currently use (especially photosensitive drugs or cortisone)',
                    'placeholder_ar' => 'اكتب لا يوجد إن لم يكن هناك أدوية',
                    'placeholder_en' => 'Write none if not taking any',
                    'is_required' => false,
                    'settings' => ['width' => 'full', 'rows' => 3],
                    'sort_order' => 40,
                ],
                [
                    'type' => 'section',
                    'label_ar' => 'الإقرار والتوقيع لليزر',
                    'label_en' => 'Laser Consent & Signatures',
                    'sort_order' => 50,
                ],
                [
                    'type' => 'consent',
                    'key' => 'laser_consent_statement',
                    'label_ar' => 'إقرار وتعهد جلسة إزالة الشعر بالليزر',
                    'label_en' => 'Laser Hair Removal Agreement Statement',
                    'content_ar' => 'أوافق على الخضوع لجلسة إزالة الشعر بالليزر، وأقر بأنني أطلعت على تعليمات ما قبل وما بعد الجلسة، وأنني سألتزم باستخدام واقي الشمس وتجنب العطور والساونا لمدة 3 أيام، وأتفهم حدوث احمرار طفيف أو تهيج للبشرة كعرض طبيعي ومؤقت بعد الجلسة.',
                    'content_en' => 'I agree to undergo the laser hair removal treatment. I acknowledge reading pre and post-session guidelines, including sunscreen use and avoiding perfumes/saunas for 3 days. I understand that temporary redness or skin irritation is a normal reaction.',
                    'sort_order' => 60,
                ],
                [
                    'type' => 'signature',
                    'key' => 'patient',
                    'label_ar' => 'توقيع المريضة / المراجع',
                    'label_en' => 'Patient Signature',
                    'is_required' => true,
                    'settings' => ['width' => 'half'],
                    'sort_order' => 70,
                ],
                [
                    'type' => 'signature',
                    'key' => 'doctor',
                    'label_ar' => 'توقيع الأخصائية المعالجة',
                    'label_en' => 'Practitioner Signature',
                    'is_required' => true,
                    'settings' => ['width' => 'half'],
                    'sort_order' => 80,
                ],
            ]);
        }

        // 3. Laser Bleaching Consent Fields
        $bleach = FormTemplate::where('code', 'laser_bleach')->first();
        if ($bleach) {
            $bleach->fields()->delete();
            $bleach->fields()->createMany([
                [
                    'type' => 'section',
                    'label_ar' => 'سجل الحالة والتاريخ الطبي لتشقير الحواجب / الشعر بالليزر',
                    'label_en' => 'Laser Bleaching Health History & Status',
                    'sort_order' => 10,
                ],
                [
                    'type' => 'radio',
                    'key' => 'bleached_before',
                    'label_ar' => 'هل قمت بعمل تشقير بالليزر سابقاً؟',
                    'label_en' => 'Have you undergone laser bleaching before?',
                    'is_required' => true,
                    'options' => [
                        ['value' => 'yes', 'label_ar' => 'نعم', 'label_en' => 'Yes'],
                        ['value' => 'no', 'label_ar' => 'لا', 'label_en' => 'No'],
                    ],
                    'settings' => ['width' => 'half'],
                    'sort_order' => 20,
                ],
                [
                    'type' => 'select',
                    'key' => 'skin_sensitivity',
                    'label_ar' => 'حساسية الجلد للمواد الكيميائية أو الحرارة',
                    'label_en' => 'Skin sensitivity to chemical agents or heat',
                    'is_required' => true,
                    'options' => [
                        ['value' => 'normal', 'label_ar' => 'بشرة عادية (غير حساسة)', 'label_en' => 'Normal Skin'],
                        ['value' => 'sensitive', 'label_ar' => 'بشرة حساسة', 'label_en' => 'Sensitive Skin'],
                        ['value' => 'hypersensitive', 'label_ar' => 'بشرة شديدة الحساسية والتهيج', 'label_en' => 'Hyper-sensitive Skin'],
                    ],
                    'settings' => ['width' => 'half'],
                    'sort_order' => 30,
                ],
                [
                    'type' => 'section',
                    'label_ar' => 'الإقرار والتوقيع لجلسة التشقير',
                    'label_en' => 'Laser Bleaching Consent & Signatures',
                    'sort_order' => 40,
                ],
                [
                    'type' => 'consent',
                    'key' => 'bleach_consent_statement',
                    'label_ar' => 'إقرار وتعهد جلسة تشقير الحواجب / الشعر بالليزر',
                    'label_en' => 'Laser Bleaching Agreement Statement',
                    'content_ar' => 'أوافق على الخضوع لجلسة التشقير بالليزر، وأقر بأنني أعلم أن التشقير يؤدي لتفتيح لون الشعر بشكل مؤقت وقد يسبب احمراراً أو تهيجاً خفيفاً يزول خلال ساعات، وألتزم تماماً بالتعليمات الموصى بها.',
                    'content_en' => 'I consent to undergo the laser bleaching treatment. I understand it temporarily lightens hair color and may cause minor redness or irritation that resolves within hours. I agree to follow all post-session guidelines.',
                    'sort_order' => 50,
                ],
                [
                    'type' => 'signature',
                    'key' => 'patient',
                    'label_ar' => 'توقيع المريضة / المراجع',
                    'label_en' => 'Patient Signature',
                    'is_required' => true,
                    'settings' => ['width' => 'half'],
                    'sort_order' => 60,
                ],
                [
                    'type' => 'signature',
                    'key' => 'doctor',
                    'label_ar' => 'توقيع الأخصائية المعالجة',
                    'label_en' => 'Practitioner Signature',
                    'is_required' => true,
                    'settings' => ['width' => 'half'],
                    'sort_order' => 70,
                ],
            ]);
        }

        // 4. Apexification Consent (afinash - موافقة معالجة الجذور)
        $afinash = FormTemplate::where('code', 'afinash')->first();
        if ($afinash) {
            $afinash->fields()->delete();
            $afinash->fields()->createMany([
                [
                    'type' => 'section',
                    'label_ar' => 'بيانات إجراء معالجة قمة الجذور المفتوحة للأطفال',
                    'label_en' => 'Apexification Procedure Details',
                    'sort_order' => 10,
                ],
                [
                    'type' => 'text',
                    'key' => 'tooth_number',
                    'label_ar' => 'رقم السن المستهدف للعلاج',
                    'label_en' => 'Target Tooth Number',
                    'placeholder_ar' => 'مثال: 11, 21, 46',
                    'placeholder_en' => 'e.g. 11, 21, 46',
                    'is_required' => true,
                    'settings' => ['width' => 'half'],
                    'sort_order' => 20,
                ],
                [
                    'type' => 'checkbox',
                    'key' => 'symptoms',
                    'label_ar' => 'الأعراض المصاحبة للسن حالياً',
                    'label_en' => 'Current Active Symptoms',
                    'options' => [
                        ['value' => 'pain', 'label_ar' => 'ألم مستمر أو مفاجئ', 'label_en' => 'Spontaneous Pain'],
                        ['value' => 'swelling', 'label_ar' => 'تورم أو انتفاخ باللثة', 'label_en' => 'Gingival Swelling'],
                        ['value' => 'mobility', 'label_ar' => 'حركة خفيفة بالسن', 'label_en' => 'Tooth Mobility'],
                    ],
                    'settings' => ['width' => 'half'],
                    'sort_order' => 30,
                ],
                [
                    'type' => 'consent',
                    'key' => 'apex_consent',
                    'label_ar' => 'إقرار الموافقة على علاج جذور الأسنان وفتح القنوات المفتوحة',
                    'label_en' => 'Apexification Treatment & Dental Canal Therapy Consent',
                    'content_ar' => 'أقر بموافقتي على إجراء علاج قمة جذور السن الموضح أعلاه، وتلقي المواد اللازمة لتحفيز إغلاق ذروة السن المفتوحة، كما تفهمت ضرورة الزيارات الدورية لإكمال الحشوات والمتابعة.',
                    'content_en' => 'I hereby consent to the Apexification procedure for the target tooth, including materials placement for apex closure. I understand the requirement for sequential treatment visits and X-ray checks.',
                    'sort_order' => 40,
                ],
                [
                    'type' => 'signature',
                    'key' => 'patient',
                    'label_ar' => 'توقيع ولي أمر المريض (للأطفال)',
                    'label_en' => 'Guardian Signature',
                    'is_required' => true,
                    'settings' => ['width' => 'half'],
                    'sort_order' => 50,
                ],
                [
                    'type' => 'signature',
                    'key' => 'doctor',
                    'label_ar' => 'توقيع طبيب الأسنان المعالج',
                    'label_en' => 'Dentist Signature',
                    'is_required' => true,
                    'settings' => ['width' => 'half'],
                    'sort_order' => 60,
                ],
            ]);
        }

        // 5. Root Canal Completion (rct - نهاية علاج العصب)
        $rct = FormTemplate::where('code', 'rct')->first();
        if ($rct) {
            $rct->fields()->delete();
            $rct->fields()->createMany([
                [
                    'type' => 'section',
                    'label_ar' => 'سجل إتمام حشو عصب السن النهائي',
                    'label_en' => 'Root Canal Treatment Obturation Details',
                    'sort_order' => 10,
                ],
                [
                    'type' => 'text',
                    'key' => 'completed_tooth',
                    'label_ar' => 'رقم السن الذي تم إنهاء حشوه',
                    'label_en' => 'Treated Tooth Number',
                    'placeholder_ar' => 'مثال: 36, 47',
                    'placeholder_en' => 'e.g. 36, 47',
                    'is_required' => true,
                    'settings' => ['width' => 'half'],
                    'sort_order' => 20,
                ],
                [
                    'type' => 'date',
                    'key' => 'completion_date',
                    'label_ar' => 'تاريخ حشو القنوات النهائي',
                    'label_en' => 'Canal Obturation Date',
                    'is_required' => true,
                    'settings' => ['width' => 'half'],
                    'sort_order' => 30,
                ],
                [
                    'type' => 'consent',
                    'key' => 'rct_consent',
                    'label_ar' => 'إقرار إنهاء علاج العصب والالتزام بتركيب التاج / التلبيسة لحماية السن',
                    'label_en' => 'Root Canal Obturation Compliance & Crown Placement Requirement',
                    'content_ar' => 'أقر بأن الطبيب المعالج قد أتم حشو عصب السن بنجاح، وأتفهم تماماً أن السن المعالج يصبح ضعيفاً وقابلاً للكسر بسهولة، ولذلك ألتزم بتركيب التاج (التلبيسة) أو الحشوة التجميلية الكبرى خلال 14 يوماً لحماية السن، وأتحمل مسؤولية أي كسر يطرأ على السن في حال تأخري.',
                    'content_en' => 'I acknowledge that the root canal obturation has been completed. I understand that the treated tooth is now brittle and highly prone to fractures. I commit to placing a permanent restoration or crown within 14 days and hold no clinic responsibility for fractures due to delay.',
                    'sort_order' => 40,
                ],
                [
                    'type' => 'signature',
                    'key' => 'patient',
                    'label_ar' => 'توقيع المريض أو ولي أمره',
                    'label_en' => 'Patient Signature',
                    'is_required' => true,
                    'settings' => ['width' => 'half'],
                    'sort_order' => 50,
                ],
                [
                    'type' => 'signature',
                    'key' => 'doctor',
                    'label_ar' => 'توقيع أخصائي علاج العصب',
                    'label_en' => 'Endodontist Signature',
                    'is_required' => true,
                    'settings' => ['width' => 'half'],
                    'sort_order' => 60,
                ],
            ]);
        }

        // 6. Veneer Consent (veneer - موافقة الفينير)
        $veneer = FormTemplate::where('code', 'veneer')->first();
        if ($veneer) {
            $veneer->fields()->delete();
            $veneer->fields()->createMany([
                [
                    'type' => 'section',
                    'label_ar' => 'خطة وتفاصيل عدسات الفينير التجميلية',
                    'label_en' => 'Veneers Plan & Aesthetic Details',
                    'sort_order' => 10,
                ],
                [
                    'type' => 'select',
                    'key' => 'veneer_shade',
                    'label_ar' => 'درجة ولون الفينير المطلوب (Shade Code)',
                    'label_en' => 'Requested Veneer Shade',
                    'is_required' => true,
                    'options' => [
                        ['value' => 'bl1', 'label_ar' => 'أبيض ناصع جداً (BL1)', 'label_en' => 'Bleach Shade BL1'],
                        ['value' => 'bl2', 'label_ar' => 'أبيض طبيعي ناصع (BL2)', 'label_en' => 'Bleach Shade BL2'],
                        ['value' => 'om3', 'label_ar' => 'أبيض لؤلؤي (OM3)', 'label_en' => 'Pearl White OM3'],
                        ['value' => 'a1', 'label_ar' => 'أبيض طبيعي (A1)', 'label_en' => 'Natural White A1'],
                    ],
                    'settings' => ['width' => 'half'],
                    'sort_order' => 20,
                ],
                [
                    'type' => 'text',
                    'key' => 'teeth_count',
                    'label_ar' => 'عدد الأسنان والضروس المستهدفة بالعدسات',
                    'label_en' => 'Total Number of Targeted Teeth',
                    'placeholder_ar' => 'مثال: 8 أسنان علوية، 8 سفلية',
                    'placeholder_en' => 'e.g. 8 upper, 8 lower',
                    'is_required' => true,
                    'settings' => ['width' => 'half'],
                    'sort_order' => 30,
                ],
                [
                    'type' => 'consent',
                    'key' => 'veneer_consent',
                    'label_ar' => 'إقرار وتعهد تركيب عدسات الأسنان اللاصقة (الفينير / اللومينير)',
                    'label_en' => 'Veneers Cosmetic Application Agreement',
                    'content_ar' => 'أقر بأنني وافقت على خطة العلاج التجميلي بالعدسات الموضحة أعلاه، وتم توضيح الحاجة إلى برد خفيف للأسنان ونوع المادة المستخدمة ولونها. وأتعهد بالالتزام بتعليمات العناية اليومية وتجنب الأطعمة القاسية جداً لحماية العدسات من التصدع.',
                    'content_en' => 'I agree to the cosmetic veneer treatment plan. I understand the tooth preparation required and color options chosen. I promise to adhere to daily hygiene instructions and avoid biting hard substances to protect the veneers.',
                    'sort_order' => 40,
                ],
                [
                    'type' => 'signature',
                    'key' => 'patient',
                    'label_ar' => 'توقيع المراجع للمطابقة والتلوين',
                    'label_en' => 'Patient Shade & Fit Approval Signature',
                    'is_required' => true,
                    'settings' => ['width' => 'half'],
                    'sort_order' => 50,
                ],
                [
                    'type' => 'signature',
                    'key' => 'doctor',
                    'label_ar' => 'توقيع طبيب أسنان التجميل',
                    'label_en' => 'Cosmetic Dentist Signature',
                    'is_required' => true,
                    'settings' => ['width' => 'half'],
                    'sort_order' => 60,
                ],
            ]);
        }

        // 7. Zirconia/Emax Crown - External RCT (crown_rct - تاج زركون/إيماكس)
        $crown_rct = FormTemplate::where('code', 'crown_rct')->first();
        if ($crown_rct) {
            $crown_rct->fields()->delete();
            $crown_rct->fields()->createMany([
                [
                    'type' => 'section',
                    'label_ar' => 'بيانات ومادة التاج لسن معالج خارجياً',
                    'label_en' => 'Crown on External RCT Placement Details',
                    'sort_order' => 10,
                ],
                [
                    'type' => 'select',
                    'key' => 'crown_material',
                    'label_ar' => 'نوع ومادة التاج التجميلي المطلوب',
                    'label_en' => 'Selected Crown Material',
                    'is_required' => true,
                    'options' => [
                        ['value' => 'zirconia', 'label_ar' => 'زركون كامل (Full Monolithic Zirconia)', 'label_en' => 'Full Zirconia'],
                        ['value' => 'emax', 'label_ar' => 'إيماكس عالي الشفافية (IPS e.max)', 'label_en' => 'Lithium Disilicate (E.max)'],
                        ['value' => 'porcelain_fused', 'label_ar' => 'خزف مصهور على معدن (PFM)', 'label_en' => 'Porcelain Fused to Metal'],
                    ],
                    'settings' => ['width' => 'half'],
                    'sort_order' => 20,
                ],
                [
                    'type' => 'text',
                    'key' => 'external_rct_source',
                    'label_ar' => 'الجهة / العيادة الخارجية التي أتمت علاج العصب للسن',
                    'label_en' => 'External Clinic or Dentist of RCT Treatment',
                    'placeholder_ar' => 'مثال: مستوصف حكومي، عيادة خارج الرياض',
                    'placeholder_en' => 'e.g. Public clinic, another local facility',
                    'is_required' => true,
                    'settings' => ['width' => 'half'],
                    'sort_order' => 30,
                ],
                [
                    'type' => 'consent',
                    'key' => 'crown_rct_consent',
                    'label_ar' => 'إقرار وتعهد تركيب التاج (التلبيسة) على سن معالج عصب خارج مجمعاتنا',
                    'label_en' => 'External RCT Crown Placement Consent',
                    'content_ar' => 'أفهم وأوافق على أن تركيب التاج يتم على سن تم معالجة عصبه في مركز خارجي. وأقر بأن العيادة لا تضمن جودة أو استمرار نجاح حشو العصب الخارجي، وفي حال حدوث خراج أو فشل مستقبلي لحشو العصب، فإن إعادة علاج العصب والتاج ستكون على نفقتي الخاصة.',
                    'content_en' => 'I consent to placing the crown on a tooth treated elsewhere. I agree that the clinic does not guarantee the external RCT. Should the root canal fail or develop pathology, any retreatment and new crown fabrication will be entirely at my own expense.',
                    'sort_order' => 40,
                ],
                [
                    'type' => 'signature',
                    'key' => 'patient',
                    'label_ar' => 'توقيع المراجع للموافقة على الشرط',
                    'label_en' => 'Patient Signature',
                    'is_required' => true,
                    'settings' => ['width' => 'half'],
                    'sort_order' => 50,
                ],
                [
                    'type' => 'signature',
                    'key' => 'doctor',
                    'label_ar' => 'توقيع الطبيب المعالج والختم',
                    'label_en' => 'Dentist Signature',
                    'is_required' => true,
                    'settings' => ['width' => 'half'],
                    'sort_order' => 60,
                ],
            ]);
        }

        // 8. Crown on External Implant (crown_implant - تاج على زرعة خارجية)
        $crown_implant = FormTemplate::where('code', 'crown_implant')->first();
        if ($crown_implant) {
            $crown_implant->fields()->delete();
            $crown_implant->fields()->createMany([
                [
                    'type' => 'section',
                    'label_ar' => 'تفاصيل الزرعة الأسنان الخارجية والدعامة',
                    'label_en' => 'External Implant & Abutment Details',
                    'sort_order' => 10,
                ],
                [
                    'type' => 'text',
                    'key' => 'implant_brand',
                    'label_ar' => 'اسم شركة ومقاس الزرعة الخارجية (إن وجد)',
                    'label_en' => 'External Implant Brand, System & Size',
                    'placeholder_ar' => 'مثال: Straumann, Nobel Biocare 4.3mm',
                    'placeholder_en' => 'e.g. Straumann, Nobel Biocare 4.3mm',
                    'is_required' => true,
                    'settings' => ['width' => 'half'],
                    'sort_order' => 20,
                ],
                [
                    'type' => 'select',
                    'key' => 'connection_type',
                    'label_ar' => 'طريقة توصيل وتثبيت التاج',
                    'label_en' => 'Abutment Retention / Connection Type',
                    'is_required' => true,
                    'options' => [
                        ['value' => 'screw_retained', 'label_ar' => 'مثبت ببرغي (Screw Retained)', 'label_en' => 'Screw-Retained Crown'],
                        ['value' => 'cement_retained', 'label_ar' => 'مثبت بإسمنت (Cement Retained)', 'label_en' => 'Cement-Retained Crown'],
                    ],
                    'settings' => ['width' => 'half'],
                    'sort_order' => 30,
                ],
                [
                    'type' => 'consent',
                    'key' => 'crown_implant_consent',
                    'label_ar' => 'إقرار وتعهد تركيب التاج التجميلي على زرعة أسنان خارجية',
                    'label_en' => 'External Implant Crown Placement Consent',
                    'content_ar' => 'أفهم تماماً بأنني أخضع لتركيب تاج أسنان على زرعة داخلية تم وضعها في عيادة خارجية. وأقر بأن العيادة غير مسؤولة تماماً عن ثبات أو فشل أو ذوبان العظم المحيط بالزرعة الخارجية، كما أن توفير قطع الغيار المتوافقة مع الزرعة هو مسؤوليتي الكاملة.',
                    'content_en' => 'I acknowledge that the implant fixture was placed at another facility. I hold the clinic free of any liability regarding the osseointegration status or failure of the external implant. Sourcing any brand-specific custom components is my sole responsibility.',
                    'sort_order' => 40,
                ],
                [
                    'type' => 'signature',
                    'key' => 'patient',
                    'label_ar' => 'توقيع المريض بالموافقة التامة',
                    'label_en' => 'Patient Signature',
                    'is_required' => true,
                    'settings' => ['width' => 'half'],
                    'sort_order' => 50,
                ],
                [
                    'type' => 'signature',
                    'key' => 'doctor',
                    'label_ar' => 'توقيع طبيب التركيبات المعالج',
                    'label_en' => 'Prosthodontist Signature',
                    'is_required' => true,
                    'settings' => ['width' => 'half'],
                    'sort_order' => 60,
                ],
            ]);
        }

        // 9. Orthodontic Contract (ortho - عقد التقويم)
        $ortho = FormTemplate::where('code', 'ortho')->first();
        if ($ortho) {
            $ortho->fields()->delete();
            $ortho->fields()->createMany([
                [
                    'type' => 'section',
                    'label_ar' => 'تفاصيل عقد ومدة العلاج التقويمي المالي والطبي',
                    'label_en' => 'Orthodontic Contract & Plan details',
                    'sort_order' => 10,
                ],
                [
                    'type' => 'select',
                    'key' => 'ortho_system',
                    'label_ar' => 'نوع ومادة جهاز تقويم الأسنان',
                    'label_en' => 'Selected Orthodontic System',
                    'is_required' => true,
                    'options' => [
                        ['value' => 'metal_braces', 'label_ar' => 'تقويم معدني تقليدي (Metal Braces)', 'label_en' => 'Conventional Metal Braces'],
                        ['value' => 'ceramic_braces', 'label_ar' => 'تقويم خزفي تجميلي (Ceramic Braces)', 'label_en' => 'Cosmetic Ceramic Braces'],
                        ['value' => 'clear_aligners', 'label_ar' => 'شفاف غير مرئي (Invisalign / Clear)', 'label_en' => 'Invisible Clear Aligners'],
                    ],
                    'settings' => ['width' => 'half'],
                    'sort_order' => 20,
                ],
                [
                    'type' => 'text',
                    'key' => 'total_cost',
                    'label_ar' => 'إجمالي تكلفة علاج التقويم بالريال السعودي',
                    'label_en' => 'Total Orthodontic Treatment Cost (SAR)',
                    'placeholder_ar' => 'مثال: 6000 ريال',
                    'placeholder_en' => 'e.g. 6000 SAR',
                    'is_required' => true,
                    'settings' => ['width' => 'half'],
                    'sort_order' => 30,
                ],
                [
                    'type' => 'text',
                    'key' => 'payment_installments',
                    'label_ar' => 'الدفعة الأولى وقيمة القسط الشهري بالريال',
                    'label_en' => 'Down Payment & Monthly Installments Details',
                    'placeholder_ar' => 'مثال: 1500 مقدم، و 300 ريال شهرياً',
                    'placeholder_en' => 'e.g. 1500 upfront, 300 monthly',
                    'is_required' => true,
                    'settings' => ['width' => 'half'],
                    'sort_order' => 40,
                ],
                [
                    'type' => 'text',
                    'key' => 'estimated_months',
                    'label_ar' => 'المدة التقريبية المقدرة للعلاج (بالشهور)',
                    'label_en' => 'Estimated Treatment Duration (Months)',
                    'placeholder_ar' => 'مثال: 18 إلى 24 شهراً',
                    'placeholder_en' => 'e.g. 18 to 24 months',
                    'is_required' => true,
                    'settings' => ['width' => 'half'],
                    'sort_order' => 50,
                ],
                [
                    'type' => 'consent',
                    'key' => 'ortho_contract_rules',
                    'label_ar' => 'شروط وبنود عقد علاج تقويم الأسنان المتبادلة',
                    'label_en' => 'Orthodontic Treatment Contract Terms',
                    'content_ar' => 'أتعهد بالالتزام بالحضور للمواعيد الشهرية بانتظام والمحافظة التامة على نظافة الفم والأسنان لمنع التسوس أو التهابات اللثة الشديدة. وأتفهم أنه في حال عدم انتظامي أو كسر الحاصرات بصفة متكررة، سيؤدي ذلك لتأخر إنجاز العلاج وزيادة التكلفة والمدة المحددة بالعقد.',
                    'content_en' => 'I commit to attending all scheduled monthly activation appointments and maintaining absolute oral hygiene. I understand that failure to comply with instructions, missed appointments, or repeated bracket breakage will extend the treatment period and may result in additional fees.',
                    'sort_order' => 60,
                ],
                [
                    'type' => 'signature',
                    'key' => 'patient',
                    'label_ar' => 'توقيع المريض أو ولي أمره للموافقة على العقد',
                    'label_en' => 'Patient / Guardian Signature',
                    'is_required' => true,
                    'settings' => ['width' => 'half'],
                    'sort_order' => 70,
                ],
                [
                    'type' => 'signature',
                    'key' => 'doctor',
                    'label_ar' => 'توقيع أخصائي / استشاري تقويم الأسنان والختم',
                    'label_en' => 'Orthodontist Signature & Stamp',
                    'is_required' => true,
                    'settings' => ['width' => 'half'],
                    'sort_order' => 80,
                ],
            ]);
        }

        // 10. Treatment Completion (ortho_end - إقرار إنهاء التقويم)
        $ortho_end = FormTemplate::where('code', 'ortho_end')->first();
        if ($ortho_end) {
            $ortho_end->fields()->delete();
            $ortho_end->fields()->createMany([
                [
                    'type' => 'section',
                    'label_ar' => 'إنهاء العلاج التقويمي وإزالة الأجهزة الحاصرة',
                    'label_en' => 'Orthodontic Debonding & Completion',
                    'sort_order' => 10,
                ],
                [
                    'type' => 'date',
                    'key' => 'debonding_date',
                    'label_ar' => 'تاريخ إزالة جهاز التقويم بطلب المراجع',
                    'label_en' => 'Debonding Date',
                    'is_required' => true,
                    'settings' => ['width' => 'half'],
                    'sort_order' => 20,
                ],
                [
                    'type' => 'select',
                    'key' => 'retainer_type',
                    'label_ar' => 'نوع المثبت الموصى به والمصمم للحالة',
                    'label_en' => 'Recommended Post-Ortho Retainer Type',
                    'is_required' => true,
                    'options' => [
                        ['value' => 'fixed', 'label_ar' => 'مثبت سلكي ثابت (Fixed Wire Retainer)', 'label_en' => 'Fixed Retainer'],
                        ['value' => 'essix', 'label_ar' => 'مثبت شفاف متحرك (Essix Clear)', 'label_en' => 'Clear Essix Retainer'],
                        ['value' => 'hawley', 'label_ar' => 'مثبت هولي الأكريليكي (Hawley Retainer)', 'label_en' => 'Hawley Retainer'],
                    ],
                    'settings' => ['width' => 'half'],
                    'sort_order' => 30,
                ],
                [
                    'type' => 'consent',
                    'key' => 'debond_retainer_consent',
                    'label_ar' => 'إقرار وتعهد بالالتزام بجهاز تثبيت الأسنان بعد التقويم وإخلاء المسؤولية',
                    'label_en' => 'Post-Ortho Retainer Compliance Consent & Waiver',
                    'content_ar' => 'أقر بأنني راضٍ تماماً عن النتيجة التجميلية والوظيفية التي وصلت إليها أسناني، وطلبت إزالة جهاز التقويم. وأتفهم تماماً أن الأسنان قابلة للارتداد والتحرك لوضعها السابق إذا لم ألتزم بارتداء جهاز تثبيت الأسنان (الريتينر) بانتظام حسب توجيهات الطبيب، وتتحمل المسؤولية كاملة عن أي تغير يطرأ مستقبلاً.',
                    'content_en' => 'I declare full satisfaction with the orthodontic outcome and requested debonding. I strongly understand that teeth will relapse to their original positions if I do not wear the retainer strictly as prescribed by the orthodontist. I accept full liability for any relapse.',
                    'sort_order' => 40,
                ],
                [
                    'type' => 'signature',
                    'key' => 'patient',
                    'label_ar' => 'توقيع المراجع للإقرار بالنتيجة والالتزام بالمثبت',
                    'label_en' => 'Patient Signature',
                    'is_required' => true,
                    'settings' => ['width' => 'half'],
                    'sort_order' => 50,
                ],
                [
                    'type' => 'signature',
                    'key' => 'doctor',
                    'label_ar' => 'توقيع أخصائي التقويم المعالج والختم',
                    'label_en' => 'Orthodontist Signature',
                    'is_required' => true,
                    'settings' => ['width' => 'half'],
                    'sort_order' => 60,
                ],
            ]);
        }

        // 11. Orthodontic Photo Consent (ortho_photo - موافقة تصوير التقويم)
        $ortho_photo = FormTemplate::where('code', 'ortho_photo')->first();
        if ($ortho_photo) {
            $ortho_photo->fields()->delete();
            $ortho_photo->fields()->createMany([
                [
                    'type' => 'section',
                    'label_ar' => 'بيانات وتفاصيل موافقة تصوير الأسنان والوجه للتقويم',
                    'label_en' => 'Orthodontic Photographic Consent details',
                    'sort_order' => 10,
                ],
                [
                    'type' => 'radio',
                    'key' => 'allow_social_media',
                    'label_ar' => 'هل توافق على نشر الصور في حسابات التواصل الاجتماعي للمجمع لأغراض دعائية؟',
                    'label_en' => 'Do you consent to publishing clinical photographs on our social media platforms for marketing?',
                    'is_required' => true,
                    'options' => [
                        ['value' => 'yes_all', 'label_ar' => 'نعم، أوافق على النشر الكامل', 'label_en' => 'Yes, fully agree'],
                        ['value' => 'yes_eyes_masked', 'label_ar' => 'نعم، بشرط إخفاء العينين والهوية', 'label_en' => 'Yes, but mask my eyes/identity'],
                        ['value' => 'no_scientific_only', 'label_ar' => 'لا، أوافق للاستخدام العلمي والملف الطبي فقط', 'label_en' => 'No, clinical/scientific records only'],
                    ],
                    'settings' => ['width' => 'full'],
                    'sort_order' => 20,
                ],
                [
                    'type' => 'consent',
                    'key' => 'photo_release_text',
                    'label_ar' => 'إقرار وتعهد تصوير الأسنان والوجه وحقوق الملكية',
                    'label_en' => 'Clinical Photo Consent & Intellectual Release Statement',
                    'content_ar' => 'أوافق على التقاط صور فوتوغرافية ومقاطع فيديو لوجهي وأسناني قبل، خلال، وبعد العلاج التقويمي لدراسة الحالة وقياس التطور، وأتنازل طواعية عن أي حقوق ملكية فكرية أو مطالبات مالية تتعلق بنشر هذه الصور للغرض المحدد الموضح أعلاه.',
                    'content_en' => 'I authorize capturing clinical photographs and videos of my face and teeth before, during, and after orthodontic treatment. I voluntarily waive any intellectual property rights or financial claims related to publishing these records for the authorized purposes.',
                    'sort_order' => 30,
                ],
                [
                    'type' => 'signature',
                    'key' => 'patient',
                    'label_ar' => 'توقيع المريض أو ولي أمره للموافقة على التصوير',
                    'label_en' => 'Patient / Guardian Signature',
                    'is_required' => true,
                    'settings' => ['width' => 'half'],
                    'sort_order' => 40,
                ],
                [
                    'type' => 'signature',
                    'key' => 'doctor',
                    'label_ar' => 'توقيع الأخصائي المصور',
                    'label_en' => 'Practitioner Signature',
                    'is_required' => true,
                    'settings' => ['width' => 'half'],
                    'sort_order' => 50,
                ],
            ]);
        }

        // 12. Extraction Referral (ortho_extraction - تحويل لخلع الأسنان)
        $ortho_extraction = FormTemplate::where('code', 'ortho_extraction')->first();
        if ($ortho_extraction) {
            $ortho_extraction->fields()->delete();
            $ortho_extraction->fields()->createMany([
                [
                    'type' => 'section',
                    'label_ar' => 'تفاصيل تحويل خلع الأسنان والضروس لأغراض التقويم',
                    'label_en' => 'Ortho Extraction Referral details',
                    'sort_order' => 10,
                ],
                [
                    'type' => 'text',
                    'key' => 'teeth_to_extract',
                    'label_ar' => 'أرقام الأسنان المطلوبة للخلع بوضوح (FDI System)',
                    'label_en' => 'Exact Teeth Numbers Scheduled for Extraction',
                    'placeholder_ar' => 'مثال: 14, 24, 34, 44',
                    'placeholder_en' => 'e.g. 14, 24, 34, 44',
                    'is_required' => true,
                    'settings' => ['width' => 'half'],
                    'sort_order' => 20,
                ],
                [
                    'type' => 'textarea',
                    'key' => 'clinical_reason',
                    'label_ar' => 'السبب وراء خلع الأسنان (توفير مساحة / بروز / تزاحم)',
                    'label_en' => 'Clinical Reason for recommended extractions',
                    'placeholder_ar' => 'مثال: لتوفير مساحة لصف الأسنان وحل التزاحم الشديد والتراجع بالفك السفلي',
                    'placeholder_en' => 'e.g. To resolve severe anterior crowding and profile bimaxillary protrusion',
                    'is_required' => true,
                    'settings' => ['width' => 'full', 'rows' => 2],
                    'sort_order' => 30,
                ],
                [
                    'type' => 'consent',
                    'key' => 'referral_consent_text',
                    'label_ar' => 'تأكيد أخصائي التقويم المعالج وإقرار المريض وتعاونه',
                    'label_en' => 'Orthodontist Referral Statement & Patient Compliance',
                    'content_ar' => 'تم اتخاذ قرار خلع الأسنان المذكورة أعلاه بعد مراجعة دقيقة لصور الأشعة، ويتم تحويل المراجع لعيادة الجراحة/الخلع لتنفيذ الإجراء الموضح، وقد وافق المراجع بعد فهمه الكامل للفوائد التجميلية للتقويم الناتجة عن الخلع.',
                    'content_en' => 'The extraction decision was finalized following thorough review of radiographic analysis. The patient is referred to oral surgery for the specified procedures. The patient consents to this step as a necessary part of the orthodontic setup.',
                    'sort_order' => 40,
                ],
                [
                    'type' => 'signature',
                    'key' => 'doctor',
                    'label_ar' => 'توقيع استشاري / أخصائي تقويم الأسنان المحوّل والختم',
                    'label_en' => 'Referring Orthodontist Signature',
                    'is_required' => true,
                    'settings' => ['width' => 'half'],
                    'sort_order' => 50,
                ],
            ]);
        }

        // 13. Procedures & Injections Card (proc_card - كرت الإجراءات والحقن)
        $proc_card = FormTemplate::where('code', 'proc_card')->first();
        if ($proc_card) {
            $proc_card->fields()->delete();
            $proc_card->fields()->createMany([
                [
                    'type' => 'section',
                    'label_ar' => 'بيانات وتفاصيل جلسة الإجراء التجميلي وجلسة الحقن',
                    'label_en' => 'Cosmetic Procedures & Injections Session Details',
                    'sort_order' => 10,
                ],
                [
                    'type' => 'select',
                    'key' => 'injection_type',
                    'label_ar' => 'نوع الإجراء التجميلي / الحقن الجلدي المطلوب',
                    'label_en' => 'Selected Dermatology / Cosmetic Treatment Type',
                    'is_required' => true,
                    'options' => [
                        ['value' => 'botox', 'label_ar' => 'بوتوكس للتجاعيد (Botox Full Face)', 'label_en' => 'Botox Injection'],
                        ['value' => 'filler', 'label_ar' => 'حقن فيلر تعبئة (Hyaluronic Acid Filler)', 'label_en' => 'Dermal Filler'],
                        ['value' => 'profhilo', 'label_ar' => 'بروفايلو لإعادة النضارة (Profhilo Face)', 'label_en' => 'Profhilo Bioremodeling'],
                        ['value' => 'mesotherapy', 'label_ar' => 'ميزوثيرابي / بلازما نضارة وشعر', 'label_en' => 'Mesotherapy / PRP Session'],
                    ],
                    'settings' => ['width' => 'half'],
                    'sort_order' => 20,
                ],
                [
                    'type' => 'text',
                    'key' => 'product_name',
                    'label_ar' => 'اسم المادة والشركة المصنعة والكمية / وحدات الحقن',
                    'label_en' => 'Product Brand, Batch, and Volume / Units Injected',
                    'placeholder_ar' => 'مثال: Allergan Botox 50 Units, Juvederm Voluma 1ml',
                    'placeholder_en' => 'e.g. Allergan Botox 50 Units, Juvederm Voluma 1ml',
                    'is_required' => true,
                    'settings' => ['width' => 'half'],
                    'sort_order' => 30,
                ],
                [
                    'type' => 'consent',
                    'key' => 'injection_consent_text',
                    'label_ar' => 'إقرار وتعهد جلسة حقن المواد التجميلية وإخلاء المسؤولية',
                    'label_en' => 'Injectable Cosmetic Treatment Consent Statement',
                    'content_ar' => 'أوافق على تلقي جلسة الحقن الموضحة أعلاه، وأقر بأنني على علم تام ببعض الآثار الجانبية الشائعة المؤقتة للحقن مثل الاحمرار البسيط، الكدمات الموضعية تحت الجلد، أو التورم الخفيف الذي يزول خلال 2 إلى 5 أيام. وأتعهد بالالتزام بتعليمات الطبيب كاملة بعد الجلسة.',
                    'content_en' => 'I agree to receive the cosmetic injections detailed above. I understand potential temporary side effects, including minor localized swelling, redness, or transient bruising, which usually resolve in 2 to 5 days. I promise to follow all post-treatment instructions.',
                    'sort_order' => 40,
                ],
                [
                    'type' => 'signature',
                    'key' => 'patient',
                    'label_ar' => 'توقيع المراجعة للموافقة وتطابق المادة',
                    'label_en' => 'Patient Signature',
                    'is_required' => true,
                    'settings' => ['width' => 'half'],
                    'sort_order' => 50,
                ],
                [
                    'type' => 'signature',
                    'key' => 'doctor',
                    'label_ar' => 'توقيع طبيب الجلدية والتجميل المعالج',
                    'label_en' => 'Dermatologist Signature',
                    'is_required' => true,
                    'settings' => ['width' => 'half'],
                    'sort_order' => 60,
                ],
            ]);
        }

        // 14. Roaccutane Consent (roaccutane - موافقة الروكتان)
        $roaccutane = FormTemplate::where('code', 'roaccutane')->first();
        if ($roaccutane) {
            $roaccutane->fields()->delete();
            $roaccutane->fields()->createMany([
                [
                    'type' => 'section',
                    'label_ar' => 'الملف والتاريخ الطبي والنفسي لوصف حبوب الروكتان',
                    'label_en' => 'Roaccutane Health Profile & History',
                    'sort_order' => 10,
                ],
                [
                    'type' => 'radio',
                    'key' => 'is_female_pregnant',
                    'label_ar' => 'للإناث: هل هناك أي احتمالية للحمل حالياً، وهل تم عمل تحليل دم خلال 3 أيام مضت؟',
                    'label_en' => 'Females: Is there any possibility of pregnancy, and was a serum pregnancy test negative in the last 3 days?',
                    'is_required' => true,
                    'options' => [
                        ['value' => 'yes_negative', 'label_ar' => 'أنا أنثى متزوجة، وتم عمل تحليل سلبي بنجاح', 'label_en' => 'Yes, married and test was negative'],
                        ['value' => 'not_applicable', 'label_ar' => 'أنا غير متزوجة (أو مراجع ذكر)', 'label_en' => 'Single female or Male patient'],
                    ],
                    'settings' => ['width' => 'half'],
                    'sort_order' => 20,
                ],
                [
                    'type' => 'text',
                    'key' => 'daily_dose',
                    'label_ar' => 'الجرعة اليومية المقررة بالملغ من قبل الطبيب',
                    'label_en' => 'Prescribed Daily Dosage (mg)',
                    'placeholder_ar' => 'مثال: 20 ملغ، 40 ملغ يومياً',
                    'placeholder_en' => 'e.g. 20 mg, 40 mg daily',
                    'is_required' => true,
                    'settings' => ['width' => 'half'],
                    'sort_order' => 30,
                ],
                [
                    'type' => 'consent',
                    'key' => 'roaccutane_safety_consent',
                    'label_ar' => 'إقرار وتعهد الالتزام التام بشروط استخدام الروكتان (أيزوتريتينوين) الخطيرة',
                    'label_en' => 'Isotretinoin (Roaccutane) Safe Use & Birth Prevention Consent',
                    'content_ar' => 'أتعهد تعهداً تاماً بعدم حدوث حمل مطلقاً طوال فترة العلاج بالروكتان ولمدة شهر كامل بعد التوقف التام عنه، وذلك نظراً لخطورته القصوى وإحداثه تشوهات جنينية قاسية بنسبة 100%. كما ألتزم بعمل التحاليل الدورية لوظائف الكبد والدهون بانتظام.',
                    'content_en' => 'I firmly commit to preventing pregnancy during Isotretinoin treatment and for one month following discontinuation, due to the absolute risk of severe fetal malformations. I agree to perform regular blood tests for lipid profile and liver enzymes.',
                    'sort_order' => 40,
                ],
                [
                    'type' => 'signature',
                    'key' => 'patient',
                    'label_ar' => 'توقيع المراجعة / المريض للإقرار بالالتزام والخطورة',
                    'label_en' => 'Patient Signature',
                    'is_required' => true,
                    'settings' => ['width' => 'half'],
                    'sort_order' => 50,
                ],
                [
                    'type' => 'signature',
                    'key' => 'doctor',
                    'label_ar' => 'توقيع أخصائي / استشاري الجلدية والختم',
                    'label_en' => 'Dermatologist Signature',
                    'is_required' => true,
                    'settings' => ['width' => 'half'],
                    'sort_order' => 60,
                ],
            ]);
        }

        // 15. Photo Consent (derm_photo - موافقة التصوير للجلدية والتجميل)
        $derm_photo = FormTemplate::where('code', 'derm_photo')->first();
        if ($derm_photo) {
            $derm_photo->fields()->delete();
            $derm_photo->fields()->createMany([
                [
                    'type' => 'section',
                    'label_ar' => 'تفاصيل موافقة تصوير الحالات الجلدية والتجميلية',
                    'label_en' => 'Dermatology Photo Consent details',
                    'sort_order' => 10,
                ],
                [
                    'type' => 'consent',
                    'key' => 'photo_consent_statement',
                    'label_ar' => 'إقرار وتعهد الموافقة على تصوير ونشر الحالات الطبية الجلدية والتجميلية',
                    'label_en' => 'Clinical Photo Consent for Dermatology & Laser Treatment',
                    'content_ar' => 'أوافق على التقاط صور فوتوغرافية ومقاطع فيديو للمنطقة المستهدفة بالعلاج لدراسة الحالة وقياس التطور قبل وبعد الإجراء. وأسمح للمجمع باستخدام الصور إما للملف الطبي العلمي أو لنشرها دعائياً مع حماية خصوصية وجهي وهوية عيوني بالكامل.',
                    'content_en' => 'I authorize capturing clinical photographs of the treatment areas to document progress before and after treatments. I consent to their clinical use and potential educational or marketing publication, provided my facial identity and eyes are fully masked.',
                    'sort_order' => 20,
                ],
                [
                    'type' => 'signature',
                    'key' => 'patient',
                    'label_ar' => 'توقيع المراجعة للموافقة',
                    'label_en' => 'Patient Signature',
                    'is_required' => true,
                    'settings' => ['width' => 'half'],
                    'sort_order' => 30,
                ],
                [
                    'type' => 'signature',
                    'key' => 'doctor',
                    'label_ar' => 'توقيع الأخصائي أو المعالج والختم',
                    'label_en' => 'Practitioner Signature',
                    'is_required' => true,
                    'settings' => ['width' => 'half'],
                    'sort_order' => 40,
                ],
            ]);
        }

        // 16. Self Laser Consent (self_laser - إقرار الليزر الذاتي)
        $self_laser = FormTemplate::where('code', 'self_laser')->first();
        if ($self_laser) {
            $self_laser->fields()->delete();
            $self_laser->fields()->createMany([
                [
                    'type' => 'section',
                    'label_ar' => 'إقرار وتعهد استخدام جهاز الخدمة الذاتية لليزر',
                    'label_en' => 'Self Laser Service Acknowledgment & Zones',
                    'sort_order' => 10,
                ],
                [
                    'type' => 'text',
                    'key' => 'body_zones',
                    'label_ar' => 'مناطق الجسم المستهدفة بجلسة الخدمة الذاتية لليزر',
                    'label_en' => 'Targeted Body Zones for Self Laser Session',
                    'placeholder_ar' => 'مثال: الساقين، اليدين، الابطين',
                    'placeholder_en' => 'e.g. Full arms, legs, underarms',
                    'is_required' => true,
                    'settings' => ['width' => 'full'],
                    'sort_order' => 20,
                ],
                [
                    'type' => 'consent',
                    'key' => 'self_laser_waiver_statement',
                    'label_ar' => 'تعهد وإخلاء مسؤولية مجمع النخبة الطبي عن جهاز الخدمة الذاتية لليزر والخطورة',
                    'label_en' => 'Self Laser Liability Waiver & Safe Operation Acknowledgment',
                    'content_ar' => 'أقر بأنني اخترت استخدام جهاز الليزر بالخدمة الذاتية بكامل رغبتي وأتحمل المسؤولية الكاملة عن تشغيل الجهاز، وسلامة عيني باستخدام النظارات الواقية طوال الجلسة. وأخلي مسؤولية العيادة أو الأخصائيين تماماً عن أي حروق أو أعراض قد تحدث نتيجة سوء استخدامي للجهاز أو زيادة الطاقة المقررة.',
                    'content_en' => 'I acknowledge that I voluntarily chose to utilize the Self Laser service. I assume full responsibility for operating the device and protecting my eyes using protective goggles. I release the clinic and practitioners of any liability regarding skin burns or complications caused by incorrect operation.',
                    'sort_order' => 30,
                ],
                [
                    'type' => 'signature',
                    'key' => 'patient',
                    'label_ar' => 'توقيع المراجعة لإخلاء المسؤولية والتعهد',
                    'label_en' => 'Patient Signature & Release',
                    'is_required' => true,
                    'settings' => ['width' => 'full'],
                    'sort_order' => 40,
                ],
            ]);
        }

        // 17. Morpheus Consent (morpheus - موافقة المورفيس)
        $morpheus = FormTemplate::where('code', 'morpheus')->first();
        if ($morpheus) {
            $morpheus->fields()->delete();
            $morpheus->fields()->createMany([
                [
                    'type' => 'section',
                    'label_ar' => 'تفاصيل جلسة المورفيس لإعادة نضارة البشرة وشد الترهلات',
                    'label_en' => 'Morpheus RF Treatment Details',
                    'sort_order' => 10,
                ],
                [
                    'type' => 'select',
                    'key' => 'morpheus_area',
                    'label_ar' => 'منطقة العلاج المستهدفة بجلسة المورفيس',
                    'label_en' => 'Target Morpheus Treatment Area',
                    'is_required' => true,
                    'options' => [
                        ['value' => 'face_only', 'label_ar' => 'الوجه بالكامل (Full Face)', 'label_en' => 'Full Face'],
                        ['value' => 'face_neck', 'label_ar' => 'الوجه والرقبة معاً (Full Face & Neck)', 'label_en' => 'Full Face & Neck'],
                        ['value' => 'neck_only', 'label_ar' => 'الرقبة فقط (Neck Area)', 'label_en' => 'Neck Only'],
                        ['value' => 'body_zone', 'label_ar' => 'منطقة محددة بالجسم (علامات تمدد / شد)', 'label_en' => 'Target Body Area'],
                    ],
                    'settings' => ['width' => 'half'],
                    'sort_order' => 20,
                ],
                [
                    'type' => 'select',
                    'key' => 'num_sessions',
                    'label_ar' => 'رقم الجلسة الحالية في الخطة العلاجية للمراجع',
                    'label_en' => 'Current Session Number in Treatment Plan',
                    'is_required' => true,
                    'options' => [
                        ['value' => 'session_1', 'label_ar' => 'الجلسة الأولى (First Session)', 'label_en' => 'First Session'],
                        ['value' => 'session_2', 'label_ar' => 'الجلسة الثانية (Second Session)', 'label_en' => 'Second Session'],
                        ['value' => 'session_3', 'label_ar' => 'الجلسة الثالثة (Third Session)', 'label_en' => 'Third Session'],
                    ],
                    'settings' => ['width' => 'half'],
                    'sort_order' => 30,
                ],
                [
                    'type' => 'consent',
                    'key' => 'morpheus_consent_statement',
                    'label_ar' => 'إقرار وتعهد الخضوع لعلاج المورفيس وجهاز الراديو فريدوانسي الإبرية للتجميل',
                    'label_en' => 'Morpheus RF Microneedling Consent Statement',
                    'content_ar' => 'أوافق على الخضوع لعلاج المورفيس، وأقر بأنني على علم تام بطبيعة الإجراء الذي يعتمد على تمرير الإبر الدقيقة الموصلة للراديو فريكونسي، وأتفهم حدوث احمرار طفيف وتورم خفيف أو قشور ناعمة مؤقتة في البشرة تزول خلال 3 إلى 7 أيام، وسألتزم باستخدام كريمات النضارة والترميم بدقة.',
                    'content_en' => 'I consent to undergo the Morpheus RF treatment. I understand that the microneedling RF process may cause temporary redness, mild swelling, or fine crusting on the skin which resolves naturally within 3 to 7 days. I agree to apply post-session restorative creams as directed.',
                    'sort_order' => 40,
                ],
                [
                    'type' => 'signature',
                    'key' => 'patient',
                    'label_ar' => 'توقيع المراجعة للإقرار بالموافقة والالتزام',
                    'label_en' => 'Patient Signature',
                    'is_required' => true,
                    'settings' => ['width' => 'half'],
                    'sort_order' => 50,
                ],
                [
                    'type' => 'signature',
                    'key' => 'doctor',
                    'label_ar' => 'توقيع الطبيب المعالج والختم',
                    'label_en' => 'Practitioner Signature & Stamp',
                    'is_required' => true,
                    'settings' => ['width' => 'half'],
                    'sort_order' => 60,
                ],
            ]);
        }
    }
}
