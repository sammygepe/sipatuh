<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Departemen;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Buat departemen
        $finance = Departemen::create([
            'nama' => 'Finance',
            'bobot_default' => 100
        ]);

        $accounting = Departemen::create([
            'nama' => 'Accounting',
            'bobot_default' => 100
        ]);

        $admin = Departemen::create([
            'nama' => 'Admin',
            'bobot_default' => 100
        ]);

        $technical = Departemen::create([
            'nama' => 'Technical Support',
            'bobot_default' => 100
        ]);

        // Buat user Aisyah (Finance - Atasan)
        User::create([
            'name' => 'Aisyah',
            'email' => 'aisyah@company.com',
            'password' => Hash::make('password'),
            'departemen_id' => $finance->id,
            'is_atasan' => true,
            'is_admin' => false
        ]);

        // Buat user Ani (Accounting - Atasan)
        $ani = User::create([
            'name' => 'Ani',
            'email' => 'ani@company.com',
            'password' => Hash::make('password'),
            'departemen_id' => $accounting->id,
            'is_atasan' => true,
            'is_admin' => false
        ]);

        // Buat user Cici (Accounting - Staff)
        User::create([
            'name' => 'Cici',
            'email' => 'cici@company.com',
            'password' => Hash::make('password'),
            'departemen_id' => $accounting->id,
            'is_atasan' => false,
            'is_admin' => false,
            'atasan_id' => $ani->id
        ]);

        // Buat user Raja (Admin)
        User::create([
            'name' => 'Raja',
            'email' => 'raja@company.com',
            'password' => Hash::make('password'),
            'departemen_id' => $admin->id,
            'is_atasan' => true,
            'is_admin' => true
        ]);

        // Buat user Eka (Technical Support - Atasan)
        $eka = User::create([
            'name' => 'Eka',
            'email' => 'eka@company.com',
            'password' => Hash::make('password'),
            'departemen_id' => $technical->id,
            'is_atasan' => true,
            'is_admin' => false
        ]);

        // Buat user Fani (Technical Support - Staff)
        User::create([
            'name' => 'Fani',
            'email' => 'fani@company.com',
            'password' => Hash::make('password'),
            'departemen_id' => $technical->id,
            'is_atasan' => false,
            'is_admin' => false,
            'atasan_id' => $eka->id
        ]);
    }
}