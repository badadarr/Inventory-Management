# 🎯 Database V2 - Quick Reference

## Status Migrasi
✅ **COMPLETED** - 7 Oktober 2025

## 📊 Summary

### Total Tabel: 21 tabel
- **17 tabel bisnis utama** (sesuai ERD v2)
- **4 tabel sistem** (Laravel default + settings)

---

## 📋 Tabel-tabel yang Sudah Dibuat

### Core System Tables
1. ✅ `users` - User management dengan RBAC
2. ✅ `employees` - Data karyawan
3. ✅ `sales` - Data sales/tenaga penjualan

### Master Data Tables
4. ✅ `categories` - Kategori produk
5. ✅ `suppliers` - Data supplier
6. ✅ `unit_types` - Tipe satuan
7. ✅ `products` - Master produk dengan spesifikasi lengkap
8. ✅ `customers` - Data customer dengan tracking komisi

### Pricing & Orders
9. ✅ `product_customer_prices` - Harga khusus per customer
10. ✅ `purchase_orders` - PO dari supplier
11. ✅ `orders` - Sales order ke customer
12. ✅ `order_items` - Detail item order

### Financial & Tracking
13. ✅ `transactions` - Transaksi pembayaran
14. ✅ `stock_movement` - Tracking pergerakan stok
15. ✅ `expenses` - Pengeluaran operasional
16. ✅ `sales_points` - Point komisi sales

### Configuration
17. ✅ `settings` - Konfigurasi sistem

### Laravel System Tables
18. ✅ `password_reset_tokens`
19. ✅ `failed_jobs`
20. ✅ `personal_access_tokens`
21. ✅ `migrations` (Laravel migration table)

---

## 🔄 Status Data

| Status | Keterangan |
|--------|------------|
| ✅ Struktur Database | COMPLETE - Semua tabel sudah dibuat |
| ⏳ Data Seeding | PENDING - Perlu dibuat seeder untuk data dummy |
| ⏳ Models Update | PENDING - Perlu update model Laravel |
| ⏳ API Endpoints | PENDING - Perlu update controllers & routes |
| ⏳ Frontend | PENDING - Perlu update Vue components |

---

## 🔗 Key Features

### ✨ Fitur Baru di V2
- **Role-Based Access Control** (admin, sales, finance, warehouse)
- **Sales Commission System** dengan tracking point
- **Customer Segmentation** (baru vs repeat customer)
- **Custom Pricing** per customer
- **Stock Movement Tracking** yang detail
- **Purchase Order Management** dari supplier
- **Product Specifications** yang lebih lengkap

### 📊 Relasi Penting
```
users
  └─> employees
       └─> sales
            └─> customers
                 └─> orders
                      ├─> order_items
                      ├─> transactions
                      └─> sales_points

categories
  └─> products
       ├─> product_customer_prices
       ├─> order_items
       └─> stock_movement

suppliers
  └─> products
  └─> purchase_orders
```

---

## 📦 Backup Location

```
database/migrations_backup_v1/
```

---

## 🚀 Commands

```bash
# Check migration status
php artisan migrate:status

# Fresh migration (reset + migrate)
php artisan migrate:fresh

# Rollback
php artisan migrate:rollback

# Create seeder
php artisan make:seeder [SeederName]

# Run specific seeder
php artisan db:seed --class=[SeederName]
```

---

## 🎯 Next Actions

1. [ ] Buat seeders untuk data dummy
2. [ ] Update Models (User, Product, Order, dll)
3. [ ] Update Enums untuk match dengan database
4. [ ] Update Controllers & Repositories
5. [ ] Update API Routes
6. [ ] Update Vue Components
7. [ ] Testing seluruh fitur

---

## 📄 Dokumentasi Lengkap

Lihat file: `DATABASE_V2_MIGRATION_GUIDE.md`

---

**Status**: ✅ Database V2 Ready for Development  
**Last Update**: 7 Oktober 2025
