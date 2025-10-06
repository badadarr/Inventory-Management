# 🌙 Cara Menggunakan Dark Theme Sidebar (Camos Style)

## 📋 Overview

Saya telah membuat komponen dark theme sidebar yang mirip dengan sistem Camos. Berikut cara menggunakannya:

---

## 🎨 Fitur Dark Theme

### ✅ Yang Sudah Dibuat:
1. **SidebarDark.vue** - Main sidebar dengan dark theme
2. **SidebarItemDark.vue** - Menu item untuk dark theme
3. **SidebarCollapsibleItemDark.vue** - Collapsible menu untuk dark theme
4. **SidebarSubItemDark.vue** - Sub-menu item untuk dark theme

### 🎯 Fitur Utama:
- ✅ Dark background (gray-900)
- ✅ White text dengan hover effects
- ✅ Company branding di atas
- ✅ User info di bawah
- ✅ Collapsible menu groups
- ✅ Active state highlighting (emerald color)
- ✅ "Coming Soon" labels untuk fitur yang belum ada
- ✅ Smooth transitions & animations
- ✅ Responsive (mobile & desktop)

---

## 🚀 Cara Implementasi

### Step 1: Update AuthenticatedLayout.vue

Ganti import Sidebar dengan SidebarDark:

```vue
<!-- File: resources/js/Layouts/AuthenticatedLayout.vue -->

<script setup>
// GANTI INI:
// import Sidebar from "@/Components/Sidebar/Sidebar.vue";

// DENGAN INI:
import Sidebar from "@/Components/Sidebar/SidebarDark.vue";

import AdminNavbar from "@/Components/Navbars/AdminNavbar.vue";
import FooterAdmin from "@/Components/Footers/FooterAdmin.vue";
import {Notification, Notivue, pastelTheme} from "notivue";
</script>

<template>
    <Notivue v-slot="item">
        <Notification
            :item="item"
            :theme="pastelTheme"
        />
    </Notivue>

    <div>
        <Sidebar/>

        <div class="relative md:ml-64 bg-blueGray-100">
            <AdminNavbar>
                <template #breadcrumb>
                    <slot name="breadcrumb"/>
                </template>
            </AdminNavbar>

            <!-- Header -->
            <div class="relative bg-emerald-600 md:pt-32 pb-32 pt-12">
                <div class="px-4 md:px-10 mx-auto w-full">
                    <div>
                        <!-- Card stats -->
                        <slot name="headerState" />
                    </div>
                </div>
            </div>

            <div class="px-4 md:px-10 mx-auto w-full -m-24">
                <slot/>
                <FooterAdmin/>
            </div>
        </div>
    </div>
</template>
```

### Step 2: Update Company Name

Edit file `SidebarDark.vue` dan ganti `[Company Name]` dengan nama perusahaan Anda:

```vue
<!-- File: resources/js/Components/Sidebar/SidebarDark.vue -->
<!-- Line ~20 -->

<div class="hidden md:block w-full px-4 py-6 border-b border-gray-700">
    <h1 class="text-white text-lg font-bold">PT. Nama Perusahaan Anda</h1>
    <p class="text-gray-400 text-xs mt-1">Inventory Management System</p>
</div>
```

### Step 3: Test

1. Refresh browser
2. Sidebar sekarang akan tampil dengan dark theme
3. Cek semua menu berfungsi dengan baik
4. Cek responsive di mobile

---

## 🎨 Struktur Menu Baru (Seperti Camos)

```
📊 Dashboard
📁 Master Data
   ├─ Categories
   ├─ Unit Types
   ├─ Products
   ├─ Suppliers
   └─ Customers

🛒 Purchasing (Coming Soon)
   ├─ Purchase Orders (Soon)
   └─ Purchase Requests (Soon)

📦 Inventory
   ├─ POS
   ├─ Orders
   └─ Stock Movement (Soon)

📊 Reports
   ├─ Transactions
   ├─ Sales Report (Soon)
   └─ Stock Report (Soon)

💰 Finance
   ├─ Salary
   └─ Expenses

⚙️ Others
   ├─ Employees
   └─ Settings

⚖️ Adjustment Stock (Coming Soon)
🔄 Mutasi (Coming Soon)
```

---

## 🎨 Customization

