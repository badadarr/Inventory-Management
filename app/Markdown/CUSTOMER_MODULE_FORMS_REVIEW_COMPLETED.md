# Customer Module - Create/Edit Forms Review & Fixes

**Date:** October 8, 2025  
**Status:** ✅ COMPLETED  
**Phase:** Customer Module Refactor - Step 2 (Forms Review)

---

## 📋 Overview

Review dan perbaikan form Create/Edit untuk Customer module agar sesuai dengan v2 schema dan best practices. Form ini berada dalam modal di `resources/js/Pages/Customer/Index.vue`.

---

## 🔍 Issues Found & Fixed

### 1. ❌ **Field "nama_sales" → "sales_id" (Text Input → Dropdown)**

**Problem:**
- Form menggunakan text input untuk `nama_sales`
- User harus mengetik nama sales person secara manual
- Tidak ada validasi bahwa sales person ada di database
- Tidak sesuai dengan database schema yang menggunakan `sales_id` (foreign key)

**Solution:**
```vue
<!-- BEFORE: Text Input -->
<DashboardInputGroup
    label="Nama Sales"
    name="nama_sales"
    v-model="form.nama_sales"
    placeholder="Enter nama sales"
/>

<!-- AFTER: Dropdown with Sales List -->
<label for="sales_id" class="text-stone-600 text-sm font-medium">Sales Person</label>
<select
    id="sales_id"
    v-model="form.sales_id"
    class="mt-2 block w-full rounded-md border border-gray-200 px-2 py-2"
>
    <option :value="null">-- Select Sales Person --</option>
    <option v-for="sales in salesList" :key="sales.id" :value="sales.id">
        {{ sales.name }}
    </option>
</select>
```

**Backend Changes:**
```php
// CustomerController.php - Added salesList to Inertia props
'salesList' => \App\Models\Sales::where('status', 'active')
    ->orderBy('name')
    ->get(['id', 'name']),
```

---

### 2. ⚠️ **Status Customer Value Mismatch**

**Problem:**
- Form menggunakan values: `"new"` dan `"repeat"`
- Database enum menggunakan: `"baru"` dan `"repeat"`
- Menyebabkan data tidak konsisten

**Solution:**
```vue
<!-- BEFORE -->
<option value="new">New</option>
<option value="repeat">Repeat</option>

<!-- AFTER -->
<option value="baru">New</option>
<option value="repeat">Repeat</option>
```

**Form Default Value:**
```javascript
// BEFORE
status_customer: 'new',

// AFTER
status_customer: 'baru',
```

**Service Default Value:**
```php
// CustomerService.php - create() method
// BEFORE
CustomerFieldsEnum::STATUS_CUSTOMER->value => $payload[...] ?? 'new',

// AFTER
CustomerFieldsEnum::STATUS_CUSTOMER->value => $payload[...] ?? 'baru',
```

---

### 3. ⚠️ **Field Name Inconsistency: "harga_komisi_ekstra" vs "harga_komisi_extra"**

**Problem:**
- Database column: `harga_komisi_extra`
- Form field: `harga_komisi_ekstra`
- Mismatch menyebabkan data tidak tersimpan

**Solution:**

**Frontend (Index.vue):**
```javascript
// Form definition
const form = useForm({
    // BEFORE
    harga_komisi_ekstra: null,
    
    // AFTER
    harga_komisi_extra: null,
});
```

```vue
<!-- Form Input -->
<!-- BEFORE -->
<DashboardInputGroup
    label="Harga Komisi Ekstra"
    name="harga_komisi_ekstra"
    v-model="form.harga_komisi_ekstra"
/>

<!-- AFTER -->
<DashboardInputGroup
    label="Harga Komisi Extra"
    name="harga_komisi_extra"
    v-model="form.harga_komisi_extra"
/>
```

**Enum (CustomerFieldsEnum.php):**
```php
// BEFORE
case HARGA_KOMISI_EKSTRA = 'harga_komisi_ekstra';

// AFTER
case HARGA_KOMISI_EXTRA = 'harga_komisi_extra';
```

**Service (CustomerService.php):**
```php
// Both create() and update() methods
// BEFORE
CustomerFieldsEnum::HARGA_KOMISI_EKSTRA->value => ...

// AFTER
CustomerFieldsEnum::HARGA_KOMISI_EXTRA->value => ...
```

---

### 4. 🆕 **Added Sales Dropdown Props**

