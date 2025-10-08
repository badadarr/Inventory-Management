# Next Module: Orders (Sales Orders) - Planning Document

**Date:** October 8, 2025  
**Status:** 📋 PLANNING PHASE  
**Priority:** 🔴 HIGH (Major Refactor Required)  
**Branch:** feat-rbac

---

## 📊 Module Overview

**Current Status:** ⚠️ Still using v1 structure  
**Target:** Migrate to v2 schema and integrate with new features  
**Complexity:** 🔴 HIGH (Transaction module with multiple dependencies)  
**Estimated Duration:** 3-4 days

---

## 🎯 Objectives

### Primary Goals

1. ✅ **Update Database Schema** - Add v2 fields, remove v1 fields
2. ✅ **Integrate Sales Person** - Link orders to sales
3. ✅ **Remove Profit/Loss Calculation** - No longer calculated at order level
4. ✅ **Integrate Custom Pricing** - Use ProductCustomerPrice if exists
5. ✅ **Add Order Details** - tanggal_po, tanggal_kirim, jenis_bahan, etc.
6. ✅ **Update Business Logic** - Service layer refactor
7. ✅ **Redesign Frontend** - Modern UI with sections
8. ✅ **Add Audit Trail** - created_by tracking

---

## 📋 Schema Analysis

### V1 → V2 Changes

#### **REMOVED Fields** ❌
```sql
- profit (decimal)         -- Removed from schema
- loss (decimal)           -- Removed from schema
```

#### **ADDED Fields** ✅
```sql
- sales_id (FK)            -- Link to sales person
- tanggal_po (date)        -- Purchase Order date
- tanggal_kirim (date)     -- Delivery date
- jenis_bahan (string)     -- Material type
- gramasi (string)         -- Weight/thickness
- volume (integer)         -- Volume quantity
- harga_jual_pcs (decimal) -- Price per piece
- jumlah_cetak (integer)   -- Print quantity
- catatan (text)           -- Notes/remarks
- created_by (FK)          -- User who created
```

#### **UNCHANGED Fields** ✓
```sql
- id
- customer_id (FK)
- order_date (date)
- total (decimal)
- status (enum: pending, processing, completed, cancelled)
- created_at, updated_at
```

---

## 🔄 Dependencies

### Related Modules
1. **Customers** ✅ (Already refactored - commission ready)
2. **Products** ✅ (Already refactored - custom pricing ready)
3. **ProductCustomerPrices** ✅ (Already implemented)
4. **Sales** ✅ (Already exists in v2)
5. **OrderItems** ⚠️ (Need to review)
6. **Transactions** ⚠️ (Will be affected)

### External Services
- **ProductCustomerPriceService** ✅ Available
- **CustomerService** ✅ Available
- **SalesService** ✅ Available
- **ProductService** ✅ Available

---

## 📦 Current Files

### Backend Files
```
app/
├── Http/
│   ├── Controllers/
│   │   └── OrderController.php          ⚠️ MAJOR REFACTOR
│   └── Requests/
│       └── Order/
│           ├── OrderIndexRequest.php    ⚠️ CHECK
│           ├── OrderCreateRequest.php   🔴 MAJOR UPDATE
│           └── OrderUpdateRequest.php   🔴 MAJOR UPDATE
├── Services/
│   └── OrderService.php                 🔴 MAJOR REFACTOR
├── Repositories/
│   └── OrderRepository.php              ⚠️ CHECK
├── Models/
│   ├── Order.php                        🔴 UPDATE
│   └── OrderItem.php                    ⚠️ CHECK
├── Enums/
│   └── Order/
│       ├── OrderFieldsEnum.php          🔴 UPDATE
│       ├── OrderFiltersEnum.php         ⚠️ CHECK
│       └── OrderStatusEnum.php          ✅ OK
└── Exceptions/
    └── OrderNotFoundException.php       ✅ OK
```

### Frontend Files
```
resources/js/Pages/Order/
├── Index.vue                            🔴 MAJOR REFACTOR
├── Create.vue                           🔴 MAJOR REFACTOR
└── Edit.vue                             🔴 MAJOR REFACTOR
```

---

## 🎯 Refactor Plan (Step-by-Step)

### **Phase 1: Database & Schema Review** (Day 1 - Morning)

