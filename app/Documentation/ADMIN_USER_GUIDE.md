# 📘 Panduan Penggunaan Sistem - Admin

## 🎯 Alur Kerja Sistem Inventory Management

---

## 1️⃣ SETUP AWAL (Dilakukan Sekali)

### A. Login ke Sistem
- [ ] Buka browser, akses URL sistem
- [ ] Login dengan kredensial admin
- [ ] Verifikasi dashboard muncul dengan benar

☒☑
### B. Setup Master Data Unit Types
- [☑] Klik menu **"Unit Types"**
- [☑] Klik tombol **"Create Unit Type"**
- [☑] Isi data:
  - Name: `Pcs`, `Box`, `Rim`, `Lembar`, dll
  - Symbol: `pcs`, `box`, `rim`, `lbr`
- [☑] Klik **"Submit"**
- [☑] Ulangi untuk semua unit yang dibutuhkan

### C. Setup Categories
- [☑] Klik menu **"Categories"**
- [☑] Klik tombol **"Create Category"**
- [☑] Isi data:
  - Name: `Box Makanan`, `Kertas Nasi`, `Packaging`, dll
  - Description: (opsional) ☒ Belum ada field yang disediakan
- [☒] Upload foto kategori (opsional) ☒ Belum ada field yang disediakan
- [☑] Klik **"Submit"**
- [☑] Ulangi untuk semua kategori

### D. Setup Suppliers (Opsional)
- [☑] Klik menu **"Suppliers"**
- [☑] Klik tombol **"Create Supplier"**
- [☒] Isi data supplier
  - Fields foto belum bisa upload gambar
- [☑] Klik **"Submit"**

---

## 2️⃣ MANAJEMEN PRODUK (Harian/Mingguan)

### A. Tambah Produk Baru
- [☑] Klik menu **"Products"** → **"Create Product"**
- [☒] Isi data wajib:
  - **Category**: Pilih kategori
  - **Supplier**: Pilih supplier
  - **Name**: Nama produk
  - **Product Code**: Kode produk
  - **Buying Price**: Harga beli
  - **Selling Price**: Harga jual
  - **Quantity**: Stok awal
  - **Unit Type**: Pilih satuan
  - **Status**: Active/Inactive
  - **Photo**: Upload foto produk
Error: Tidak terinput data wajib ini ke database

Error: sql sintaks untuk query search
SQLSTATE[22P02]: Invalid text representation: 7 ERROR: sintaks masukan tidak valid untuk tipe bigint : « Lunch Box » CONTEXT: unnamed portal parameter $1 = '...'


- [ ] Isi data tambahan (Master Data):
  - **Bahan**: Jenis bahan (contoh: Art Paper, Duplex)
  - **Gramatur**: Ketebalan (contoh: 260gsm, 310gsm)
  - **Ukuran**: Ukuran produk (contoh: 20x15cm)
  - **Ukuran Potongan 1**: (contoh: 40x30cm)
  - **Ukuran Plano 1**: (contoh: 65x100cm)
  - **Ukuran Potongan 2**: (opsional)
  - **Ukuran Plano 2**: (opsional)
  - **Alamat Pengiriman**: Alamat default pengiriman
  - **Description**: Keterangan tambahan

- [ ] Klik **"Submit"**
- [ ] Verifikasi produk muncul di list

### B. Edit Produk
- [ ] Klik menu **"Products"**
- [ ] Cari produk yang ingin diedit
- [ ] Klik tombol **Edit** (icon pensil)
- [ ] Update data yang diperlukan
- [ ] Klik **"Submit"**

### C. Update Stok Produk
- [ ] Buka halaman **"Products"**
- [ ] Edit produk
- [ ] Update field **"Quantity"**
- [ ] Klik **"Submit"**

---

## 3️⃣ MANAJEMEN CUSTOMER (Saat Ada Customer Baru)