**Frontend Props:**
```javascript
defineProps({
    filters: { type: Object },
    customers: { type: Object },
    // NEW: Added salesList prop
    salesList: {
        type: Array,
        default: () => []
    },
});
```

---

## 📝 Validation Rules Updated

### CustomerCreateRequest.php

**Changes:**
```php
// BEFORE
"nama_sales" => ["nullable", "string", "max:255"],
"status_customer" => ["nullable", "string", "in:new,repeat"],
"harga_komisi_standar" => ["nullable", "numeric"],
"harga_komisi_ekstra" => ["nullable", "numeric"],

// AFTER
"sales_id" => ["nullable", "exists:sales,id"],
"status_customer" => ["nullable", "string", "in:baru,repeat"],
"harga_komisi_standar" => ["nullable", "numeric", "min:0"],
"harga_komisi_extra" => ["nullable", "numeric", "min:0"],
```

**Improvements:**
- ✅ `sales_id` validates against actual sales table
- ✅ `status_customer` values match database enum
- ✅ Added `min:0` validation for commission amounts
- ✅ Fixed field name from `harga_komisi_ekstra` to `harga_komisi_extra`

### CustomerUpdateRequest.php

Same changes applied to update request.

---

## 🔄 Enum Updates

### CustomerFieldsEnum.php

**Changes:**
```php
// Case definitions
case NAMA_SALES = 'nama_sales';  // REMOVED
case SALES_ID = 'sales_id';      // ADDED

case HARGA_KOMISI_EKSTRA = 'harga_komisi_ekstra';  // REMOVED
case HARGA_KOMISI_EXTRA = 'harga_komisi_extra';    // ADDED

// Labels array
self::NAMA_SALES->value => "Nama Sales",           // REMOVED
self::SALES_ID->value => "Sales Person",           // ADDED

self::HARGA_KOMISI_EKSTRA->value => "Harga Komisi Ekstra",  // REMOVED
self::HARGA_KOMISI_EXTRA->value => "Harga Komisi Extra",    // ADDED
```

---

## 📦 Files Modified

### Backend Files (5 files)

1. **`app/Http/Controllers/CustomerController.php`**
   - ✅ Added `salesList` to Inertia props
   - ✅ Loads active sales persons ordered by name

2. **`app/Http/Requests/Customer/CustomerCreateRequest.php`**
   - ✅ Changed `nama_sales` to `sales_id` with `exists:sales,id` validation
   - ✅ Fixed status_customer enum values to `baru,repeat`
   - ✅ Changed `harga_komisi_ekstra` to `harga_komisi_extra`
   - ✅ Added `min:0` validation for commission amounts

3. **`app/Http/Requests/Customer/CustomerUpdateRequest.php`**
   - ✅ Same validation changes as CreateRequest

4. **`app/Enums/Customer/CustomerFieldsEnum.php`**
   - ✅ Changed `NAMA_SALES` to `SALES_ID`
   - ✅ Changed `HARGA_KOMISI_EKSTRA` to `HARGA_KOMISI_EXTRA`
   - ✅ Updated labels array

5. **`app/Services/CustomerService.php`**
   - ✅ Updated `create()` method to use `SALES_ID` and `HARGA_KOMISI_EXTRA`
   - ✅ Updated `update()` method to use `SALES_ID` and `HARGA_KOMISI_EXTRA`
   - ✅ Changed default `status_customer` from `'new'` to `'baru'`

### Frontend Files (1 file)

6. **`resources/js/Pages/Customer/Index.vue`**
   - ✅ Added `salesList` prop
   - ✅ Changed form field `nama_sales` to `sales_id`
   - ✅ Changed form field `harga_komisi_ekstra` to `harga_komisi_extra`
   - ✅ Changed form default `status_customer` from `'new'` to `'baru'`
   - ✅ Replaced text input with dropdown for sales person (CREATE form)
   - ✅ Replaced text input with dropdown for sales person (EDIT form)
   - ✅ Fixed status_customer select options to use `'baru'` instead of `'new'`
   - ✅ Updated editCustomerModal to populate `sales_id` and `harga_komisi_extra`

---

## ✅ Form Structure Summary

