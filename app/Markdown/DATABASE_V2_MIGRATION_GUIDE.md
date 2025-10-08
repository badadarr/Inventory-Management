# Database Migration V2 - PostgreSQL Schema

## Ringkasan Perubahan

Dokumen ini menjelaskan perubahan struktur database dari versi 1 ke versi 2 untuk Inventory Management System.

## Tanggal Migrasi
**7 Oktober 2025**

---

## 📋 Daftar Tabel (17 Tabel Utama)

### 1. **users** (TABLE 17)
Tabel untuk manajemen pengguna sistem dengan role-based access.

**Kolom:**
- `id` - Primary key
- `name` - Nama user
- `email` - Email (unique)
- `password` - Password (hashed)
- `role` - ENUM: admin, sales, finance, warehouse (default: admin)
- `photo` - Foto profil
- `company_name` - Nama perusahaan (unique)
- `company_id` - ID perusahaan
- `email_verified_at` - Timestamp verifikasi email
- `remember_token` - Token remember me
- `created_at`, `updated_at` - Timestamps

---

### 2. **employees** (TABLE 18)
Tabel data karyawan perusahaan.

**Kolom:**
- `id` - Primary key
- `user_id` - FK ke users (nullable, ON DELETE SET NULL)
- `name` - Nama karyawan
- `phone` - Nomor telepon
- `designation` - Jabatan
- `address` - Alamat
- `salary` - Gaji (DECIMAL 20,2)
- `photo` - Foto karyawan
- `nid` - Nomor identitas
- `status` - ENUM: active, resigned (default: active)
- `joining_date` - Tanggal bergabung
- `created_at`, `updated_at` - Timestamps

---

### 3. **sales** (TABLE 19)
Tabel data sales/tenaga penjualan.

**Kolom:**
- `id` - Primary key
- `employee_id` - FK ke employees (ON DELETE CASCADE)
- `name` - Nama sales
- `email` - Email sales
- `phone` - Nomor telepon
- `address` - Alamat
- `photo` - Foto sales
- `status` - ENUM: active, inactive (default: active)
- `created_at`, `updated_at` - Timestamps

---

### 4. **customers** (TABLE 20)
Tabel data pelanggan dengan tracking repeat order.

**Kolom:**
- `id` - Primary key
- `sales_id` - FK ke sales (nullable, ON DELETE SET NULL)
- `name` - Nama customer
- `email` - Email (unique)
- `phone` - Nomor telepon
- `address` - Alamat
- `nama_box` - Nama box
- `nama_owner` - Nama pemilik
- `bulan_join` - Bulan bergabung
- `tahun_join` - Tahun bergabung
- `status_customer` - ENUM: baru, repeat (default: baru)
- `repeat_order_count` - Jumlah repeat order (INTEGER, default: 0)
- `status_komisi` - Status komisi
- `harga_komisi_standar` - Harga komisi standar (DECIMAL 20,2)
- `harga_komisi_extra` - Harga komisi extra (DECIMAL 20,2)
- `created_at`, `updated_at` - Timestamps

---

### 5. **categories** (TABLE 21)
Tabel kategori produk.

**Kolom:**
- `id` - Primary key
- `name` - Nama kategori (unique)
- `description` - Deskripsi kategori
- `created_at`, `updated_at` - Timestamps

---

### 6. **suppliers** (TABLE 22)
Tabel data supplier/pemasok.

**Kolom:**
- `id` - Primary key
- `name` - Nama supplier
- `email` - Email supplier
- `phone` - Nomor telepon
- `photo` - Foto supplier
- `address` - Alamat
- `shop_name` - Nama toko
- `created_at`, `updated_at` - Timestamps

---

### 7. **unit_types** (TABLE 23)
Tabel tipe satuan produk (kg, pcs, box, dll).

**Kolom:**
- `id` - Primary key
- `name` - Nama tipe satuan
- `symbol` - Simbol satuan
- `created_at`, `updated_at` - Timestamps

---

### 8. **products** (TABLE 24)
Tabel master produk dengan spesifikasi lengkap.

