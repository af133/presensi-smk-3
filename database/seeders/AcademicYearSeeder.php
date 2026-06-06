<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AcademicYear;

class AcademicYearSeeder extends Seeder
{
    public function run()
    {
        $years = [
            ['name' => '2025/2026', 'is_active' => false],
            ['name' => '2026/2027', 'is_active' => true],
            ['name' => '2027/2028', 'is_active' => false],
            ['name' => '2028/2029', 'is_active' => false],
            ['name' => '2029/2030', 'is_active' => false],
        ];

        foreach ($years as $year) {
            AcademicYear::create($year);
        }
    }
}