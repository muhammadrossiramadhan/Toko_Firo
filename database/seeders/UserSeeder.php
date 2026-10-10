<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(['username' => 'admin'], [
            'name' => 'Pemilik Toko',
            'email' => 'admin@tokofiro.local',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);
        User::updateOrCreate(['username' => 'kasir1'], [
            'name' => 'Kasir 1',
            'email' => null,
            'password' => Hash::make('kasir123'),
            'role' => 'kasir',
        ]);
    }
}
