<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@gmail.com',
            'username' => 'admin',
            'password' => Hash::make('12345678'),
            'phone_number' => '9876543210',
            'is_active' => true,
            'is_verified' => true,
            'email_verified_at' => now(),
            'last_login_at' => now(),
            'last_login_ip' => '127.0.0.1',
        ]);

    }
}
