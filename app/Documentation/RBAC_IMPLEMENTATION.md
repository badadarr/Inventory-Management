# 🔐 RBAC Implementation Guide

## Overview
Sistem ini telah diimplementasikan dengan Role-Based Access Control (RBAC) untuk mengatur akses menu dan fitur berdasarkan role user.

## Roles

### 1. Super Admin
- **Akses**: Semua menu dan fitur
- **Deskripsi**: Role tertinggi dengan akses penuh ke seluruh sistem
- **Use Case**: Owner atau IT Administrator

### 2. Admin
- **Akses**: Semua menu dan fitur untuk PT/Company mereka
- **Deskripsi**: Administrator untuk masing-masing PT/Company
- **Use Case**: Manager atau Admin PT

### 3. Sales
- **Akses**: 
  - Dashboard
  - Customers (view, create, edit)
  - POS
  - Orders
  - Sales Points
  - Top Customers Report
- **Deskripsi**: Tim sales yang fokus pada penjualan dan customer
- **Use Case**: Sales Representative

### 4. Warehouse
- **Akses**:
  - Dashboard
  - Products (view, create, edit)
  - Orders (view, update status)
- **Deskripsi**: Tim gudang yang mengelola produk dan order
- **Use Case**: Warehouse Staff

### 5. Finance
- **Akses**:
  - Dashboard
  - Transactions
  - Salaries
  - Expenses
  - Outstanding Report
- **Deskripsi**: Tim keuangan yang mengelola transaksi dan laporan keuangan
- **Use Case**: Finance Staff

## Permission Matrix

| Menu/Feature | Super Admin | Admin | Sales | Warehouse | Finance |
|--------------|-------------|-------|-------|-----------|---------|
| Dashboard | ✅ | ✅ | ✅ | ✅ | ✅ |
| Categories | ✅ | ✅ | ❌ | ❌ | ❌ |
| Unit Types | ✅ | ✅ | ❌ | ❌ | ❌ |
| Products | ✅ | ✅ | ❌ | ✅ | ❌ |
| Suppliers | ✅ | ✅ | ❌ | ❌ | ❌ |
| Customers | ✅ | ✅ | ✅ | ❌ | ❌ |
| Sales | ✅ | ✅ | ❌ | ❌ | ❌ |
| POS | ✅ | ✅ | ✅ | ❌ | ❌ |
| Orders | ✅ | ✅ | ✅ | ✅ | ❌ |
| Transactions | ✅ | ✅ | ❌ | ❌ | ✅ |
| Sales Points | ✅ | ✅ | ✅ | ❌ | ❌ |
| Outstanding | ✅ | ✅ | ❌ | ❌ | ✅ |
| Top Customers | ✅ | ✅ | ✅ | ❌ | ❌ |
| Salaries | ✅ | ✅ | ❌ | ❌ | ✅ |
| Expenses | ✅ | ✅ | ❌ | ❌ | ✅ |
| Employees | ✅ | ✅ | ❌ | ❌ | ❌ |
| Settings | ✅ | ✅ | ❌ | ❌ | ❌ |

## Implementation Details

### Backend

#### 1. Database Migration
```php
// Migration: add_role_to_users_table
- role: string (default: 'admin')
- company_id: unsignedBigInteger (nullable)
```

#### 2. User Model
```php
// Enum casting
protected $casts = [
    'role' => UserRoleEnum::class,
];

// Helper methods
- isSuperAdmin(): bool
- isAdmin(): bool
- isSales(): bool
- isWarehouse(): bool
- isFinance(): bool
- hasRole(string|array $roles): bool
- canAccessAllMenus(): bool
```

#### 3. Middleware
```php
// RoleMiddleware
Route::middleware('role:super_admin,admin')->group(function () {
    // Routes here
});
```

#### 4. Permission Helper
```php
// PermissionHelper
- getMenuPermissions(): array
- canAccess(string $menu, string $userRole): bool
- getAccessibleMenus(string $userRole): array
```