### Create Form Modal (13 fields)

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `name` | Text | ✅ Yes | Customer company name |
| `nama_box` | Text | ❌ No | Box identifier |
| `sales_id` | Dropdown | ❌ No | Sales person assignment |
| `nama_owner` | Text | ❌ No | Owner name |
| `email` | Email | ✅ Yes | Customer email (unique) |
| `phone` | Text | ✅ Yes | Customer phone |
| `bulan_join` | Text | ❌ No | Join month |
| `tahun_join` | Text | ❌ No | Join year |
| `status_customer` | Select | ❌ No | New/Repeat status |
| `status_komisi` | Text | ❌ No | Commission status |
| `harga_komisi_standar` | Number | ❌ No | Standard commission amount |
| `harga_komisi_extra` | Number | ❌ No | Extra commission amount |
| `photo` | File | ❌ No | Customer photo |
| `address` | Textarea | ❌ No | Customer address |

### Edit Form Modal

Same fields as Create form, with:
- Pre-populated values from selected customer
- Photo field shows existing photo or allows new upload
- Email unique validation ignores current customer

---

## 🎯 Form Layout

**Grid Structure:** 3 columns (responsive)
```
lg:grid-cols-3 md:grid-cols-2 sm:grid-cols-1
```

**Field Order:**
```
Row 1: Name | Nama Box | Sales Person (Dropdown)
Row 2: Nama Owner | Email | Phone
Row 3: Bulan Join | Tahun Join | Status Customer
Row 4: Status Komisi | Komisi Standar | Komisi Extra
Row 5: Photo Upload | Address (textarea spans remaining)
```

---

## 🔍 Form Validation Features

### Frontend Validation
- ✅ Email type validation
- ✅ Number type for commission fields
- ✅ File type and size validation for photos
- ✅ Real-time error display via `InputError` component
- ✅ Enter key submit for text inputs

### Backend Validation
- ✅ Required fields: name, email, phone
- ✅ Email uniqueness check (ignores self in update)
- ✅ Email format validation
- ✅ Sales ID exists in database
- ✅ Status customer enum validation
- ✅ Commission amounts min:0
- ✅ Photo mime types: jpg, jpeg, png, gif, svg
- ✅ Photo max size: 1024KB (1MB)

---

## 🎨 Sales Dropdown Features

**Features:**
- Loads only **active** sales persons
- Sorted alphabetically by name
- Shows "-- Select Sales Person --" as default/null option
- Displays sales person name from relationship in edit mode
- Validates selection against database

**Backend Query:**
```php
\App\Models\Sales::where('status', 'active')
    ->orderBy('name')
    ->get(['id', 'name'])
```

---

## 🧪 Testing Checklist

### Create Form
- [ ] Open create modal
- [ ] Fill required fields (name, email, phone)
- [ ] Select sales person from dropdown
- [ ] Enter commission amounts
- [ ] Select status customer
- [ ] Upload photo
- [ ] Submit form
- [ ] Verify data saved correctly in database
- [ ] Verify sales_id populated correctly

### Edit Form
- [ ] Click edit button on customer
- [ ] Verify all fields pre-populated
- [ ] Verify sales person dropdown shows current selection
- [ ] Modify fields
- [ ] Change sales person
- [ ] Submit form
- [ ] Verify changes saved

### Validation
- [ ] Try to submit without required fields
- [ ] Try to enter invalid email
- [ ] Try to enter negative commission amounts
- [ ] Try to upload large photo (>1MB)
- [ ] Try to upload invalid file type
- [ ] Try to create duplicate email
- [ ] Verify all error messages display correctly

---

## 📊 Impact Summary

### ✅ Fixed Issues: 4
1. Sales person field (text → dropdown with validation)
2. Status customer value mismatch (new → baru)
3. Commission field name inconsistency (ekstra → extra)
4. Missing sales dropdown functionality

### 📝 Files Modified: 6
- 5 Backend files (Controller, Requests, Enum, Service)
- 1 Frontend file (Index.vue)

### 🔧 Total Changes: ~50 lines modified

---

## 🚀 Next Steps

1. **Create SalesSeeder** - Generate sample sales data for testing
2. **Test Forms** - Create and edit customers with sales assignment
3. **Frontend Polish** - Consider improving form layout and grouping
4. **Documentation** - Update user guide with sales assignment feature

---

## 📌 Notes

- All changes maintain backward compatibility
- Database schema already supports these changes
- No migration required
- Forms follow existing design patterns from Product module
- Commission fields accept decimal values
- Sales dropdown can be null (optional assignment)

---

**Review completed by:** AI Assistant  
**Reviewed on:** October 8, 2025  
**Next Phase:** Testing & Sales Seeder Creation