**Kolom:**
- `id` - Primary key
- `category_id` - FK ke categories (nullable, ON DELETE SET NULL)
- `supplier_id` - FK ke suppliers (nullable, ON DELETE SET NULL)
- `unit_type_id` - FK ke unit_types (nullable, ON DELETE SET NULL)
- `product_code` - Kode produk
- `name` - Nama produk
- `bahan` - Jenis bahan
- `gramatur` - Gramatur bahan
- `ukuran` - Ukuran produk
- `ukuran_potongan_1` - Ukuran potongan 1
- `ukuran_plano_1` - Ukuran plano 1
- `ukuran_potongan_2` - Ukuran potongan 2
- `ukuran_plano_2` - Ukuran plano 2
- `buying_price` - Harga beli (DECIMAL 20,2)
- `selling_price` - Harga jual (DECIMAL 20,2)
- `quantity` - Stok tersedia (DECIMAL 20,2)
- `reorder_level` - Level reorder (DECIMAL 20,2)
- `photo` - Foto produk
- `status` - ENUM: active, inactive (default: active)
- `keterangan_tambahan` - Keterangan tambahan
- `alamat_pengiriman` - Alamat pengiriman
- `created_at`, `updated_at` - Timestamps

---

### 9. **product_customer_prices** (TABLE 25)
Tabel harga khusus produk per customer.

**Kolom:**
- `id` - Primary key
- `product_id` - FK ke products (ON DELETE CASCADE)
- `customer_id` - FK ke customers (ON DELETE CASCADE)
- `custom_price` - Harga custom (DECIMAL 20,2)
- `notes` - Catatan
- `created_at`, `updated_at` - Timestamps

---

### 10. **purchase_orders** (TABLE 26)
Tabel purchase order dari supplier.

**Kolom:**
- `id` - Primary key
- `supplier_id` - FK ke suppliers (nullable, ON DELETE SET NULL)
- `order_number` - Nomor PO (unique)
- `total` - Total pembelian (DECIMAL 20,2)
- `paid` - Jumlah dibayar (DECIMAL 20,2)
- `status` - ENUM: pending, received, cancelled (default: pending)
- `order_date` - Tanggal order
- `received_date` - Tanggal diterima
- `created_by` - FK ke users (nullable, ON DELETE SET NULL)
- `created_at`, `updated_at` - Timestamps

---

### 11. **orders** (TABLE 27)
Tabel order penjualan kepada customer.

**Kolom:**
- `id` - Primary key
- `customer_id` - FK ke customers (nullable, ON DELETE SET NULL)
- `sales_id` - FK ke sales (nullable, ON DELETE SET NULL)
- `order_number` - Nomor order (unique)
- `sub_total` - Sub total (DECIMAL 20,2)
- `tax_total` - Total pajak (DECIMAL 20,2)
- `discount_total` - Total diskon (DECIMAL 20,2)
- `total` - Total order (DECIMAL 20,2)
- `paid` - Jumlah dibayar (DECIMAL 20,2)
- `due` - Sisa hutang (DECIMAL 20,2)
- `charge` - Biaya tambahan (DECIMAL 20,2)
- `status` - ENUM: pending, completed, cancelled (default: pending)
- `tanggal_po` - Tanggal PO
- `tanggal_kirim` - Tanggal kirim
- `jenis_bahan` - Jenis bahan
- `gramasi` - Gramasi
- `volume` - Volume
- `harga_jual_pcs` - Harga jual per pcs (DECIMAL 20,2)
- `jumlah_cetak` - Jumlah cetak
- `catatan` - Catatan order
- `created_by` - FK ke users (nullable, ON DELETE SET NULL)
- `created_at`, `updated_at` - Timestamps

---

### 12. **order_items** (TABLE 28)
Tabel detail item order.

**Kolom:**
- `id` - Primary key
- `order_id` - FK ke orders (ON DELETE CASCADE)
- `product_id` - FK ke products (nullable, ON DELETE SET NULL)
- `unit_price` - Harga per unit (DECIMAL 20,2)
- `quantity` - Jumlah (DECIMAL 20,2)
- `discount` - Diskon (DECIMAL 20,2)
- `subtotal` - Subtotal (DECIMAL 20,2)
- `created_at`, `updated_at` - Timestamps