### A. Tambah Customer Baru
- [ ] Klik menu **"Customers"** → **"Create Customer"**
- [ ] Isi data wajib:
  - **Name**: Nama customer
  - **Email**: Email customer
  - **Phone**: Nomor telepon
  - **Address**: Alamat lengkap

- [ ] Isi data tambahan:
  - **Nama Box**: Nama box customer
  - **Nama Sales**: Sales yang menangani
  - **Nama Owner**: Nama pemilik usaha customer
  - **Bulan Join**: Bulan bergabung (contoh: Januari)
  - **Tahun Join**: Tahun bergabung (contoh: 2025)
  - **Status Customer**: New (default) / Repeat
  - **Status Komisi**: Status adjustment komisi
  - **Harga Komisi Standar**: Nominal komisi standar
  - **Harga Komisi Ekstra**: Nominal komisi ekstra
  - **Photo**: Upload foto customer (opsional)

- [ ] Klik **"Submit"**
- [ ] Verifikasi customer muncul di list

### B. Update Status Customer (New → Repeat)
- [ ] Buka halaman **"Customers"**
- [ ] Klik tombol **Edit** pada customer
- [ ] Ubah **"Status Customer"** dari **"New"** ke **"Repeat"**
- [ ] Klik **"Submit"**

---

## 4️⃣ PROSES PENJUALAN (POS)

### A. Buat Order Baru via POS
- [ ] Klik menu **"POS"** atau **"Cart"**
- [ ] Pilih **Customer** dari dropdown
- [ ] Tambah produk ke cart:
  - Cari produk
  - Klik tombol **"Add to Cart"**
  - Atur quantity
- [ ] Ulangi untuk semua produk yang dibeli
- [ ] Review cart:
  - Sub Total
  - Tax (jika ada)
  - Discount (jika ada)
  - Total

### B. Checkout & Payment
- [ ] Klik tombol **"Checkout"**
- [ ] Isi informasi pembayaran:
  - **Paid Amount**: Jumlah yang dibayar
  - **Payment Method**: Cash/Transfer/dll
- [ ] Klik **"Complete Order"**
- [ ] Order akan masuk ke menu **"Orders"**

---

## 5️⃣ MANAJEMEN ORDERS

### A. Lihat Semua Orders
- [ ] Klik menu **"Orders"**
- [ ] Lihat list semua order dengan info:
  - Order Number
  - Customer
  - Total, Paid, Due
  - Profit/Loss
  - Status (Paid/Partial Paid/Unpaid)
  - Date

### B. Lihat Detail Order Items
- [ ] Klik tombol **"List"** (icon list) pada order
- [ ] Lihat detail produk yang dibeli:
  - Product Name
  - Product Code
  - Price (Buying & Selling)
  - Quantity

### C. Bayar Cicilan (Due Payment)
- [ ] Cari order dengan status **"Partial Paid"** atau **"Unpaid"**
- [ ] Klik tombol **"Pay Due"** (icon uang)
- [ ] Pilih **Payment Method**
- [ ] Masukkan **Amount** yang dibayar
- [ ] Klik **"Submit"**
- [ ] Status order akan update otomatis

### D. Settle Order (Hapus Hutang)
- [ ] Cari order dengan due amount
- [ ] Klik tombol **"Settle"** (icon handshake)
- [ ] Konfirmasi settlement
- [ ] Due amount akan dijadikan discount
- [ ] Status berubah menjadi **"Settled"**

---

## 6️⃣ MANAJEMEN EMPLOYEE & SALARY

### A. Tambah Employee
- [ ] Klik menu **"Employees"** → **"Create Employee"**
- [ ] Isi data employee:
  - Name, Email, Phone
  - Address
  - Salary
  - Photo
- [ ] Klik **"Submit"**

### B. Bayar Gaji Employee
- [ ] Klik menu **"Salaries"**
- [ ] Klik **"Create Salary"**
- [ ] Pilih **Employee**
- [ ] Isi **Amount** dan **Date**
- [ ] Klik **"Submit"**

---