**Tasks:**
1. ✅ Review current orders table structure
2. ✅ Create migration to add v2 fields
3. ✅ Create migration to remove profit/loss columns
4. ✅ Update Order model (casts, fillable, relationships)
5. ✅ Update OrderItem model if needed
6. ✅ Review OrderFieldsEnum and update
7. ✅ Run migrations

**Deliverables:**
- Migration files (2 files)
- Updated Order.php model
- Updated OrderFieldsEnum.php

---

### **Phase 2: Backend Refactor** (Day 1 - Afternoon)

**Tasks:**
1. ✅ Update OrderCreateRequest validation
2. ✅ Update OrderUpdateRequest validation
3. ✅ Update OrderService:
   - Remove profit/loss calculation
   - Add sales_id handling
   - Integrate ProductCustomerPrice lookup
   - Add created_by tracking
   - Handle new v2 fields
4. ✅ Update OrderController:
   - Load sales list for dropdown
   - Remove profit/loss from responses
   - Add proper error handling
5. ✅ Update OrderRepository if needed

**Deliverables:**
- Updated validation files (2 files)
- Updated OrderService.php
- Updated OrderController.php

---

### **Phase 3: Frontend Redesign** (Day 2)

**Tasks:**

**Index.vue:**
1. ✅ Update table columns:
   - Remove: profit, loss
   - Add: sales_id, tanggal_po, tanggal_kirim
2. ✅ Add filters:
   - Filter by sales person
   - Filter by tanggal_po range
   - Filter by tanggal_kirim range
3. ✅ Update status badges
4. ✅ Remove modal approach (if any)

**Create.vue:**
1. ✅ Create section-based layout (like Customer)
2. ✅ Sections:
   - **Order Information** (customer, sales, dates)
   - **Material Details** (jenis_bahan, gramasi, volume)
   - **Pricing** (harga_jual_pcs, jumlah_cetak, total)
   - **Order Items** (products with custom pricing)
   - **Additional Info** (catatan)
3. ✅ Implement ProductCustomerPrice lookup
4. ✅ Auto-calculate total based on items
5. ✅ Add date pickers for tanggal_po, tanggal_kirim

**Edit.vue:**
1. ✅ Same sections as Create
2. ✅ Pre-populate all fields
3. ✅ Show order history/timeline
4. ✅ Allow status change
5. ✅ Show created_by info

**Deliverables:**
- Updated Index.vue
- Updated Create.vue
- Updated Edit.vue

---

### **Phase 4: Integration & Testing** (Day 3)

**Tasks:**
1. ✅ Test order creation with custom pricing
2. ✅ Test order creation without custom pricing
3. ✅ Test sales person dropdown
4. ✅ Test date pickers
5. ✅ Test order items calculation
6. ✅ Test order status workflow
7. ✅ Test validation rules
8. ✅ Test edit/update functionality
9. ✅ Test delete functionality
10. ✅ Test filters and sorting

**Test Scenarios:**
- Create order for customer with custom prices
- Create order for customer without custom prices
- Edit order and change items
- Change order status
- Delete order
- Filter by sales person
- Filter by date range
- Verify created_by tracking

**Deliverables:**
- Testing checklist completed
- Bug fixes if any

---

### **Phase 5: Documentation** (Day 4)

**Tasks:**
1. ✅ Create ORDER_MODULE_REFACTOR_COMPLETED.md
2. ✅ Document schema changes
3. ✅ Document business logic changes
4. ✅ Document UI changes
5. ✅ Create migration guide
6. ✅ Update REFACTOR_PLAN.md

**Deliverables:**
- Comprehensive documentation (3-4 files)

---

## 🔍 Key Challenges

### 1. **Profit/Loss Removal** 🔴 HIGH

**Old Logic:**
```php
// v1 - Calculate profit/loss at order level
$profit = $order->total - $order->cost;
```

**New Logic:**
```php
// v2 - No profit/loss calculation
// (Will be handled at reporting level)
```

**Impact:**
- Remove from Order model
- Remove from OrderService
- Remove from frontend displays
- Update reports/dashboard

---

### 2. **Custom Pricing Integration** 🟡 MEDIUM

**Logic:**
```php
// For each order item, check if custom price exists
$customPrice = ProductCustomerPriceService::getPrice(
    product_id: $productId,
    customer_id: $customerId
);

$price = $customPrice ?? $product->selling_price;
```

