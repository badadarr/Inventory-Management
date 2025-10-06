# 🔧 Troubleshooting Guide

## ✅ FIXED: Error di Halaman Sales

### Error yang Sudah Diperbaiki:

1. **Vue warn: Invalid prop type** - `statTitle` dan `statPercent` menerima Number tapi expect String
   - ✅ Fixed: CardStats.vue sekarang menerima String atau Number

2. **Cannot read properties of undefined (reading 'data')** - CardTable.vue error di line 122
   - ✅ Fixed: Menambahkan null check untuk `paginatedData`

3. **Link accessibility warning** - POST/PUT/PATCH/DELETE links
   - ✅ Fixed: Menambahkan `as="button"` pada logout link

### Jika Masih Error:

1. **Clear cache dan rebuild:**
```bash
php artisan optimize:clear
npm run build
```

2. **Hard refresh browser:**
   - Windows/Linux: Ctrl+Shift+R
   - Mac: Cmd+Shift+R

---

## Masalah: Halaman Sales Tidak Muncul / Blank

### Solusi:

1. **Pastikan migrations sudah dijalankan:**
```bash
php artisan migrate
```

2. **Clear semua cache:**
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

3. **Rebuild frontend assets:**
```bash
npm run build
# atau untuk development
npm run dev
```

4. **Restart server:**
```bash
# Stop server (Ctrl+C)
php artisan serve
```

5. **Cek browser console (F12):**
- Lihat apakah ada error JavaScript
- Lihat Network tab untuk error 404 atau 500

6. **Cek Laravel logs:**
```bash
# Windows
type storage\logs\laravel.log

# Unix/Linux/Mac
tail -f storage/logs/laravel.log
```

### Kemungkinan Penyebab:

#### 1. Route Belum Terdaftar
**Cek:** `routes/web.php` harus ada:
```php
Route::apiResource('sales', SalesController::class);
```

**Fix:**
```bash
php artisan route:list | findstr sales
```

#### 2. Controller Error
**Cek:** Apakah SalesController ada di `app/Http/Controllers/`

**Test:**
```bash
php artisan tinker
>>> app(App\Http\Controllers\SalesController::class);
```

#### 3. Service/Repository Error
**Test di tinker:**
```bash
php artisan tinker
>>> $service = app(App\Services\SalesService::class);
>>> $service->getAll();
```

#### 4. Vue Component Error
**Cek:** File `resources/js/Pages/Sales/Index.vue` ada

**Rebuild:**
```bash
npm run build
```

#### 5. Inertia Error
**Clear Inertia cache:**
```bash
# Refresh browser dengan Ctrl+Shift+R (hard refresh)
```

---

## Masalah: Error 500 Internal Server Error

### Solusi:

1. **Cek error di Laravel log:**
```bash
type storage\logs\laravel.log
```

2. **Enable debug mode:**
Edit `.env`:
```
APP_DEBUG=true
```

3. **Cek database connection:**
```bash
php artisan tinker
>>> DB::connection()->getPdo();
```

---

## Masalah: Data Tidak Muncul di Table

### Solusi:

1. **Cek apakah ada data di database:**
```bash
php artisan tinker
>>> App\Models\Sales::count();
>>> App\Models\Sales::all();
```

2. **Tambah data dummy:**
```bash
php artisan tinker
>>> App\Models\Sales::create([
    'name' => 'Test Sales',
    'phone' => '08123456789',
    'email' => 'test@example.com',
    'status' => 'active'
]);
```

3. **Refresh halaman**

---

## Masalah: Photo Upload Error

### Solusi:

1. **Pastikan storage linked:**
```bash
php artisan storage:link
```

2. **Cek permission folder:**
```bash
# Windows (run as admin)
icacls storage /grant Users:F /T

# Unix/Linux/Mac
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

3. **Cek max upload size di php.ini:**
```
upload_max_filesize = 10M
post_max_size = 10M
```

---

## Masalah: Form Validation Error

### Solusi:

1. **Cek console browser untuk error message**

2. **Pastikan semua required fields terisi:**
- Name (required)
- Status (required)

3. **Cek format email jika diisi**

---

## Masalah: Menu Tidak Muncul di Sidebar

### Solusi:

1. **Clear browser cache:**
- Ctrl+Shift+Delete
- Clear cache and cookies

2. **Hard refresh:**
- Ctrl+Shift+R (Windows/Linux)
- Cmd+Shift+R (Mac)

3. **Rebuild frontend:**
```bash
npm run build
```

4. **Cek SidebarDark.vue sudah terupdate**

---

## Masalah: Outstanding/Top Customers Report Kosong

### Solusi:

1. **Pastikan ada data orders dengan outstanding:**
```bash
php artisan tinker
>>> App\Models\Order::where('due', '>', 0)->count();
```

2. **Cek filter date range tidak terlalu sempit**

3. **Reset filter dengan refresh halaman**

---

## Quick Diagnostic Commands

### Cek semua route:
```bash
php artisan route:list
```

### Cek semua migrations:
```bash
php artisan migrate:status
```

### Cek database tables:
```bash
php artisan tinker
>>> Schema::hasTable('sales');
>>> Schema::hasTable('sales_points');
```

### Test controller:
```bash
php artisan tinker
>>> $controller = app(App\Http\Controllers\SalesController::class);
>>> $controller->index();
```

### Cek Vue component compile:
```bash
npm run build
# Lihat output untuk error
```

---

## Masih Bermasalah?

### Langkah Terakhir:

1. **Fresh install:**
```bash
# Backup database dulu!
php artisan migrate:fresh
php artisan migrate
```

2. **Reinstall dependencies:**
```bash
composer install
npm install
npm run build
```

3. **Restart everything:**
```bash
# Stop server
# Clear all cache
php artisan optimize:clear
# Start server
php artisan serve
```

4. **Cek dokumentasi:**
- Baca `QUICK_START_GUIDE.md`
- Baca `COMPLETE_FEATURES_LIST.md`

---

## Contact Support

Jika masih error, kirim informasi berikut:
1. Error message dari Laravel log
2. Error message dari browser console
3. Screenshot error
4. Output dari `php artisan route:list | findstr sales`
5. Output dari `php artisan migrate:status`
