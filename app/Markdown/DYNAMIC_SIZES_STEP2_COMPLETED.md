# Dynamic Product Sizes - Step 2: Service Layer Updates

**Status**: ✅ COMPLETED  
**Date**: October 8, 2025  
**Estimated Time**: 45 minutes  
**Actual Time**: ~40 minutes

## Overview
Updated ProductService to handle dynamic product sizes array instead of fixed ukuran columns. Implemented DB transactions for data integrity and auto-calculation features.

---

## Files Modified

### 1. **app/Services/ProductService.php**

#### Changes Made:

**a) Added Imports:**
```php
use App\Models\ProductSize;
use Illuminate\Support\Facades\DB;
```

**b) Updated `create()` Method:**
- ✅ Wrapped in `DB::transaction()` for atomicity
- ✅ Removed old ukuran field processing (ukuran, ukuran_potongan_1, ukuran_plano_1, ukuran_potongan_2, ukuran_plano_2)
- ✅ Added sizes array handling with loop
- ✅ Auto-calculate `quantity_per_plano` from dimensions
- ✅ Auto-calculate `waste_percentage` from efficiency
- ✅ Set first size as default if not specified
- ✅ Load sizes relationship before returning

**Logic Flow:**
1. Upload photo
2. Create product with basic fields
3. Loop through `sizes` array from payload
4. For each size:
   - Extract size data (size_name, ukuran_potongan, dimensions, etc.)
   - If dimensions provided → auto-calculate qty per plano & waste %
   - Set is_default (first size = true if not specified)
   - Set sort_order (index if not specified)
   - Create ProductSize record
5. Return product with sizes loaded

**c) Updated `update()` Method:**
- ✅ Wrapped in `DB::transaction()` for atomicity
- ✅ Removed old ukuran field processing
- ✅ Delete existing sizes before creating new ones
- ✅ Same logic as create() for sizes array handling
- ✅ Load sizes relationship before returning

**Logic Flow:**
1. Find product by ID
2. Handle photo update if provided
3. Update product basic fields
4. Delete all existing ProductSize records (cascade safe)
5. Loop through new `sizes` array
6. Create fresh ProductSize records with auto-calculations
7. Return updated product with sizes loaded

**d) Updated `findByIdOrFail()` Method:**
- ✅ Always include 'sizes' in expands array
- ✅ Ensures sizes are loaded for edit forms

---

### 2. **app/Enums/Product/ProductFieldsEnum.php**

#### Changes Made:

**Removed Old Cases (v1 fields):**
```php
// REMOVED:
case PRODUCT_NUMBER = 'product_number';
case DESCRIPTION    = 'description';
case UKURAN         = 'ukuran';
case UKURAN_POTONGAN_1 = 'ukuran_potongan_1';
case UKURAN_PLANO_1 = 'ukuran_plano_1';
case UKURAN_POTONGAN_2 = 'ukuran_potongan_2';
case UKURAN_PLANO_2 = 'ukuran_plano_2';
case ROOT           = 'root';
case BUYING_DATE    = 'buying_date';
```

**Added New Cases (v2 fields):**
```php
// ADDED:
case REORDER_LEVEL = 'reorder_level';
case KETERANGAN_TAMBAHAN = 'keterangan_tambahan';
```

**Updated labels() array:**
- Removed labels for deleted fields
- Added labels for new v2 fields
- Maintained alphabetical-ish order for readability

---

## Auto-Calculation Logic

### Quantity Per Plano Calculation:
```php
// Formula: floor(plano_width / width) × floor(plano_height / height)
$size = new ProductSize($sizePayload);
$sizePayload['quantity_per_plano'] = $size->calculateQuantityPerPlano();
```

**Example:**
- Size: 21 x 29.7 cm
- Plano: 65 x 100 cm
- Calculation: floor(65/21) × floor(100/29.7) = 3 × 3 = **9 pieces**

### Waste Percentage Calculation:
```php
// Formula: 100 - efficiency%
$efficiency = $size->calculateEfficiency(); // 86.36%
$sizePayload['waste_percentage'] = 100 - 86.36; // 13.64%
```

---

## Payload Structure

### Expected Request Payload:

```json
{
  "category_id": 1,
  "supplier_id": 2,
  "name": "Art Paper 210 gsm",
  "bahan": "Art Paper",
  "gramatur": "210 gsm",
  "product_code": "AP-210-A4",
  "buying_price": 50000,
  "selling_price": 75000,
  "unit_type_id": 3,
  "quantity": 100,
  "reorder_level": 20,
  "keterangan_tambahan": "Premium quality",
  "photo": "(file upload)",
  "status": "active",
  "sizes": [
    {
      "size_name": "A4 Standard",
      "ukuran_potongan": "21 x 29.7 cm",
      "ukuran_plano": "65 x 100 cm",
      "width": 21,
      "height": 29.7,
      "plano_width": 65,
      "plano_height": 100,
      "notes": "Standard A4 size",
      "is_default": true,
      "sort_order": 0
    },
    {
      "size_name": "A5 Custom",
      "ukuran_potongan": "14.8 x 21 cm",
      "ukuran_plano": "65 x 100 cm",
      "width": 14.8,
      "height": 21,
      "plano_width": 65,
      "plano_height": 100,
      "notes": "Half of A4",
      "is_default": false,
      "sort_order": 1
    }
  ]
}
```

### Notes:
- `sizes` array is **optional** - if not provided, product will have no sizes initially
- If `sizes` provided but `is_default` not specified, first size will be marked as default
- If `sizes` provided but `sort_order` not specified, array index will be used
- Dimensions (width, height, plano_width, plano_height) are **optional** - if provided, auto-calculations will run
- `quantity_per_plano` and `waste_percentage` are **auto-calculated** if dimensions provided

