# 🚀 Quick Start Guide - Fitur Baru

## 📦 Instalasi & Setup

### 1. Jalankan Migrations
```bash
php artisan migrate
```

Ini akan membuat tabel-tabel baru:
- `sales` - Data sales personnel
- `sales_points` - Tracking point penjualan
- Update `customers` - Tambah relasi ke sales
- Update `orders` - Tambah field untuk outstanding report
- Update `products` - Tambah keterangan_tambahan

### 2. Build Frontend Assets
```bash
# Development mode (dengan hot reload)
npm run dev

# Production mode (optimized)
npm run build
```

### 3. Clear Cache (Optional)
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### 4. Start Server
```bash
php artisan serve
```

Akses aplikasi di: `http://localhost:8000`

## 🎯 Cara Menggunakan Fitur Baru

### 1️⃣ Sales Management

**Lokasi Menu:** Master Data → Sales

**Fungsi:**
- Tambah data sales baru
- Edit data sales
- Hapus data sales
- Upload foto sales

**Cara Pakai:**
1. Klik menu "Master Data" → "Sales"
2. Klik tombol "Create Sales"
3. Isi form: Name, Phone, Email, Address, Status
4. Upload foto (optional)
5. Klik "Submit"

**Field yang Wajib:**
- Name ✅
- Status ✅

### 2️⃣ Customer dengan Sales

**Lokasi Menu:** Master Data → Customers

**Update:**
- Sekarang customer bisa di-assign ke sales tertentu
- Field baru: nama_box, nama_sales, nama_owner
- Field baru: bulan_join, tahun_join
- Field baru: status_customer (new/repeat)
- Field baru: status_komisi, harga_komisi_standar, harga_komisi_ekstra

**Auto-Update Status:**
- Customer baru = status "new"
- Saat order kedua kali = otomatis jadi "repeat"

### 3️⃣ Sales Points Recap

**Lokasi Menu:** Reports → Sales Points

**Fungsi:**
- Melihat rekap point penjualan per sales
- Grouping by product type (Box / Kertas Nasi Padang)
- Total cetak dan total points

**Cara Pakai:**
1. Klik menu "Reports" → "Sales Points"
2. Lihat rekap otomatis

**Note:** Data akan muncul setelah ada transaksi dengan sales_points

### 4️⃣ Outstanding Report

**Lokasi Menu:** Reports → Outstanding

**Fungsi:**
- Melihat order yang masih ada outstanding payment
- Filter by tanggal PO
- Detail lengkap: jenis bahan, gramasi, volume, dll

**Cara Pakai:**
1. Klik menu "Reports" → "Outstanding"
2. Pilih Start Date dan End Date (optional)
3. Klik "Apply Filter"
4. Lihat hasil laporan

**Columns:**
- Customer name
- Tanggal PO & Tanggal Kirim
- Jenis Bahan, Gramasi, Volume
- Harga per Pcs, Jumlah Cetak
- Total, Charge, Paid
- Outstanding (highlighted merah)

### 5️⃣ Top Customers Report

**Lokasi Menu:** Reports → Top Customers

**Fungsi:**
- Ranking customer berdasarkan total penjualan
- Filter by date range
- Pilih limit (Top 5/10/20/50)

**Cara Pakai:**
1. Klik menu "Reports" → "Top Customers"
2. Pilih Start Date dan End Date (optional)
3. Pilih Limit (default: Top 10)
4. Klik "Apply Filter"
5. Lihat ranking customer

**Features:**
- Medal badges untuk top 3 (🥇🥈🥉)
- Total lembar cetak
- Total penjualan (Rp)

## 📊 Flow Penggunaan

### Skenario 1: Tambah Sales Baru
```
1. Master Data → Sales → Create Sales
2. Isi data sales
3. Submit
4. Sales siap di-assign ke customer
```

### Skenario 2: Customer Baru dengan Sales
```
1. Master Data → Customers → Create Customer
2. Isi data customer
3. Pilih/isi nama sales
4. Status otomatis "new"
5. Submit
```

### Skenario 3: Order Repeat Customer
```
1. Customer order pertama → status "new"
2. Customer order kedua → status otomatis "repeat"
3. Lihat perubahan di Master Data → Customers
```

### Skenario 4: Cek Outstanding
```
1. Reports → Outstanding
2. Filter by date (optional)
3. Lihat order yang belum lunas
4. Outstanding = Total + Charge - Paid
```

### Skenario 5: Lihat Top Customers
```
1. Reports → Top Customers
2. Pilih periode (optional)
3. Pilih Top berapa (5/10/20/50)
4. Lihat ranking customer terbesar
```

## 🔧 Troubleshooting

### Migration Error
```bash
# Rollback dan migrate ulang
php artisan migrate:rollback
php artisan migrate
```

### Frontend Not Loading
```bash
# Clear cache dan rebuild
npm run build
php artisan cache:clear
```

### Route Not Found
```bash
# Clear route cache
php artisan route:clear
php artisan route:cache
```

### Data Tidak Muncul
1. Pastikan migrations sudah dijalankan
2. Cek apakah ada data di database
3. Clear browser cache
4. Refresh halaman (Ctrl+F5)

## 📝 Database Seeding (Optional)

Jika ingin data sample untuk testing:

```bash
# Buat seeder untuk sales
php artisan make:seeder SalesSeeder

# Edit file database/seeders/SalesSeeder.php
# Jalankan seeder
php artisan db:seed --class=SalesSeeder
```

## 🎨 Customization

### Ubah Warna Badge
Edit file Vue component:
- `Sales/Index.vue` - Line ~60 (status badge)
- `SalesPoint/Index.vue` - Line ~30 (product type badge)

### Ubah Format Currency
Edit file Vue component, function `formatCurrency`:
```javascript
formatCurrency(num) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency', 
        currency: 'IDR'
    }).format(num);
}
```

### Tambah Field Baru
1. Buat migration baru
2. Update Model
3. Update Controller
4. Update Vue component form

## 📞 Support

Jika ada masalah:
1. Cek error di `storage/logs/laravel.log`
2. Cek console browser (F12)
3. Pastikan semua dependencies terinstall
4. Pastikan database connection benar

## ✅ Checklist Sebelum Production

- [ ] Jalankan semua migrations
- [ ] Test semua CRUD operations
- [ ] Test filter dan search
- [ ] Test responsive design
- [ ] Build production assets (`npm run build`)
- [ ] Set `APP_ENV=production` di `.env`
- [ ] Set `APP_DEBUG=false` di `.env`
- [ ] Optimize autoloader: `composer install --optimize-autoloader --no-dev`
- [ ] Cache config: `php artisan config:cache`
- [ ] Cache routes: `php artisan route:cache`
- [ ] Cache views: `php artisan view:cache`

## 🎉 Selesai!

Semua fitur baru sudah siap digunakan. Selamat mencoba! 🚀