## 7️⃣ MANAJEMEN EXPENSES (Pengeluaran)

### A. Catat Pengeluaran
- [ ] Klik menu **"Expenses"** → **"Create Expense"**
- [ ] Isi data:
  - **Name**: Nama pengeluaran
  - **Amount**: Nominal
  - **Date**: Tanggal
  - **Description**: Keterangan
- [ ] Klik **"Submit"**

---

## 8️⃣ MONITORING & LAPORAN

### A. Dashboard
- [ ] Klik menu **"Dashboard"**
- [ ] Monitor:
  - Total Sales
  - Total Orders
  - Total Customers
  - Total Products
  - Recent Orders
  - Top Selling Products

### B. Transactions
- [ ] Klik menu **"Transactions"**
- [ ] Lihat semua transaksi:
  - Order transactions
  - Payment history
  - Filter by date

### C. Filter & Search
- [ ] Gunakan search box untuk cari data
- [ ] Gunakan filter untuk menyaring data
- [ ] Export data (jika tersedia)

---

## 9️⃣ SETTINGS

### A. Update Profile
- [ ] Klik icon profile → **"Profile"**
- [ ] Update informasi personal
- [ ] Klik **"Save"**

### B. Change Password
- [ ] Klik icon profile → **"Profile"**
- [ ] Tab **"Update Password"**
- [ ] Isi current & new password
- [ ] Klik **"Save"**

### C. System Settings
- [ ] Klik menu **"Settings"**
- [ ] Update:
  - Company Name
  - Currency
  - Tax Rate
  - Logo
- [ ] Klik **"Save"**

---

## 🔄 ALUR KERJA HARIAN ADMIN

### Pagi (Buka Toko)
1. [ ] Login ke sistem
2. [ ] Cek dashboard untuk overview
3. [ ] Cek stok produk yang menipis
4. [ ] Review pending orders

### Siang (Operasional)
1. [ ] Proses order baru via POS
2. [ ] Input customer baru jika ada
3. [ ] Update stok jika ada pembelian bahan
4. [ ] Terima pembayaran cicilan

### Sore (Closing)
1. [ ] Review semua transaksi hari ini
2. [ ] Catat pengeluaran harian
3. [ ] Update status order
4. [ ] Backup data (jika perlu)

### Mingguan
1. [ ] Bayar gaji employee
2. [ ] Review laporan penjualan
3. [ ] Update harga produk (jika perlu)
4. [ ] Follow up customer dengan due payment

---

## ⚠️ TIPS & BEST PRACTICES

### DO ✅
- ✅ Selalu isi data lengkap saat input
- ✅ Upload foto produk untuk memudahkan identifikasi
- ✅ Update status customer dari New ke Repeat
- ✅ Catat semua pengeluaran
- ✅ Backup data secara berkala
- ✅ Logout setelah selesai

### DON'T ❌
- ❌ Jangan hapus data tanpa backup
- ❌ Jangan share password admin
- ❌ Jangan lupa update stok setelah penjualan
- ❌ Jangan skip field penting (bahan, gramatur, ukuran)
- ❌ Jangan biarkan order unpaid terlalu lama

---

## 🆘 TROUBLESHOOTING

### Masalah: Tidak bisa login
- Cek username/password
- Clear browser cache
- Hubungi IT support

### Masalah: Stok tidak update
- Refresh halaman
- Cek apakah order sudah complete
- Cek log error

### Masalah: Upload foto gagal
- Cek ukuran file (max 1MB)
- Cek format file (jpg, png, gif, svg)
- Cek koneksi internet

### Masalah: Data tidak tersimpan
- Cek semua field wajib sudah diisi
- Lihat pesan error di form
- Refresh dan coba lagi

---

## 📞 KONTAK SUPPORT

- **Technical Support**: [email/phone]
- **Admin Support**: [email/phone]
- **Emergency**: [phone]

---

**Catatan:** Panduan ini akan diupdate seiring dengan penambahan fitur baru.
