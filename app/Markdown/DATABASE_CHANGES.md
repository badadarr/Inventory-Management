# Perubahan Database - Field Baru

## 1. Tabel Products (Master Data)
Field baru yang ditambahkan:
- `bahan` (string, nullable) - Jenis bahan produk
- `gramatur` (string, nullable) - Gramatur bahan
- `ukuran` (string, nullable) - Ukuran produk
- `ukuran_potongan_1` (string, nullable) - Ukuran potongan pertama
- `ukuran_plano_1` (string, nullable) - Ukuran plano pertama
- `ukuran_potongan_2` (string, nullable) - Ukuran potongan kedua
- `ukuran_plano_2` (string, nullable) - Ukuran plano kedua
- `alamat_pengiriman` (text, nullable) - Alamat pengiriman produk

**Migration:** `2025_10_05_004859_add_master_data_fields_to_products_table.php`

---

## 2. Tabel Customers (Customer Baru)
Field baru yang ditambahkan:
- `nama_box` (string, nullable) - Nama box customer
- `nama_sales` (string, nullable) - Nama sales yang menangani
- `nama_owner` (string, nullable) - Nama owner customer
- `bulan_join` (string, nullable) - Bulan bergabung
- `tahun_join` (string, nullable) - Tahun bergabung
- `status_customer` (string, default: 'new') - Status customer (new/repeat)
- `status_komisi` (string, nullable) - Status komisi (adjustment)
- `harga_komisi_standar` (decimal, nullable) - Harga komisi standar
- `harga_komisi_ekstra` (decimal, nullable) - Harga komisi ekstra

**Migration:** `2025_10_05_005112_add_customer_extended_fields_to_customers_table.php`

**Note:** Status customer akan berubah dari 'new' menjadi 'repeat' saat customer melakukan repeat order.

---

## 3. Tabel Orders (Laporan Outstanding)
Field baru yang ditambahkan:
- `tanggal_po` (date, nullable) - Tanggal Purchase Order
- `tanggal_kirim` (date, nullable) - Tanggal pengiriman
- `charge` (decimal, nullable) - Biaya tambahan
- `catatan` (text, nullable) - Catatan tambahan order

**Migration:** `2025_10_05_005149_add_outstanding_fields_to_orders_table.php`

---

## Cara Menggunakan

### Rollback Migration (jika diperlukan)
```bash
php artisan migrate:rollback --step=3
```

### Menjalankan Migration
```bash
php artisan migrate
```

### Melihat Status Migration
```bash
php artisan migrate:status
```

---

## Field yang Masih Perlu Ditambahkan (Future Development)

### Point Penjualan:
- Rekap penjualan sales
- Box (jumlah cetak dan point)
- Kertas nasi padang (jumlah cetak dan point)

### Laporan Customer Terbesar:
- Total lembar per customer
- Total penjualan per customer (bisa dihitung dari data orders yang ada)

### Order Items:
- Volume
- Jumlah cetak
- Jenis bahan (bisa diambil dari product)
- Gramasi (bisa diambil dari product)

---

## Enum yang Sudah Diupdate

1. **ProductFieldsEnum** - Ditambahkan enum untuk field master data
2. **CustomerFieldsEnum** - Ditambahkan enum untuk field customer baru
3. **OrderFieldsEnum** - Ditambahkan enum untuk field laporan outstanding

---

## Model yang Sudah Diupdate

1. **Product** - Siap menerima field master data baru
2. **Customer** - Ditambahkan casting untuk harga komisi
3. **Order** - Ditambahkan casting untuk charge dan tanggal