**Implementation:**
- In OrderService::create()
- In OrderService::update()
- In frontend Create.vue (live preview)
- In frontend Edit.vue (recalculation)

---

### 3. **Multi-Field Date Handling** 🟢 LOW

**New Fields:**
- order_date (existing)
- tanggal_po (new)
- tanggal_kirim (new)

**Validation:**
- tanggal_po should be <= order_date
- tanggal_kirim should be >= order_date
- All dates nullable for flexibility

---

### 4. **Sales Person Integration** 🟢 LOW

**Implementation:**
- Add sales_id dropdown in Create/Edit
- Load active sales list
- Optional field (nullable)
- Show in order listing

---

### 5. **Created By Tracking** 🟢 LOW

**Implementation:**
```php
// In OrderService::create()
'created_by' => auth()->id()
```

**Display:**
- Show in Edit form
- Show in order details
- Use for audit reports

---

## 📊 Migration Plan

### Migration 1: Add V2 Fields

```php
Schema::table('orders', function (Blueprint $table) {
    $table->foreignId('sales_id')
        ->nullable()
        ->after('customer_id')
        ->constrained('sales')
        ->nullOnDelete();
    
    $table->date('tanggal_po')->nullable()->after('order_date');
    $table->date('tanggal_kirim')->nullable()->after('tanggal_po');
    
    $table->string('jenis_bahan')->nullable()->after('status');
    $table->string('gramasi')->nullable()->after('jenis_bahan');
    $table->integer('volume')->nullable()->after('gramasi');
    $table->decimal('harga_jual_pcs', 15, 2)->nullable()->after('volume');
    $table->integer('jumlah_cetak')->nullable()->after('harga_jual_pcs');
    $table->text('catatan')->nullable()->after('jumlah_cetak');
    
    $table->foreignId('created_by')
        ->nullable()
        ->after('catatan')
        ->constrained('users')
        ->nullOnDelete();
});
```

### Migration 2: Remove V1 Fields

```php
Schema::table('orders', function (Blueprint $table) {
    $table->dropColumn(['profit', 'loss']);
});
```

---

## 🎨 UI/UX Design

### Index Page Layout

```
┌─────────────────────────────────────────────────────────┐
│ Orders                                         [+ Create]│
├─────────────────────────────────────────────────────────┤
│ Filters: [Customer▾] [Sales▾] [Status▾] [Date Range]   │
├──────┬──────────┬──────────┬─────────┬────────┬────────┤
│ ID   │ Customer │ Sales    │ Tgl PO  │ Total  │ Status │
├──────┼──────────┼──────────┼─────────┼────────┼────────┤
│ #001 │ ABC Corp │ John Doe │ 01 Oct  │ 1.5M   │ ✅     │
│ #002 │ XYZ Ltd  │ Jane Doe │ 02 Oct  │ 2.3M   │ ⏳     │
└──────┴──────────┴──────────┴─────────┴────────┴────────┘
```

### Create/Edit Page Layout

```
┌─────────────────────────────────────────────────────────┐
│ Create Order                              [Cancel] [Save]│
├─────────────────────────────────────────────────────────┤
│ 📋 Order Information                                     │
│ ─────────────────────────────────────────────────────── │
│ Customer*     [Dropdown: Select customer]               │
│ Sales Person  [Dropdown: Select sales (optional)]       │
│ Order Date*   [Date picker]                             │
│ Tgl PO        [Date picker]                             │
│ Tgl Kirim     [Date picker]                             │
│                                                          │
│ 📦 Material Details                                      │
│ ─────────────────────────────────────────────────────── │
│ Jenis Bahan   [Text input]                              │
│ Gramasi       [Text input]                              │
│ Volume        [Number input]                            │
│ Harga/Pcs     [Rp _______]                              │
│ Jumlah Cetak  [Number input]                            │
│                                                          │
│ 🛍️ Order Items                                          │
│ ─────────────────────────────────────────────────────── │
│ [+ Add Item]                                            │
│ Product          Qty    Price      Subtotal             │
│ Kertas A4        100    Rp 5,000   Rp 500,000  [Remove]│
│ Karton Box       50     Rp 10,000  Rp 500,000  [Remove]│
│                                                          │
│                          Total: Rp 1,000,000            │
│                                                          │
│ 📝 Additional Information                                │
│ ─────────────────────────────────────────────────────── │
│ Catatan       [Textarea for notes]                      │
└─────────────────────────────────────────────────────────┘
```

