<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DaySeeder extends Seeder
{
    public function run()
    {
        $days = [
            ['name' => 'Senin'],
            ['name' => 'Selasa'],
            ['name' => 'Rabu'],
            ['name' => 'Kamis'],
            ['name' => 'Jumat'],
            ['name' => 'Sabtu'],
            ['name' => 'Minggu'],
        ];

        foreach ($days as $day) {
            DB::table('days')->insert([
                'name'       => $day['name'],
            ]);
        }
    }
}