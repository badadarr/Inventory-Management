<?php

namespace App\Helpers;

use App\Enums\User\UserRoleEnum;

class PermissionHelper
{
    /**
     * Define menu permissions for each role
     */
    public static function getMenuPermissions(): array
    {
        return [
            'dashboard' => [
                UserRoleEnum::SUPER_ADMIN->value,
                UserRoleEnum::ADMIN->value,
                UserRoleEnum::SALES->value,
                UserRoleEnum::WAREHOUSE->value,
                UserRoleEnum::FINANCE->value,
            ],
            'master_data' => [
                UserRoleEnum::SUPER_ADMIN->value,
                UserRoleEnum::ADMIN->value,
            ],
            'categories' => [
                UserRoleEnum::SUPER_ADMIN->value,
                UserRoleEnum::ADMIN->value,
            ],
            'unit_types' => [
                UserRoleEnum::SUPER_ADMIN->value,
                UserRoleEnum::ADMIN->value,
            ],
            'products' => [
                UserRoleEnum::SUPER_ADMIN->value,
                UserRoleEnum::ADMIN->value,
                UserRoleEnum::WAREHOUSE->value,
            ],
            'suppliers' => [
                UserRoleEnum::SUPER_ADMIN->value,
                UserRoleEnum::ADMIN->value,
            ],
            'customers' => [
                UserRoleEnum::SUPER_ADMIN->value,
                UserRoleEnum::ADMIN->value,
                UserRoleEnum::SALES->value,
            ],
            'sales' => [
                UserRoleEnum::SUPER_ADMIN->value,
                UserRoleEnum::ADMIN->value,
            ],
            'pos' => [
                UserRoleEnum::SUPER_ADMIN->value,
                UserRoleEnum::ADMIN->value,
                UserRoleEnum::SALES->value,
            ],
            'orders' => [
                UserRoleEnum::SUPER_ADMIN->value,
                UserRoleEnum::ADMIN->value,
                UserRoleEnum::SALES->value,
                UserRoleEnum::WAREHOUSE->value,
            ],
            'transactions' => [
                UserRoleEnum::SUPER_ADMIN->value,
                UserRoleEnum::ADMIN->value,
                UserRoleEnum::FINANCE->value,
            ],
            'sales_points' => [
                UserRoleEnum::SUPER_ADMIN->value,
                UserRoleEnum::ADMIN->value,
                UserRoleEnum::SALES->value,
            ],
            'reports' => [
                UserRoleEnum::SUPER_ADMIN->value,
                UserRoleEnum::ADMIN->value,
                UserRoleEnum::FINANCE->value,
            ],
            'outstanding' => [
                UserRoleEnum::SUPER_ADMIN->value,
                UserRoleEnum::ADMIN->value,
                UserRoleEnum::FINANCE->value,
            ],
            'top_customers' => [
                UserRoleEnum::SUPER_ADMIN->value,
                UserRoleEnum::ADMIN->value,
                UserRoleEnum::SALES->value,
            ],
            'finance' => [
                UserRoleEnum::SUPER_ADMIN->value,
                UserRoleEnum::ADMIN->value,
            ],
            'salaries' => [
                UserRoleEnum::SUPER_ADMIN->value,
                UserRoleEnum::ADMIN->value,
                UserRoleEnum::FINANCE->value,
            ],
            'expenses' => [
                UserRoleEnum::SUPER_ADMIN->value,
                UserRoleEnum::ADMIN->value,
                UserRoleEnum::FINANCE->value,
            ],
            'employees' => [
                UserRoleEnum::SUPER_ADMIN->value,
                UserRoleEnum::ADMIN->value,
                UserRoleEnum::FINANCE->value,
            ],
            'settings' => [
                UserRoleEnum::SUPER_ADMIN->value,
                UserRoleEnum::ADMIN->value,
            ],
            'users' => [
                UserRoleEnum::SUPER_ADMIN->value,
                UserRoleEnum::ADMIN->value,
            ],
        ];
    }

    /**
     * Check if user has permission to access a menu
     */
    public static function canAccess(string $menu, string $userRole): bool
    {
        $permissions = self::getMenuPermissions();
        
        if (!isset($permissions[$menu])) {
            return false;
        }

        return in_array($userRole, $permissions[$menu]);
    }

    /**
     * Get accessible menus for a role
     */
    public static function getAccessibleMenus(string $userRole): array
    {
        $permissions = self::getMenuPermissions();
        $accessibleMenus = [];

        foreach ($permissions as $menu => $roles) {
            if (in_array($userRole, $roles)) {
                $accessibleMenus[] = $menu;
            }
        }

        return $accessibleMenus;
    }
}