---

### 13. **transactions** (TABLE 29)
Tabel transaksi pembayaran.

**Kolom:**
- `id` - Primary key
- `order_id` - FK ke orders (ON DELETE CASCADE)
- `transaction_number` - Nomor transaksi (unique)
- `transaction_type` - ENUM: payment, refund, adjustment (default: payment)
- `amount` - Jumlah transaksi (DECIMAL 20,2)
- `paid_through` - Metode pembayaran
- `transaction_date` - Tanggal transaksi (default: CURRENT_DATE)
- `created_at`, `updated_at` - Timestamps

---

### 14. **stock_movement** (TABLE 30)
Tabel pergerakan stok produk.

**Kolom:**
- `id` - Primary key
- `product_id` - FK ke products (ON DELETE CASCADE)
- `reference_type` - ENUM: purchase_order, sales_order, adjustment
- `reference_id` - ID referensi
- `movement_type` - ENUM: in, out
- `quantity` - Jumlah pergerakan (DECIMAL 20,2)
- `balance_after` - Saldo setelah pergerakan (DECIMAL 20,2)
- `created_by` - FK ke users (nullable, ON DELETE SET NULL)
- `notes` - Catatan
- `created_at`, `updated_at` - Timestamps

---

### 15. **expenses** (TABLE 31)
Tabel pengeluaran/biaya operasional.

**Kolom:**
- `id` - Primary key
- `category` - Kategori pengeluaran
- `name` - Nama pengeluaran
- `description` - Deskripsi
- `amount` - Jumlah (DECIMAL 20,2)
- `expense_date` - Tanggal pengeluaran
- `created_at`, `updated_at` - Timestamps

---

### 16. **sales_points** (TABLE 32)
Tabel point sales untuk sistem komisi.

**Kolom:**
- `id` - Primary key
- `sales_id` - FK ke sales (ON DELETE CASCADE)
- `order_id` - FK ke orders (ON DELETE CASCADE)
- `product_category_id` - FK ke categories (nullable, ON DELETE SET NULL)
- `jumlah_cetak` - Jumlah cetak (INTEGER, default: 0)
- `points` - Point yang didapat (DECIMAL 20,2)
- `created_at`, `updated_at` - Timestamps

---

### 17. **settings** (TABLE 33)
Tabel konfigurasi sistem.

**Kolom:**
- `id` - Primary key
- `name` - Nama setting
- `val` - Value setting
- `group` - Group setting (default: 'default')
- `created_at`, `updated_at` - Timestamps

---

## 🔗 Relasi Foreign Key

### Cascade on Delete
- `sales.employee_id` → `employees.id`
- `product_customer_prices.product_id` → `products.id`
- `product_customer_prices.customer_id` → `customers.id`
- `order_items.order_id` → `orders.id`
- `transactions.order_id` → `orders.id`
- `stock_movement.product_id` → `products.id`
- `sales_points.sales_id` → `sales.id`
- `sales_points.order_id` → `orders.id`

### Set Null on Delete
- `employees.user_id` → `users.id`
- `customers.sales_id` → `sales.id`
- `products.category_id` → `categories.id`
- `products.supplier_id` → `suppliers.id`
- `products.unit_type_id` → `unit_types.id`
- `purchase_orders.supplier_id` → `suppliers.id`
- `purchase_orders.created_by` → `users.id`
- `orders.customer_id` → `customers.id`
- `orders.sales_id` → `sales.id`
- `orders.created_by` → `users.id`
- `order_items.product_id` → `products.id`
- `stock_movement.created_by` → `users.id`
- `sales_points.product_category_id` → `categories.id`

---

## 📊 Perubahan Signifikan dari V1 ke V2

### ✅ Tabel Baru
1. **sales** - Tabel khusus untuk sales/tenaga penjualan
2. **product_customer_prices** - Harga khusus per customer
3. **purchase_orders** - Purchase order dari supplier
4. **stock_movement** - Tracking pergerakan stok
5. **sales_points** - Sistem point untuk komisi sales