### Frontend

#### 1. Inertia Shared Data
```javascript
// HandleInertiaRequests
auth: {
    user: $request->user(),
    permissions: PermissionHelper::getAccessibleMenus($request->user()->role->value)
}
```

#### 2. Vue Composable
```javascript
// usePermissions()
import { usePermissions } from '@/Utils/permissions.js';

const permissions = usePermissions();
permissions.canAccessMasterData();
permissions.canAccessPOS();
// etc...
```

#### 3. Sidebar Component
```vue
<!-- Conditional rendering based on permissions -->
<SidebarCollapsibleItemDark
    v-if="permissions.canAccessMasterData()"
    title="Master Data"
    ...
>
```

## Usage

### Creating New User with Role
```php
use App\Enums\User\UserRoleEnum;

User::create([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'password' => Hash::make('password'),
    'role' => UserRoleEnum::SALES->value,
    'company_name' => 'PT. Company A',
    'company_id' => 1,
]);
```

### Checking Permission in Controller
```php
// Check if user has specific role
if ($request->user()->isSuperAdmin()) {
    // Super admin logic
}

// Check if user has one of multiple roles
if ($request->user()->hasRole(['super_admin', 'admin'])) {
    // Admin logic
}

// Check if user can access all menus
if ($request->user()->canAccessAllMenus()) {
    // Full access logic
}
```

### Protecting Routes
```php
// Single role
Route::middleware('role:super_admin')->group(function () {
    // Routes
});

// Multiple roles
Route::middleware('role:super_admin,admin,sales')->group(function () {
    // Routes
});
```

### Frontend Permission Check
```vue
<script setup>
import { usePermissions } from '@/Utils/permissions.js';

const permissions = usePermissions();
</script>

<template>
    <div v-if="permissions.canAccessPOS()">
        <!-- POS content -->
    </div>
</template>
```

## Default Users (Seeder)

| Email | Password | Role | Company |
|-------|----------|------|---------|
| superadmin@example.com | password | Super Admin | Head Office |
| admin.pta@example.com | password | Admin | PT. Company A |
| admin.ptb@example.com | password | Admin | PT. Company B |
| sales@example.com | password | Sales | PT. Company A |
| warehouse@example.com | password | Warehouse | PT. Company A |
| finance@example.com | password | Finance | PT. Company A |

## Migration Steps

1. **Run migration**:
   ```bash
   php artisan migrate
   ```

2. **Seed users**:
   ```bash
   php artisan db:seed --class=UserSeeder
   ```

3. **Or fresh migration with all seeders**:
   ```bash
   php artisan migrate:fresh --seed
   ```

## Security Notes

- ✅ Middleware protection pada semua routes
- ✅ Frontend menu visibility berdasarkan role
- ✅ Backend validation untuk setiap action
- ✅ Company isolation untuk Admin (company_id)
- ✅ Super Admin bypass untuk semua restrictions

## Future Enhancements

1. **Dynamic Permissions**: Implementasi permission yang lebih granular (create, read, update, delete)
2. **Company Isolation**: Filter data berdasarkan company_id untuk Admin
3. **Audit Log**: Track semua actions berdasarkan user dan role
4. **Permission Management UI**: Interface untuk manage permissions secara dinamis
5. **Role Hierarchy**: Implementasi role inheritance

## Troubleshooting

### Issue: User tidak bisa akses menu setelah login
**Solution**: 
- Pastikan user memiliki role yang valid
- Check migration sudah dijalankan
- Verify permissions di PermissionHelper

### Issue: Menu masih muncul meskipun tidak ada permission
**Solution**:
- Check conditional rendering di Sidebar component
- Verify usePermissions() composable
- Clear browser cache

### Issue: 403 Forbidden saat akses route
**Solution**:
- Check middleware di routes/web.php
- Verify user role di database
- Check RoleMiddleware implementation
