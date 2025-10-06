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
        // Super Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('password'),
            'role' => UserRoleEnum::SUPER_ADMIN->value,
            'company_name' => 'Head Office',
            'company_id' => null,
        ]);

        // Admin PT A
        User::create([
            'name' => 'Admin PT A',
            'email' => 'admin.pta@example.com',
            'password' => Hash::make('password'),
            'role' => UserRoleEnum::ADMIN->value,
            'company_name' => 'PT. Company A',
            'company_id' => 1,
        ]);

        // Admin PT B
        User::create([
            'name' => 'Admin PT B',
            'email' => 'admin.ptb@example.com',
            'password' => Hash::make('password'),
            'role' => UserRoleEnum::ADMIN->value,
            'company_name' => 'PT. Company B',
            'company_id' => 2,
        ]);

        // Sales
        User::create([
            'name' => 'Sales User',
            'email' => 'sales@example.com',
            'password' => Hash::make('password'),
            'role' => UserRoleEnum::SALES->value,
            'company_name' => 'PT. Company A',
            'company_id' => 1,
        ]);

        // Warehouse
        User::create([
            'name' => 'Warehouse User',
            'email' => 'warehouse@example.com',
            'password' => Hash::make('password'),
            'role' => UserRoleEnum::WAREHOUSE->value,
            'company_name' => 'PT. Company A',
            'company_id' => 1,
        ]);

        // Finance
        User::create([
            'name' => 'Finance User',
            'email' => 'finance@example.com',
            'password' => Hash::make('password'),
            'role' => UserRoleEnum::FINANCE->value,
            'company_name' => 'PT. Company A',
            'company_id' => 1,
        ]);
    }
}