### 📝 Tabel yang Dimodifikasi
1. **users** - Ditambahkan kolom `role`, `company_name`, `company_id`
2. **employees** - Ditambahkan relasi ke `users`
3. **customers** - Ditambahkan:
   - `sales_id` (relasi ke sales)
   - Fields untuk tracking: `nama_box`, `nama_owner`, `bulan_join`, `tahun_join`
   - `status_customer` (baru/repeat)
   - `repeat_order_count`
   - Fields komisi: `status_komisi`, `harga_komisi_standar`, `harga_komisi_extra`
4. **products** - Ditambahkan:
   - Fields spesifikasi: `bahan`, `gramatur`, `ukuran`
   - Fields ukuran: `ukuran_potongan_1`, `ukuran_plano_1`, `ukuran_potongan_2`, `ukuran_plano_2`
   - `reorder_level` untuk auto-reorder
   - `keterangan_tambahan`, `alamat_pengiriman`
5. **orders** - Ditambahkan:
   - `sales_id` (relasi ke sales)
   - `charge` (biaya tambahan)
   - Fields produksi: `tanggal_po`, `tanggal_kirim`, `jenis_bahan`, `gramasi`, `volume`, `harga_jual_pcs`, `jumlah_cetak`
   - `catatan` untuk notes
   - `created_by` untuk audit trail

### ❌ Tabel yang Dihapus/Diganti
1. **carts** - Dihapus (tidak relevan untuk v2)
2. **salaries** - Dihapus (bisa dihandle dari employee record)

---

## 🔐 Keamanan & Audit Trail

Beberapa tabel memiliki kolom `created_by` yang merekam user yang membuat record:
- `purchase_orders.created_by`
- `orders.created_by`
- `stock_movement.created_by`

---

## 🎯 Fitur Bisnis yang Didukung

### 1. **Role-Based Access Control (RBAC)**
   - 4 role: admin, sales, finance, warehouse
   - Implementasi di tabel `users`

### 2. **Sales Commission System**
   - Tracking sales performance via `sales_points`
   - Komisi per customer via `customers` (harga_komisi_standar, harga_komisi_extra)

### 3. **Customer Segmentation**
   - Status customer: baru vs repeat
   - Tracking repeat order count

### 4. **Custom Pricing**
   - Harga khusus per customer via `product_customer_prices`

### 5. **Inventory Management**
   - Real-time stock tracking via `stock_movement`
   - Auto-reorder level detection

### 6. **Multi-source Transactions**
   - Purchase orders dari supplier
   - Sales orders ke customer
   - Stock adjustments

---

## 📦 File Backup

Migrasi lama telah di-backup ke folder:
```
database/migrations_backup_v1/
```

---

## 🚀 Cara Menjalankan Migrasi

```bash
# Reset database dan jalankan migrasi baru
php artisan migrate:fresh

# Atau dengan seeding (jika ada)
php artisan migrate:fresh --seed

# Cek status migrasi
php artisan migrate:status
```

---

## 📌 Catatan Penting

1. **Database Type**: PostgreSQL
2. **Decimal Precision**: Menggunakan DECIMAL(20,2) untuk semua field currency/numeric
3. **Date Handling**: Menggunakan DATE type untuk tanggal, TIMESTAMP untuk created_at/updated_at
4. **Enum Constraints**: Semua enum values didefinisikan di migration
5. **Foreign Key Actions**: 
   - CASCADE untuk relasi parent-child yang strict
   - SET NULL untuk relasi yang bisa null

---

## 👨‍💻 Developer Notes

### Next Steps:
1. ✅ Update Models untuk reflect struktur baru
2. ✅ Update Seeders untuk data dummy
3. ✅ Update Controllers & Repositories
4. ✅ Update Frontend (Vue components)
5. ✅ Update API endpoints
6. ✅ Update Enums
7. ✅ Testing

---

**Last Updated**: 7 Oktober 2025  
**Version**: 2.0  
**Database**: PostgreSQL
