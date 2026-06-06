<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Pastikan Role "Guru" sudah ada (opsional, sesuaikan dengan logic sistem Anda)
        $roleGuru = Role::firstOrCreate(['name' => 'Guru']);

        // 2. Data dummy guru
        $teachers = [
            [
                'name' => 'Budi Santoso, S.Pd',
                'email' => 'budi@sekolah.com',
                'password' => Hash::make('password123'),
                'nip' => '198001012005011001'
            ],
            [
                'name' => 'Siti Aminah, M.Pd',
                'email' => 'siti@sekolah.com',
                'password' => Hash::make('password123'),
                'nip' => '198505052010012002'
            ]
        ];

        // 3. Simpan ke database dan berikan role
        foreach ($teachers as $teacherData) {
            $user = User::create($teacherData);
            $user->roles()->attach($roleGuru->id);
        }
    }
}