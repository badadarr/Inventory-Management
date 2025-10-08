# 🔧 REFACTOR PLAN - INVENTORY V2

**Date:** October 7, 2025  
**Branch:** feat-rbac  
**Status:** In Progress

---

## 📋 MENU REFACTOR PRIORITY

### ✅ Phase 0: Already Complete
- [x] Dashboard (Updated - uses `total` instead of `profit`)
- [x] Purchase Orders (NEW - v2 feature)
- [x] Stock Movements (NEW - v2 feature)
- [x] Product Customer Prices (NEW - v2 feature via modal)

---

### 🔄 Phase 1: Core Master Data (HIGH PRIORITY)

#### 1. **Products** ✅ REFACTOR COMPLETE + ENHANCED
**Status:** ✅ **COMPLETE** (See: PRODUCT_MODULE_REFACTOR_COMPLETED.md, DYNAMIC_SIZES_IMPLEMENTATION_COMPLETE.md)

**Completed Changes:**
- ✅ Frontend updated: Shows "Bahan", "Gramatur", "Ukuran" instead of "Product Number"
- ✅ Added: reorder_level column with warning badge
- ✅ Added: "Set Custom Price" button (🏷️) per product
- ✅ Added: Low stock indicator and "Below Reorder" badge
- ✅ Forms updated: Removed v1 fields (description, root, buying_date)
- ✅ Forms updated: Added v2 fields (reorder_level, keterangan_tambahan)
- ✅ Validation rules aligned to v2 schema
- ✅ **NEW**: Dynamic Product Sizes feature implemented (October 2025)

**Files Updated (6 total):**
- [x] `resources/js/Pages/Product/Index.vue` - Table columns refactored
- [x] `resources/js/Pages/Product/Create.vue` - Form fields refactored + Dynamic Sizes
- [x] `resources/js/Pages/Product/Edit.vue` - Form fields refactored + Dynamic Sizes
- [x] `app/Services/ProductService.php` - Payload processing updated
- [x] `app/Http/Requests/Product/ProductCreateRequest.php` - Validation updated
- [x] `app/Http/Requests/Product/ProductUpdateRequest.php` - Validation updated

**Dynamic Product Sizes Feature (NEW):**
- [x] Database: `product_sizes` table created
- [x] Service: `ProductSizeService` implemented
- [x] Validation: `ProductSizeValidation` implemented
- [x] Frontend: `ProductSizes.vue` component (400+ lines)
- [x] Testing: 30+ test cases passed
- [x] Bug Fixes: 6 major bugs resolved
- [x] Documentation: 13 comprehensive docs created

**Completion Date:** 
- Initial Refactor: December 2024
- Dynamic Sizes: October 8, 2025

---

#### 2. **Customers** ⚠️ NEEDS REFACTOR
**Current Issues:**
- Missing commission fields (v2)
- Missing repeat customer tracking
- Need to show custom pricing info

**Schema Changes (v1 → v2):**
```
ADDED:
- sales_id (FK to sales table)
- nama_box
- nama_owner
- bulan_join, tahun_join
- status_customer (baru/repeat)
- repeat_order_count
- status_komisi
- harga_komisi_standar
- harga_komisi_extra
```

**Files to Update:**
- [ ] `resources/js/Pages/Customer/Index.vue` - Add commission columns
- [ ] `resources/js/Pages/Customer/Create.vue` - Add commission fields
- [ ] `resources/js/Pages/Customer/Edit.vue` - Add commission fields
- [ ] `app/Http/Controllers/CustomerController.php` - Update logic
- [ ] `app/Http/Requests/Customer/*` - Update validation

---

#### 3. **Suppliers** ✅ MINIMAL CHANGES
**Status:** Schema unchanged, working fine

---

#### 4. **Categories** ✅ NO CHANGES NEEDED
**Status:** Schema unchanged, working fine

---

#### 5. **Unit Types** ✅ NO CHANGES NEEDED
**Status:** Schema unchanged, working fine

---

### 🔄 Phase 2: Transaction Modules (MEDIUM PRIORITY)

#### 6. **Orders (Sales Orders)** ⚠️ MAJOR REFACTOR NEEDED
**Current Issues:**
- Missing sales_id field (v2)
- profit/loss columns removed
- Need to integrate with custom pricing
- Missing v2 specific fields

**Schema Changes (v1 → v2):**
```
REMOVED:
- profit, loss columns

ADDED:
- sales_id (FK to sales table)
- tanggal_po, tanggal_kirim
- jenis_bahan, gramasi
- volume, harga_jual_pcs
- jumlah_cetak
- catatan
- created_by (FK to users)
```

