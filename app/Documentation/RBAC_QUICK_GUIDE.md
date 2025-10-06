# 🚀 RBAC Quick Start Guide

## Instalasi

### 1. Jalankan Migration
```bash
php artisan migrate
```

### 2. Seed User dengan Role
```bash
php artisan db:seed --class=UserSeeder
```

Atau jalankan semua seeder:
```bash
php artisan migrate:fresh --seed
```

✅ **Status**: Seeder berhasil dijalankan! 7 users telah dibuat dengan role yang sesuai.

## Default Login Credentials

| Role | Email | Password | Akses |
|------|-------|----------|-------|
| **Super Admin** | superadmin@example.com | password | Semua menu |
| **Admin PT A** | admin.pta@example.com | password | Semua menu PT A |
| **Admin PT B** | admin.ptb@example.com | password | Semua menu PT B |
| **Sales** | sales@example.com | password | Dashboard, Customers, POS, Orders, Sales Points, Top Customers |
| **Warehouse** | warehouse@example.com | password | Dashboard, Products, Orders |
| **Finance** | finance@example.com | password | Dashboard, Transactions, Salaries, Expenses, Outstanding |

## Role Permissions

### 🔴 Super Admin
- Akses ke **SEMUA** menu dan fitur
- Tidak terbatas oleh company

### 🟠 Admin
- Akses ke **SEMUA** menu untuk PT/Company mereka
- Terbatas pada data company_id mereka
- Dapat mengelola Users

### 🟢 Sales
- Dashboard ✅
- Customers ✅
- POS ✅
- Orders ✅
- Sales Points ✅
- Top Customers Report ✅

### 🔵 Warehouse
- Dashboard ✅
- Products ✅
- Orders (view & update) ✅

### 🟣 Finance
- Dashboard ✅
- Transactions ✅
- Salaries ✅
- Expenses ✅
- Outstanding Report ✅

## Testing

1. **Login sebagai Super Admin**
   - Email: `superadmin@example.com`
   - Password: `password`
   - Cek: Semua menu harus terlihat

2. **Login sebagai Sales**
   - Email: `sales@example.com`
   - Password: `password`
   - Cek: Hanya menu Dashboard, Customers, POS, Orders, Sales Points, Top Customers yang terlihat

3. **Login sebagai Warehouse**
   - Email: `warehouse@example.com`
   - Password: `password`
   - Cek: Hanya menu Dashboard, Products, Orders yang terlihat

4. **Login sebagai Finance**
   - Email: `finance@example.com`
   - Password: `password`
   - Cek: Hanya menu Dashboard, Transactions, Salaries, Expenses, Outstanding yang terlihat

## Troubleshooting

### Menu tidak muncul setelah login?
```bash
# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Rebuild frontend
npm run build
```

### User tidak bisa akses route (403 Forbidden)?
- Pastikan user memiliki role yang sesuai di database
- Check tabel `users` kolom `role`
- Verify middleware di `routes/web.php`

### Role label tidak muncul di sidebar?
- Pastikan migration sudah dijalankan
- Check kolom `role` di tabel `users` sudah terisi
- Clear browser cache

## User Management

### Akses
- ✅ Super Admin
- ✅ Admin
- ❌ Sales, Warehouse, Finance

### Fitur
- Create, Edit, Delete users
- Assign roles
- Set company information
- Password management

Lihat dokumentasi lengkap di: `USER_MANAGEMENT_GUIDE.md`

## Dokumentasi Lengkap

Lihat dokumentasi lengkap di: `app/Documentation/RBAC_IMPLEMENTATION.md`

## Support

Jika ada pertanyaan atau issue, silakan check:
1. Migration files di `database/migrations/`
2. Middleware di `app/Http/Middleware/RoleMiddleware.php`
3. Permission Helper di `app/Helpers/PermissionHelper.php`
4. Sidebar Component di `resources/js/Components/Sidebar/SidebarDark.vue`
