<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = [
            ['name' => 'English', 'code' => 'en', 'is_default' => true, 'status' => 'active'],
            ['name' => 'Bengali', 'code' => 'bn', 'is_default' => false, 'status' => 'active'],
            ['name' => 'Russian', 'code' => 'ru', 'is_default' => false, 'status' => 'active'],
            ['name' => 'Uzbek', 'code' => 'uz', 'is_default' => false, 'status' => 'active'],
        ];

        foreach ($languages as $lang) {
            Language::updateOrCreate(['code' => $lang['code']], $lang);
        }
    }
}
