# 🔄 Migration Guide - RBAC Implementation

## Untuk Existing Database

Jika Anda sudah memiliki database dengan users yang ada, ikuti langkah-langkah berikut:

### Step 1: Backup Database
```bash
# Backup database terlebih dahulu
mysqldump -u username -p database_name > backup_before_rbac.sql
```

### Step 2: Jalankan Migration
```bash
php artisan migrate
```

Migration akan menambahkan kolom:
- `role` (default: 'admin')
- `company_id` (nullable)

### Step 3: Update Existing Users

#### Option A: Via Tinker (Recommended)
```bash
php artisan tinker
```

```php
use App\Models\User;
use App\Enums\User\UserRoleEnum;

// Set user pertama sebagai Super Admin
$superAdmin = User::first();
$superAdmin->role = UserRoleEnum::SUPER_ADMIN->value;
$superAdmin->company_id = null;
$superAdmin->save();

// Set user lain sebagai Admin dengan company
$admin = User::find(2);
$admin->role = UserRoleEnum::ADMIN->value;
$admin->company_id = 1;
$admin->save();

// Atau update multiple users sekaligus
User::where('id', '>', 1)->update([
    'role' => UserRoleEnum::ADMIN->value,
    'company_id' => 1
]);
```

#### Option B: Via SQL
```sql
-- Set user pertama sebagai Super Admin
UPDATE users SET role = 'super_admin', company_id = NULL WHERE id = 1;

-- Set user lain sebagai Admin
UPDATE users SET role = 'admin', company_id = 1 WHERE id > 1;
```

#### Option C: Via Seeder (Fresh Install)
```bash
# HATI-HATI: Ini akan menghapus semua data!
php artisan migrate:fresh --seed
```

### Step 4: Verify Migration
```bash
php artisan tinker
```

```php
use App\Models\User;

// Check semua users
User::all(['id', 'name', 'email', 'role', 'company_id']);

// Check role enum
$user = User::first();
$user->role; // Should return UserRoleEnum instance
$user->role->value; // Should return string like 'super_admin'
$user->role_label; // Should return 'Super Admin'
```

### Step 5: Test Login
1. Login dengan user yang sudah di-update
2. Verify menu yang muncul sesuai role
3. Test akses ke berbagai routes

## Untuk Fresh Installation

Jika ini adalah instalasi baru:

```bash
# 1. Run migrations
php artisan migrate

# 2. Run all seeders (includes UserSeeder)
php artisan db:seed

# 3. Login dengan default credentials
# Super Admin: superadmin@example.com / password
# Admin: admin.pta@example.com / password
# Sales: sales@example.com / password
# Warehouse: warehouse@example.com / password
# Finance: finance@example.com / password
```

## Rollback (Jika Diperlukan)

### Rollback Migration
```bash
# Rollback last migration
php artisan migrate:rollback

# Rollback specific migration
php artisan migrate:rollback --step=1
```

### Restore from Backup
```bash
# Restore database dari backup
mysql -u username -p database_name < backup_before_rbac.sql
```

## Mapping Role untuk Existing Users

Gunakan panduan ini untuk menentukan role yang sesuai:

### Super Admin
- Owner perusahaan
- IT Administrator
- User yang perlu akses ke semua company

**Set as Super Admin:**
```php
$user->role = UserRoleEnum::SUPER_ADMIN->value;
$user->company_id = null;
$user->save();
```

### Admin
- Manager per PT/Company
- Admin per cabang
- User yang perlu akses penuh untuk company tertentu

**Set as Admin:**
```php
$user->role = UserRoleEnum::ADMIN->value;
$user->company_id = 1; // ID company
$user->save();
```

### Sales
- Sales representative
- Account manager
- User yang fokus pada penjualan dan customer

**Set as Sales:**
```php
$user->role = UserRoleEnum::SALES->value;
$user->company_id = 1;
$user->save();
```

### Warehouse
- Staff gudang
- Inventory manager
- User yang mengelola produk dan stock

**Set as Warehouse:**
```php
$user->role = UserRoleEnum::WAREHOUSE->value;
$user->company_id = 1;
$user->save();
```

### Finance
- Staff keuangan
- Accounting
- User yang mengelola transaksi dan laporan keuangan

**Set as Finance:**
```php
$user->role = UserRoleEnum::FINANCE->value;
$user->company_id = 1;
$user->save();
```

## Bulk Update Script

Jika Anda memiliki banyak users, gunakan script ini:

```php
// File: database/seeders/UpdateExistingUsersSeeder.php
<?php

namespace Database\Seeders;

use App\Enums\User\UserRoleEnum;
use App\Models\User;
use Illuminate\Database\Seeder;

class UpdateExistingUsersSeeder extends Seeder
{
    public function run(): void
    {
        // Mapping email ke role
        $roleMapping = [
            'owner@company.com' => [
                'role' => UserRoleEnum::SUPER_ADMIN->value,
                'company_id' => null
            ],
            'admin.pta@company.com' => [
                'role' => UserRoleEnum::ADMIN->value,
                'company_id' => 1
            ],
            'sales1@company.com' => [
                'role' => UserRoleEnum::SALES->value,
                'company_id' => 1
            ],
            // Add more mappings...
        ];

        foreach ($roleMapping as $email => $data) {
            User::where('email', $email)->update($data);
        }

        // Set default untuk users yang belum di-mapping
        User::whereNull('role')->update([
            'role' => UserRoleEnum::ADMIN->value,
            'company_id' => 1
        ]);
    }
}
```

Jalankan seeder:
```bash
php artisan db:seed --class=UpdateExistingUsersSeeder
```

## Verification Checklist

Setelah migration, verify hal-hal berikut:

- [ ] Semua users memiliki role yang valid
- [ ] Super Admin tidak memiliki company_id (null)
- [ ] Admin dan role lain memiliki company_id yang valid
- [ ] Login berhasil untuk semua users
- [ ] Menu sidebar muncul sesuai role
- [ ] Route protection berfungsi (403 untuk unauthorized access)
- [ ] Role label muncul di sidebar

## Troubleshooting

### Issue: Column 'role' cannot be null
**Solution:**
```sql
-- Set default value untuk existing rows
UPDATE users SET role = 'admin' WHERE role IS NULL;
```

### Issue: Users tidak bisa login setelah migration
**Solution:**
```bash
# Clear all cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Restart server
php artisan serve
```

### Issue: Menu tidak muncul setelah login
**Solution:**
1. Check role di database: `SELECT id, name, email, role FROM users;`
2. Verify role adalah valid enum value
3. Clear browser cache
4. Check browser console untuk errors

### Issue: 403 Forbidden saat akses route
**Solution:**
1. Verify user role di database
2. Check middleware di routes/web.php
3. Verify RoleMiddleware registered di Kernel.php

## Support

Jika mengalami masalah saat migration:
1. Check log file: `storage/logs/laravel.log`
2. Verify database structure: `DESCRIBE users;`
3. Test dengan fresh database di environment development
4. Restore dari backup jika diperlukan

## Best Practices

1. **Always backup** sebelum migration
2. **Test di development** environment terlebih dahulu
3. **Verify data** setelah migration
4. **Document** role assignment untuk setiap user
5. **Communicate** dengan team tentang perubahan permissions

## Next Steps After Migration

1. ✅ Verify semua users bisa login
2. ✅ Test permissions untuk setiap role
3. ✅ Update user documentation
4. ✅ Train users tentang role baru mereka
5. ✅ Monitor untuk issues atau bugs
6. ✅ Implement company data isolation (optional)
7. ✅ Setup audit logging (optional)
