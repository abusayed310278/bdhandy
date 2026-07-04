<?php

namespace Database\Seeders;

use App\Models\DayOfWeek;
use Illuminate\Database\Seeder;

class DayOfWeekSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \Schema::disableForeignKeyConstraints();
        DayOfWeek::truncate();
        \Schema::enableForeignKeyConstraints();


        $days = [
            [
                'en' => 'Saturday',
                'ru' => 'Суббота',
                'uz' => 'Shanba',
                'bn' => 'শনিবার',
            ],
            [
                'en' => 'Sunday',
                'ru' => 'Воскресенье',
                'uz' => 'Yakshanba',
                'bn' => 'রবিবার',
            ],
            [
                'en' => 'Monday',
                'ru' => 'Понедельник',
                'uz' => 'Dushanba',
                'bn' => 'সোমবার',
            ],
            [
                'en' => 'Tuesday',
                'ru' => 'Вторник',
                'uz' => 'Seshanba',
                'bn' => 'মঙ্গলবার',
            ],
            [
                'en' => 'Wednesday',
                'ru' => 'Среда',
                'uz' => 'Chorshanba',
                'bn' => 'বুধবার',
            ],
            [
                'en' => 'Thursday',
                'ru' => 'Четверг',
                'uz' => 'Payshanba',
                'bn' => 'বৃহস্পতিবার',
            ],
            [
                'en' => 'Friday',
                'ru' => 'Пятница',
                'uz' => 'Juma',
                'bn' => 'শুক্রবার',
            ],
        ];


        foreach ($days as $day) {
            DayOfWeek::create([
                'translations' => $day
            ]);
        }
    }
}
