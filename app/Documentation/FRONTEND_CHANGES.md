# Perubahan Frontend - Field Baru

## 1. Product Pages

### Create Product (`resources/js/Pages/Product/Create.vue`)
Field baru yang ditambahkan:
- Bahan
- Gramatur
- Ukuran
- Ukuran Potongan 1
- Ukuran Plano 1
- Ukuran Potongan 2
- Ukuran Plano 2
- Alamat Pengiriman

### Edit Product (`resources/js/Pages/Product/Edit.vue`)
Field baru yang ditambahkan (sama dengan Create):
- Bahan
- Gramatur
- Ukuran
- Ukuran Potongan 1
- Ukuran Plano 1
- Ukuran Potongan 2
- Ukuran Plano 2
- Alamat Pengiriman

---

## 2. Customer Page

### Customer Index (`resources/js/Pages/Customer/Index.vue`)
Field baru di Create Modal:
- Nama Box
- Nama Sales
- Nama Owner
- Bulan Join
- Tahun Join
- Status Customer (dropdown: New/Repeat)
- Status Komisi
- Harga Komisi Standar
- Harga Komisi Ekstra

Field baru di Edit Modal (sama dengan Create):
- Nama Box
- Nama Sales
- Nama Owner
- Bulan Join
- Tahun Join
- Status Customer (dropdown: New/Repeat)
- Status Komisi
- Harga Komisi Standar
- Harga Komisi Ekstra

---

## 3. Backend Validation

### Product Requests
- `app/Http/Requests/Product/ProductCreateRequest.php` - Ditambahkan validasi untuk 8 field baru
- `app/Http/Requests/Product/ProductUpdateRequest.php` - Ditambahkan validasi untuk 8 field baru

### Customer Requests
- `app/Http/Requests/Customer/CustomerCreateRequest.php` - Ditambahkan validasi untuk 9 field baru
- `app/Http/Requests/Customer/CustomerUpdateRequest.php` - Ditambahkan validasi untuk 9 field baru

---

## Cara Testing

### Test Product:
1. Buka halaman Products > Create
2. Isi semua field termasuk field baru (Bahan, Gramatur, Ukuran, dll)
3. Submit form
4. Edit product yang baru dibuat
5. Pastikan semua field baru tersimpan dan bisa diedit

### Test Customer:
1. Buka halaman Customers
2. Klik "Create Customer"
3. Isi semua field termasuk field baru (Nama Box, Nama Sales, dll)
4. Submit form
5. Edit customer yang baru dibuat
6. Pastikan semua field baru tersimpan dan bisa diedit

---

## Notes

- Semua field baru bersifat **nullable** (opsional)
- Field `status_customer` default value adalah **'new'**
- Field harga komisi menggunakan input type **number**
- Validasi backend sudah ditambahkan untuk semua field baru
- Frontend form sudah terintegrasi dengan backend validation