---

## Database Transaction Safety

### Why Transactions?

```php
DB::transaction(function () use ($payload) {
    // 1. Create product
    $product = $this->repository->create($processPayload);
    
    // 2. Create sizes (multiple inserts)
    foreach ($payload['sizes'] as $sizeData) {
        ProductSize::create($sizePayload);
    }
    
    // If any step fails, ALL changes are rolled back
    return $product->load('sizes');
});
```

**Benefits:**
- ✅ **Atomicity**: All or nothing - no orphaned records
- ✅ **Consistency**: Product always has valid state
- ✅ **Error Recovery**: Auto-rollback on any failure
- ✅ **Data Integrity**: No partial updates

---

## Testing Checklist

### Manual Testing Required:

- [ ] **Create Product**:
  - [ ] Without sizes array → should create product successfully
  - [ ] With 1 size → should create product + 1 size (marked as default)
  - [ ] With multiple sizes → should create product + all sizes
  - [ ] With dimensions → should auto-calculate qty_per_plano & waste_percentage
  - [ ] Without dimensions → should leave calculations null

- [ ] **Update Product**:
  - [ ] Add new sizes to existing product
  - [ ] Remove sizes (by sending smaller array)
  - [ ] Change default size (is_default flag)
  - [ ] Reorder sizes (sort_order values)
  - [ ] Update dimensions → recalculate automatically

- [ ] **Read Product**:
  - [ ] `findByIdOrFail()` should always load sizes
  - [ ] `getAll()` pagination works correctly
  - [ ] Sizes are ordered by sort_order

- [ ] **Delete Product**:
  - [ ] Should cascade delete all related sizes
  - [ ] No orphaned ProductSize records

---

## Migration Compatibility

### Old Payload (v1) vs New Payload (v2):

**❌ Old (No longer works):**
```json
{
  "ukuran": "21 x 29.7 cm",
  "ukuran_potongan_1": "21 x 29.7 cm",
  "ukuran_plano_1": "65 x 100 cm",
  "ukuran_potongan_2": "14.8 x 21 cm",
  "ukuran_plano_2": "65 x 100 cm"
}
```

**✅ New (Current):**
```json
{
  "sizes": [
    {
      "ukuran_potongan": "21 x 29.7 cm",
      "ukuran_plano": "65 x 100 cm",
      ...
    },
    {
      "ukuran_potongan": "14.8 x 21 cm",
      "ukuran_plano": "65 x 100 cm",
      ...
    }
  ]
}
```

### Frontend Changes Required:
- Replace 5 fixed fields with dynamic repeater component
- Send `sizes` array instead of individual ukuran fields
- Handle add/remove size functionality
- Handle set default size functionality

---

## Performance Considerations

### N+1 Query Prevention:

**Before:**
```php
$product = Product::find(1);
// Later...
$sizes = $product->sizes; // Lazy load = extra query
```

**After:**
```php
$product = $this->findByIdOrFail(1); // Eager loads sizes
$sizes = $product->sizes; // No extra query!
```

### Bulk Operations:

Currently using loop for size creation:
```php
foreach ($payload['sizes'] as $sizeData) {
    ProductSize::create($sizePayload); // Individual INSERT
}
```

**Future Optimization** (if needed):
```php
ProductSize::insert($allSizesPayload); // Single bulk INSERT
```

---

## Error Handling

### Transaction Rollback Scenarios:

1. **Invalid Product Data**: Transaction rolls back, no product created
2. **Invalid Size Data**: Transaction rolls back, no product + no sizes
3. **File Upload Failure**: Exception thrown before transaction starts
4. **Database Constraint Violation**: Transaction rolls back automatically

### Example Error Flow:

```php
try {
    $product = $this->create($payload); // DB transaction starts
    // ... product created
    // ... size 1 created
    // ... size 2 FAILS (invalid data)
    // ❌ Transaction rolls back
    // ❌ Product deleted
    // ❌ Size 1 deleted
} catch (Exception $e) {
    // Handle error, log, return response
}
```

---

## Next Steps

**✅ Step 1 Complete**: Migration & Model (30 min)  
**✅ Step 2 Complete**: Service Layer Updates (45 min)  
**⏳ Step 3 Pending**: Validation Rules (15 min)  
**⏳ Step 4 Pending**: Frontend Component (1.5 hours)  
**⏳ Step 5 Pending**: Testing & Documentation (1 hour)

---

## Summary

### What Changed:
- ✅ ProductService now handles `sizes` array instead of fixed columns
- ✅ DB transactions ensure data integrity
- ✅ Auto-calculation of quantity_per_plano and waste_percentage
- ✅ Sizes always loaded with product for edit forms
- ✅ ProductFieldsEnum cleaned up (removed v1 fields, added v2 fields)

### Benefits:
- 🚀 **Unlimited Sizes**: No more 2-size limit
- 🔒 **Data Integrity**: Transactions prevent partial updates
- 🧮 **Automation**: Auto-calculate cutting efficiency
- 📊 **Better Reports**: Can aggregate by size, calculate total waste, etc.
- 🔄 **Flexibility**: Easy to add/remove sizes without schema changes

### Impact:
- ⚠️ **Breaking Change**: Old payload format no longer works
- ⚠️ **Frontend Update Required**: Must send sizes array
- ⚠️ **Validation Update Required**: Need to validate sizes array structure
- ✅ **Backward Compatible**: Migration preserved existing data as "Default" size

**Total Progress**: ~1 hour 10 minutes of 4 hours (29% complete)