### Mengubah Warna Accent

Jika ingin mengubah warna accent dari emerald ke warna lain:

```vue
<!-- Cari semua "emerald" dan ganti dengan warna lain -->
<!-- Contoh: emerald-500 → blue-500 -->

<!-- Active state -->
text-emerald-500 → text-blue-500
bg-emerald-600 → bg-blue-600

<!-- Hover state -->
hover:text-emerald-600 → hover:text-blue-600
```

### Mengubah Background Color

```vue
<!-- Main sidebar background -->
bg-gray-900 → bg-gray-800 (lebih terang)
bg-gray-900 → bg-black (lebih gelap)

<!-- Submenu background -->
bg-gray-800 → bg-gray-700
```

### Menambah Menu Baru

```vue
<!-- Tambahkan di dalam <ul> di SidebarDark.vue -->

<SidebarItemDark
    name="Menu Baru"
    routeName="menu.baru"
    icon="fas fa-star"
/>

<!-- Atau dengan submenu -->
<SidebarCollapsibleItemDark
    title="Menu Group Baru"
    icon="fas fa-folder"
    :routes="['submenu1.index', 'submenu2.index']"
>
    <SidebarSubItemDark name="Submenu 1" routeName="submenu1.index" />
    <SidebarSubItemDark name="Submenu 2" routeName="submenu2.index" />
</SidebarCollapsibleItemDark>
```

---

## 🔄 Switching Between Themes

Jika ingin bisa switch antara light dan dark theme:

### Option 1: Manual Switch (Recommended untuk sekarang)

Edit `AuthenticatedLayout.vue`:

```vue
<script setup>
// Light theme
import Sidebar from "@/Components/Sidebar/Sidebar.vue";

// Dark theme (Camos style)
// import Sidebar from "@/Components/Sidebar/SidebarDark.vue";
</script>
```

### Option 2: Dynamic Theme (Future Enhancement)

Bisa dibuat toggle button untuk switch theme secara dynamic menggunakan localStorage atau Vuex/Pinia.

---

## 📱 Mobile Responsiveness

Dark theme sudah fully responsive:

- ✅ Hamburger menu di mobile
- ✅ Slide-in sidebar
- ✅ Touch-friendly buttons
- ✅ Proper spacing untuk mobile
- ✅ Search bar di mobile

---

## 🐛 Troubleshooting

### Sidebar tidak muncul?
```bash
# Clear cache
npm run build
php artisan optimize:clear
```

### Icons tidak muncul?
Pastikan FontAwesome sudah ter-load di `app.blade.php`:
```html
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
```

### Warna tidak sesuai?
```bash
# Rebuild Tailwind CSS
npm run build
```

### Menu tidak collapsible?
Pastikan semua komponen dark theme sudah di-import dengan benar di `SidebarDark.vue`.

---

## ✅ Checklist Implementasi

- [ ] Copy semua file dark theme components
- [ ] Update `AuthenticatedLayout.vue` untuk import `SidebarDark`
- [ ] Ganti `[Company Name]` dengan nama perusahaan
- [ ] Test di browser (desktop & mobile)
- [ ] Cek semua menu berfungsi
- [ ] Cek active states
- [ ] Cek collapsible menus
- [ ] Cek user info di bawah sidebar
- [ ] Test responsive di mobile
- [ ] Clear cache & rebuild assets

---

## 🎯 Next Steps

Setelah dark theme aktif, Anda bisa:

1. ✅ Implement modul Purchasing
2. ✅ Implement modul Reports
3. ✅ Implement Stock Adjustment
4. ✅ Implement Stock Mutation
5. ✅ Add multi-warehouse support
6. ✅ Add barcode integration

---

## 📞 Support

Jika ada masalah atau pertanyaan, silakan:
1. Cek dokumentasi ini lagi
2. Cek console browser untuk error
3. Cek Laravel logs: `storage/logs/laravel.log`
4. Rebuild assets: `npm run build`

---

## 🎉 Selamat!

Sistem Anda sekarang memiliki tampilan yang mirip dengan sistem Camos! 🚀

**Before:** White sidebar dengan menu sederhana
**After:** Dark sidebar dengan struktur modular seperti Camos

**Estimasi waktu implementasi:** 5-10 menit
**Difficulty:** Easy ⭐
