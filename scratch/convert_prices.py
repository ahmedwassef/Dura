import json
import re

with open('scratch/prices.txt', 'r', encoding='utf-8') as f:
    lines = f.readlines()

data = "".join(lines)
# Remove the JS variable declaration and closing bracket
data = data.replace('const PRICES = [', '').replace('];', '')

# Extract objects using regex
# Example: { code: '111', ar: 'مراجعة', en: 'Follow-up visit', price: 0, category: 'general', categoryAr: 'الخدمات العامة', categoryEn: 'General Services' },
pattern = re.compile(r"\{\s*code:\s*'([^']*)',\s*ar:\s*'([^']*)',\s*en:\s*'([^']*)',\s*price:\s*([\d\.]+),\s*category:\s*'([^']*)',\s*categoryAr:\s*'([^']*)',\s*categoryEn:\s*'([^']*)'\s*\}")

matches = pattern.findall(data)

php_entries = []
for m in matches:
    entry = f"            ['code' => '{m[0]}', 'name_ar' => '{m[1]}', 'name_en' => '{m[2]}', 'price' => {m[3]}, 'category' => '{m[4]}', 'category_ar' => '{m[5]}', 'category_en' => '{m[6]}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]"
    php_entries.append(entry)

seeder_content = f"""<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceCatalogSeeder extends Seeder
{{
    public function run(): void
    {{
        DB::table('service_catalog')->insert([
{",\n".join(php_entries)}
        ]);
    }}
}}
"""

with open('database/seeders/ServiceCatalogSeeder.php', 'w', encoding='utf-8') as f:
    f.write(seeder_content)