---

## ✅ Acceptance Criteria

### Backend
- [ ] Migration runs without errors
- [ ] All v2 fields added to database
- [ ] profit/loss columns removed
- [ ] Model relationships updated
- [ ] Validation rules comprehensive
- [ ] Service logic handles custom pricing
- [ ] created_by auto-populated
- [ ] Exception handling complete

### Frontend
- [ ] Index page shows v2 columns
- [ ] Filters work (sales, dates)
- [ ] Create page has 4 sections
- [ ] Custom pricing lookup works
- [ ] Total auto-calculates
- [ ] Date pickers functional
- [ ] Edit page pre-populates
- [ ] Order items editable
- [ ] Status change works
- [ ] Delete confirmation works

### Testing
- [ ] Can create order with custom pricing
- [ ] Can create order without custom pricing
- [ ] Can edit existing order
- [ ] Can change order status
- [ ] Can delete order
- [ ] Filters work correctly
- [ ] Validation prevents invalid data
- [ ] created_by tracked correctly

### Documentation
- [ ] Refactor documentation complete
- [ ] Migration guide created
- [ ] API changes documented
- [ ] UI changes documented

---

## 🚀 Success Metrics

1. **Functionality:** All CRUD operations work
2. **Integration:** Custom pricing integrated seamlessly
3. **Performance:** No performance degradation
4. **Code Quality:** Clean, maintainable code
5. **Documentation:** Comprehensive docs
6. **Testing:** All scenarios pass

---

## 📝 Notes for Developers

### Backend Team
- Focus on removing profit/loss logic completely
- Ensure custom pricing lookup is efficient
- Add proper indexes for sales_id FK
- Consider caching for ProductCustomerPrice queries

### Frontend Team
- Follow Customer module pattern for consistency
- Use section-based layout with colored dividers
- Implement debounce for price calculations
- Show loading states during API calls
- Provide clear validation feedback

### QA Team
- Test custom pricing extensively
- Verify date validations
- Test with different sales persons
- Check order status workflow
- Verify totals calculation accuracy

---

## ⚠️ Risks & Mitigation

### Risk 1: Data Migration Issues
**Mitigation:**
- Backup database before migration
- Test migrations on staging first
- Have rollback plan ready

### Risk 2: Custom Pricing Performance
**Mitigation:**
- Add database indexes
- Implement caching strategy
- Optimize queries

### Risk 3: Complex Order Creation Flow
**Mitigation:**
- Break into smaller components
- Add loading indicators
- Provide helpful validation messages

### Risk 4: Breaking Existing Transactions
**Mitigation:**
- Review Transaction module dependencies
- Update Transaction logic if needed
- Test transaction creation after order refactor

---

## 🎯 Definition of Done

Order Module Refactor is **COMPLETE** when:

1. ✅ All migrations executed successfully
2. ✅ Backend API updated and tested
3. ✅ Frontend UI redesigned and responsive
4. ✅ Custom pricing integration working
5. ✅ All test scenarios pass
6. ✅ Documentation complete
7. ✅ Code reviewed and approved
8. ✅ No critical bugs
9. ✅ Ready for production deployment

---

## 📅 Timeline

| Phase | Duration | Tasks |
|-------|----------|-------|
| **Phase 1:** Database | 4 hours | Migration, models, enums |
| **Phase 2:** Backend | 6 hours | Service, controller, validation |
| **Phase 3:** Frontend | 10 hours | Index, Create, Edit redesign |
| **Phase 4:** Testing | 6 hours | All test scenarios |
| **Phase 5:** Documentation | 4 hours | Complete documentation |
| **Total** | **30 hours** | **~4 days** |

---

## 🎉 Ready to Start?

Before starting, confirm:
- ✅ Customer module refactor complete
- ✅ Product module refactor complete
- ✅ ProductCustomerPrice feature working
- ✅ Sales table exists and populated
- ✅ Development environment ready
- ✅ Database backup created

**When ready, start with Phase 1: Database & Schema Review**

---

**Created by:** AI Assistant  
**Date:** October 8, 2025  
**Status:** 📋 **PLANNING COMPLETE - READY TO START**
