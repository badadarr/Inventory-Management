# 🧪 Panduan Testing dengan 1000+ Data

## 📋 Deskripsi

Seeder ini dibuat untuk menguji konsistensi dan performa sistem dengan data dalam jumlah besar (1000+ records).

## 📊 Data yang Dibuat

| Modul | Jumlah Data |
|-------|-------------|
| Sales | 50 |
| Customers | 200 |
| Products | 150 |
| Orders | 300 |
| Order Items | 600-1500 (2-5 per order) |
| Sales Points | ~300 |
| Employees | 50 |
| Salaries | 100 |
| Expenses | 100 |
| Transactions | 50 |
| **TOTAL** | **1000+** |

## 🚀 Cara Menjalankan

### Opsi 1: Tanpa Fresh Migration
```bash
php artisan db:seed --class=LargeDataSeeder
```

### Opsi 2: Dengan Fresh Migration (Hapus semua data)
```bash
php artisan db:seed-large --fresh
```

### Opsi 3: Manual Seeder
```bash
php artisan db:seed --class=Database\\Seeders\\LargeDataSeeder
```

## ⚠️ Perhatian

- Seeder ini akan menambahkan data ke database yang sudah ada
- Gunakan opsi `--fresh` hanya jika ingin menghapus semua data dan mulai dari awal
- Proses seeding membutuhkan waktu 30-60 detik tergantung spesifikasi server
- Pastikan database memiliki cukup ruang penyimpanan

## ✅ Checklist Testing

Setelah seeding, lakukan pengujian berikut:

### 1. Dashboard
- [ ] Metrics ditampilkan dengan benar
- [ ] Chart loading tanpa error
- [ ] Response time < 3 detik

### 2. POS
- [ ] Pencarian produk berfungsi
- [ ] Tambah ke cart lancar
- [ ] Checkout berhasil

### 3. Orders
- [ ] List orders dengan pagination
- [ ] Filter dan search berfungsi
- [ ] Detail order loading cepat
- [ ] Update status berhasil

### 4. Reports
- [ ] Outstanding Report
  - [ ] Filter tanggal berfungsi
  - [ ] Data akurat
  - [ ] Export berhasil
- [ ] Top Customers
  - [ ] Ranking benar
  - [ ] Filter top N berfungsi
- [ ] Sales Points
  - [ ] Perhitungan poin akurat
  - [ ] Leaderboard ditampilkan

### 5. Master Data
- [ ] Products pagination smooth
- [ ] Customers list loading cepat
- [ ] Search dan filter responsif

### 6. Performance
- [ ] Query time < 1 detik
- [ ] Memory usage normal
- [ ] No N+1 query issues

## 🔍 Monitoring

### Check Database Size
```sql
SELECT 
    table_name AS 'Table',
    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Size (MB)'
FROM information_schema.TABLES
WHERE table_schema = 'your_database_name'
ORDER BY (data_length + index_length) DESC;
```

### Check Record Counts
```sql
SELECT 
    'sales' as table_name, COUNT(*) as count FROM sales
UNION ALL
SELECT 'customers', COUNT(*) FROM customers
UNION ALL
SELECT 'products', COUNT(*) FROM products
UNION ALL
SELECT 'orders', COUNT(*) FROM orders
UNION ALL
SELECT 'order_items', COUNT(*) FROM order_items
UNION ALL
SELECT 'employees', COUNT(*) FROM employees
UNION ALL
SELECT 'salaries', COUNT(*) FROM salaries
UNION ALL
SELECT 'expenses', COUNT(*) FROM expenses
UNION ALL
SELECT 'transactions', COUNT(*) FROM transactions;
```

## 🐛 Troubleshooting

### Error: Memory Limit
```bash
php -d memory_limit=512M artisan db:seed-large
```

### Error: Timeout
Tingkatkan max_execution_time di php.ini atau:
```bash
php -d max_execution_time=300 artisan db:seed-large
```

### Error: Foreign Key Constraint
Pastikan seeder dasar sudah dijalankan:
```bash
php artisan migrate:fresh --seed
php artisan db:seed --class=LargeDataSeeder
```

## 📈 Expected Results

- ✅ Semua data tersimpan tanpa error
- ✅ Relasi antar tabel konsisten
- ✅ Dashboard menampilkan metrics yang akurat
- ✅ Reports berjalan lancar
- ✅ Tidak ada N+1 query
- ✅ Response time tetap optimal

## 🔄 Reset Data

Untuk menghapus data testing dan kembali ke data awal:
```bash
php artisan migrate:fresh --seed
```
