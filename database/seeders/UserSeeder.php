<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'company_name' => 'PT. Inventory Management',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'photo' => null,
        ]);

        User::create([
            'name' => 'John Doe',
            'company_name' => 'PT. Spero Indonesia',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
            'photo' => null,
        ]);

        User::create([
            'name' => 'Jane Smith',
            'company_name' => 'CV. Maju Jaya',
            'email' => 'jane@example.com',
            'password' => Hash::make('password'),
            'photo' => null,
        ]);
    }
}
