<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StudentSeeder extends Seeder
{
    public function run()
    {
        $students = [
            ['nisn' => '0061234567', 'name' => 'Budi Santoso'],
            ['nisn' => '0061234568', 'name' => 'Siti Aminah'],
            ['nisn' => '0061234569', 'name' => 'Ahmad Fauzi'],
            ['nisn' => '0061234570', 'name' => 'Rina Wijaya'],
            ['nisn' => '0061234571', 'name' => 'Bambang Irawan'],
            ['nisn' => '0061234572', 'name' => 'Dewi Lestari'],
            ['nisn' => '0061234573', 'name' => 'Joko Anwar'],
            ['nisn' => '0061234574', 'name' => 'Eka Putri'],
            ['nisn' => '0061234575', 'name' => 'Agus Prasetyo'],
            ['nisn' => '0061234576', 'name' => 'Putri Indah'],
        ];

        foreach ($students as $student) {
            DB::table('students')->insert([
                'nisn'       => $student['nisn'],
                'name'       => $student['name'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}