**Files to Update:**
- [ ] `resources/js/Pages/Order/Index.vue` - Update columns
- [ ] `resources/js/Pages/Order/Create.vue` - Major refactor needed
- [ ] `resources/js/Pages/Order/Edit.vue` - Major refactor needed
- [ ] `app/Http/Controllers/OrderController.php` - Remove profit/loss logic
- [ ] `app/Http/Requests/Order/*` - Update validation
- [ ] `app/Services/OrderService.php` - Update business logic

---

#### 7. **Expenses** ⚠️ MINOR CHANGES
**Current Issues:**
- Check if expense_type enum matches v2

**Files to Check:**
- [ ] Verify enum values match
- [ ] Check if any schema changes needed

---

#### 8. **Transactions** ⚠️ NEEDS REVIEW
**Current Issues:**
- Check compatibility with v2 orders

**Files to Check:**
- [ ] Review transaction recording logic
- [ ] Ensure compatibility with new order structure

---

### 🔄 Phase 3: HR & Payroll (LOW PRIORITY)

#### 9. **Employees** ⚠️ NEEDS REFACTOR
**Schema Changes (v1 → v2):**
```
REMOVED:
- salary field (moved to separate salaries table)

ADDED:
- jenis_kelamin
- agama
- status_pernikahan
- jabatan
- divisi
```

**Files to Update:**
- [ ] `resources/js/Pages/Employee/Index.vue` - Add new columns
- [ ] `resources/js/Pages/Employee/Create.vue` - Add new fields
- [ ] `resources/js/Pages/Employee/Edit.vue` - Add new fields
- [ ] `app/Http/Controllers/EmployeeController.php` - Update logic

---

#### 10. **Salaries** ⚠️ NEEDS REFACTOR
**New Table in v2**
- Separate salary tracking per period
- Link to employees table

**Files to Update:**
- [ ] `resources/js/Pages/Salary/Index.vue` - Update to v2 structure
- [ ] `resources/js/Pages/Salary/Create.vue` - Update form
- [ ] `app/Http/Controllers/SalaryController.php` - Update logic

---

#### 11. **Sales (Sales Person)** ⚠️ NEW MODULE
**Status:** New table in v2
- Need to create CRUD
- Link to orders and customers

**Files to Create:**
- [ ] `resources/js/Pages/Sales/Index.vue`
- [ ] `resources/js/Pages/Sales/Create.vue`
- [ ] `resources/js/Pages/Sales/Edit.vue`
- [ ] `app/Http/Controllers/SalesController.php`
- [ ] `app/Services/SalesService.php`
- [ ] `app/Repositories/SalesRepository.php`

---

#### 12. **Sales Points** ⚠️ NEW MODULE
**Status:** New table in v2
- Point of sale management

**Files to Create:**
- [ ] `resources/js/Pages/SalesPoint/Index.vue`
- [ ] `resources/js/Pages/SalesPoint/Create.vue`
- [ ] `app/Http/Controllers/SalesPointController.php`

---

### 🔄 Phase 4: User Management (MEDIUM PRIORITY)

#### 13. **Users** ⚠️ NEEDS REFACTOR
**Schema Changes (v1 → v2):**
```
ADDED:
- role (admin/sales/finance/warehouse)

REMOVED:
- Multiple separate permission fields
```

**Files to Update:**
- [ ] `resources/js/Pages/User/Index.vue` - Show role
- [ ] Add role selection in user creation
- [ ] Update permissions logic to use roles

---

## 📊 REFACTOR SUMMARY

### Statistics
- **Total Modules:** 13
- **No Changes:** 3 (Suppliers, Categories, UnitTypes)
- **Minor Changes:** 1 (Expenses)
- **Major Refactor:** 9 modules
- **New Modules:** 2 (Sales, SalesPoints)

### Estimated Effort
- Phase 1 (Core Master): 4-6 hours
- Phase 2 (Transactions): 6-8 hours
- Phase 3 (HR): 3-4 hours
- Phase 4 (Users): 2-3 hours

**Total:** ~15-21 hours

---

## 🎯 REFACTOR ORDER (By Priority)

1. **Products** - Core inventory management
2. **Customers** - Required for orders
3. **Orders** - Main transaction module
4. **Users** - Role-based access
5. **Employees** - HR management
6. **Salaries** - Payroll
7. **Sales** - New sales person module
8. **Expenses** - Financial tracking
9. **Transactions** - Payment records
10. **Sales Points** - POS management

---

## 📝 NEXT STEPS

### Immediate (Start Here)
1. ✅ Create this refactor plan
2. ⏳ Start with **Product/Index.vue** refactor
3. ⏳ Update **Product/Create.vue**
4. ⏳ Update **Product/Edit.vue**

### After Products Complete
5. Move to Customers module
6. Then Orders module
7. Continue with remaining modules

---

**Status Legend:**
- ✅ Complete
- ⏳ In Progress
- ⚠️ Needs Attention
- 🔄 Planned

