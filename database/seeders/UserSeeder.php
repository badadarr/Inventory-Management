<?php

namespace Database\Seeders;

use App\Enums\User\UserRoleEnum;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@inventory.com',
                'password' => Hash::make('password'),
                'role' => UserRoleEnum::ADMIN->value,
            ],
            [
                'name' => 'Sales User',
                'email' => 'sales@inventory.com',
                'password' => Hash::make('password'),
                'role' => UserRoleEnum::SALES->value,
            ],
            [
                'name' => 'Finance User',
                'email' => 'finance@inventory.com',
                'password' => Hash::make('password'),
                'role' => UserRoleEnum::FINANCE->value,
            ],
            [
                'name' => 'Warehouse User',
                'email' => 'warehouse@inventory.com',
                'password' => Hash::make('password'),
                'role' => UserRoleEnum::WAREHOUSE->value,
            ],
            [
                'name' => 'John Manager',
                'email' => 'john@inventory.com',
                'password' => Hash::make('password'),
                'role' => UserRoleEnum::ADMIN->value,
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }

        $this->command->info('✅ Created ' . count($users) . ' users');
    }
}
