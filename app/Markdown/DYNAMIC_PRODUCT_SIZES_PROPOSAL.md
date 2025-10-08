# 🎯 PROPOSAL: Dynamic Product Sizes Implementation

**Date**: December 2024  
**Status**: 📋 PROPOSED (Pending Approval)  
**Priority**: ENHANCEMENT (Product Module Improvement)

---

## 🔍 Problem Analysis

### Current Structure (v2):
Product table memiliki **5 fixed columns** untuk ukuran:
- `ukuran` (varchar)
- `ukuran_potongan_1` (varchar)
- `ukuran_plano_1` (varchar)
- `ukuran_potongan_2` (varchar)
- `ukuran_plano_2` (varchar)

### ❌ Limitations:
1. **Not Scalable**: Maksimal hanya 2 variasi ukuran per produk
2. **Not Flexible**: Tidak bisa tambah lebih dari 2 potongan/plano
3. **Complex Queries**: Sulit untuk perhitungan efisiensi cutting
4. **Poor UX**: Form field terlalu banyak, membingungkan operator
5. **Redundant Data**: Kalau hanya butuh 1 ukuran, 4 field lain kosong

---

## ✅ Proposed Solution: Dynamic Product Sizes

### 1. Database Structure

#### New Table: `product_sizes`
```sql
CREATE TABLE product_sizes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NOT NULL,
    
    -- Size Details
    size_name VARCHAR(100) NULL,              -- e.g. "A4 Standard", "Custom Box"
    ukuran_potongan VARCHAR(100) NOT NULL,    -- e.g. "21 x 29.7 cm"
    ukuran_plano VARCHAR(100) NULL,           -- e.g. "65 x 100 cm"
    
    -- Calculation Fields
    width DECIMAL(10,2) NULL,                 -- Width in cm
    height DECIMAL(10,2) NULL,                -- Height in cm
    plano_width DECIMAL(10,2) NULL,           -- Plano width in cm
    plano_height DECIMAL(10,2) NULL,          -- Plano height in cm
    quantity_per_plano INT NULL,              -- How many pieces per plano
    waste_percentage DECIMAL(5,2) NULL,       -- Waste/scrap percentage
    
    -- Metadata
    notes TEXT NULL,                          -- Additional notes
    is_default BOOLEAN DEFAULT FALSE,         -- Default size for this product
    sort_order INT DEFAULT 0,                 -- Display order
    
    -- Timestamps
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    -- Foreign Keys
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    
    -- Indexes
    INDEX idx_product_id (product_id),
    INDEX idx_is_default (is_default)
);
```

#### Modified Table: `products`
```sql
-- REMOVE these columns:
ALTER TABLE products 
    DROP COLUMN ukuran,
    DROP COLUMN ukuran_potongan_1,
    DROP COLUMN ukuran_plano_1,
    DROP COLUMN ukuran_potongan_2,
    DROP COLUMN ukuran_plano_2;

-- Keep only: bahan, gramatur, alamat_pengiriman, keterangan_tambahan
```

---

## 🎨 Frontend Design

### Dynamic Repeater Component

```vue
<template>
  <div class="product-sizes-section">
    <div class="flex justify-between items-center mb-4">
      <h3 class="text-lg font-semibold">Product Sizes</h3>
      <button @click="addSize" class="btn-success">
        <i class="fa fa-plus"></i> Tambah Ukuran
      </button>
    </div>

    <!-- Size Repeater -->
    <div v-for="(size, index) in form.sizes" :key="index" 
         class="border rounded-lg p-4 mb-3 bg-gray-50">
      
      <div class="flex justify-between items-start mb-3">
        <span class="font-medium text-gray-700">Ukuran #{{ index + 1 }}</span>
        <div class="flex gap-2">
          <button @click="setDefault(index)" 
                  :class="size.is_default ? 'text-yellow-500' : 'text-gray-400'"
                  title="Set as default">
            <i class="fa fa-star"></i>
          </button>
          <button @click="removeSize(index)" 
                  class="text-red-500 hover:text-red-700"
                  :disabled="form.sizes.length === 1">
            <i class="fa fa-trash"></i>
          </button>
        </div>
      </div>

      <div class="grid grid-cols-2 gap-3">
        <!-- Size Name (Optional) -->
        <div>
          <label>Nama Ukuran (Optional)</label>
          <input v-model="size.size_name" 
                 placeholder="e.g. A4 Standard, Custom Box"
                 class="form-input">
        </div>

        <!-- Ukuran Potongan (Required) -->
        <div>
          <label>Ukuran Potongan <span class="text-red-500">*</span></label>
          <input v-model="size.ukuran_potongan" 
                 placeholder="e.g. 21 x 29.7 cm"
                 class="form-input" required>
        </div>

        <!-- Ukuran Plano (Optional) -->
        <div>
          <label>Ukuran Plano (Optional)</label>
          <input v-model="size.ukuran_plano" 
                 placeholder="e.g. 65 x 100 cm"
                 class="form-input">
        </div>

        <!-- Width & Height for calculations -->
        <div class="grid grid-cols-2 gap-2">
          <div>
            <label>Width (cm)</label>
            <input v-model.number="size.width" 
                   type="number" step="0.01"
                   placeholder="21"
                   class="form-input">
          </div>
          <div>
            <label>Height (cm)</label>
            <input v-model.number="size.height" 
                   type="number" step="0.01"
                   placeholder="29.7"
                   class="form-input">
          </div>
        </div>

        <!-- Plano Width & Height -->
        <div class="grid grid-cols-2 gap-2">
          <div>
            <label>Plano Width (cm)</label>
            <input v-model.number="size.plano_width" 
                   type="number" step="0.01"
                   placeholder="65"
                   class="form-input">
          </div>
          <div>
            <label>Plano Height (cm)</label>
            <input v-model.number="size.plano_height" 
                   type="number" step="0.01"
                   placeholder="100"
                   class="form-input">
          </div>
        </div>

        <!-- Quantity Per Plano -->
        <div>
          <label>Qty Per Plano (Optional)</label>
          <input v-model.number="size.quantity_per_plano" 
                 type="number"
                 placeholder="e.g. 12"
                 class="form-input">
          <span class="text-xs text-gray-500">Auto-calculated if dimensions provided</span>
        </div>

        <!-- Waste Percentage -->
        <div>
          <label>Waste % (Optional)</label>
          <input v-model.number="size.waste_percentage" 
                 type="number" step="0.01"
                 placeholder="e.g. 5"
                 class="form-input">
        </div>

        <!-- Notes -->
        <div class="col-span-2">
          <label>Catatan (Optional)</label>
          <textarea v-model="size.notes" 
                    rows="2"
                    placeholder="e.g. Khusus untuk pesanan box lipat"
                    class="form-input"></textarea>
        </div>
      </div>
    </div>

    <!-- Add Size Button (if no sizes) -->
    <div v-if="form.sizes.length === 0" 
         class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
      <button @click="addSize" class="btn-primary">
        <i class="fa fa-plus"></i> Tambah Ukuran Pertama
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const form = reactive({
  // ... other product fields
  sizes: [
    {
      size_name: null,
      ukuran_potongan: null,
      ukuran_plano: null,
      width: null,
      height: null,
      plano_width: null,
      plano_height: null,
      quantity_per_plano: null,
      waste_percentage: null,
      notes: null,
      is_default: true,
      sort_order: 0
    }
  ]
});

const addSize = () => {
  form.sizes.push({
    size_name: null,
    ukuran_potongan: null,
    ukuran_plano: null,
    width: null,
    height: null,
    plano_width: null,
    plano_height: null,
    quantity_per_plano: null,
    waste_percentage: null,
    notes: null,
    is_default: false,
    sort_order: form.sizes.length
  });
};

const removeSize = (index) => {
  if (form.sizes.length > 1) {
    form.sizes.splice(index, 1);
    // Reassign sort_order
    form.sizes.forEach((size, idx) => {
      size.sort_order = idx;
    });
  }
};

const setDefault = (index) => {
  form.sizes.forEach((size, idx) => {
    size.is_default = idx === index;
  });
};
</script>
```

---

## 🔧 Backend Implementation

### Model: ProductSize

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductSize extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'plano_width' => 'decimal:2',
        'plano_height' => 'decimal:2',
        'quantity_per_plano' => 'integer',
        'waste_percentage' => 'decimal:2',
        'is_default' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Calculate quantity per plano automatically
     */
    public function calculateQuantityPerPlano(): ?int
    {
        if (!$this->width || !$this->height || !$this->plano_width || !$this->plano_height) {
            return null;
        }

        // Calculate how many pieces fit in one plano
        $piecesWidth = floor($this->plano_width / $this->width);
        $piecesHeight = floor($this->plano_height / $this->height);

        return $piecesWidth * $piecesHeight;
    }

    /**
     * Calculate cutting efficiency
     */
    public function calculateEfficiency(): ?float
    {
        if (!$this->width || !$this->height || !$this->plano_width || !$this->plano_height) {
            return null;
        }

        $usedArea = ($this->width * $this->height) * $this->calculateQuantityPerPlano();
        $planoArea = $this->plano_width * $this->plano_height;

        return ($usedArea / $planoArea) * 100;
    }
}
```

### Updated Product Model

```php
// In Product.php
public function sizes()
{
    return $this->hasMany(ProductSize::class)->orderBy('sort_order');
}

public function defaultSize()
{
    return $this->hasOne(ProductSize::class)->where('is_default', true);
}
```

### ProductService Updates

```php
// In ProductService.php
public function create(array $payload): Product
{
    DB::beginTransaction();
    try {
        // Create product
        $product = $this->repository->create($processPayload);

        // Create sizes
        if (isset($payload['sizes']) && is_array($payload['sizes'])) {
            foreach ($payload['sizes'] as $sizeData) {
                $product->sizes()->create($sizeData);
            }
        }

        DB::commit();
        return $product;
    } catch (Exception $e) {
        DB::rollBack();
        throw $e;
    }
}

public function update(int $id, array $payload): Product
{
    DB::beginTransaction();
    try {
        $product = $this->findByIdOrFail($id);
        
        // Update product
        $this->repository->update($product, $processPayload);

        // Update sizes
        if (isset($payload['sizes']) && is_array($payload['sizes'])) {
            // Delete existing sizes
            $product->sizes()->delete();
            
            // Create new sizes
            foreach ($payload['sizes'] as $sizeData) {
                $product->sizes()->create($sizeData);
            }
        }

        DB::commit();
        return $product->fresh(['sizes']);
    } catch (Exception $e) {
        DB::rollBack();
        throw $e;
    }
}
```

---

## 📊 Benefits

### 1. **Scalability** 🚀
- Unlimited ukuran per produk
- Mudah tambah/hapus ukuran
- Tidak perlu alter table untuk tambah field

### 2. **Automation** 🤖
- Auto-calculate quantity per plano
- Auto-calculate cutting efficiency
- Auto-calculate waste percentage

### 3. **Flexibility** 🔄
- Bisa simpan dimensi detail (width/height)
- Support multiple variasi per produk
- Support custom naming per size

### 4. **Better UX** 😊
- Dynamic repeater lebih intuitif
- "+ Tambah Ukuran" lebih user-friendly
- Set default size dengan star icon
- Drag & drop untuk reorder (future enhancement)

### 5. **Reporting** 📈
- Mudah generate laporan efisiensi cutting
- Mudah calculate material requirements
- Mudah track waste per product size

### 6. **Data Integrity** ✅
- Cascade delete saat product dihapus
- Relational integrity maintained
- Easy to query and join

---

## 🔄 Migration Plan

### Step 1: Create Migration
```bash
php artisan make:migration create_product_sizes_table
```

### Step 2: Migrate Data (Optional)
```php
// If you want to migrate existing data from products table
public function up()
{
    // Create new table first
    Schema::create('product_sizes', function (Blueprint $table) {
        // ... table definition
    });

    // Migrate existing data
    DB::table('products')->whereNotNull('ukuran')->each(function ($product) {
        if ($product->ukuran) {
            DB::table('product_sizes')->insert([
                'product_id' => $product->id,
                'size_name' => 'Default',
                'ukuran_potongan' => $product->ukuran,
                'ukuran_plano' => $product->ukuran_plano_1,
                'is_default' => true,
                'sort_order' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    });

    // Drop old columns
    Schema::table('products', function (Blueprint $table) {
        $table->dropColumn(['ukuran', 'ukuran_potongan_1', 'ukuran_plano_1', 
                            'ukuran_potongan_2', 'ukuran_plano_2']);
    });
}
```

### Step 3: Update Models & Services
- Create `ProductSize` model
- Update `Product` model relationships
- Update `ProductService` to handle sizes
- Update validation rules

### Step 4: Update Frontend
- Create `ProductSizeRepeater.vue` component
- Update `Product/Create.vue`
- Update `Product/Edit.vue`
- Update `Product/Index.vue` to show sizes

### Step 5: Testing
- Test create product with multiple sizes
- Test edit/delete sizes
- Test size calculations
- Test cascade delete

---

## ⏱️ Estimated Time

| Task | Estimated Time |
|------|----------------|
| Migration & Model | 30 minutes |
| Service Layer Updates | 45 minutes |
| Frontend Component | 1.5 hours |
| Testing & Bug Fixes | 1 hour |
| Documentation | 30 minutes |
| **TOTAL** | **~4 hours** |

---

## ⚠️ Breaking Changes

### Database:
- ❌ Columns removed from `products` table
- ✅ New table `product_sizes` created
- ⚠️ Existing data needs migration

### API:
- ⚠️ Product create/update payload structure changed
- ✅ Backward compatible if migration script preserves data

### Frontend:
- ❌ Old size fields removed
- ✅ New dynamic repeater component

---

## 🎯 Acceptance Criteria

- [ ] User can add unlimited sizes per product
- [ ] User can edit/delete individual sizes
- [ ] User can set default size (star icon)
- [ ] Auto-calculate quantity per plano (if dimensions provided)
- [ ] Auto-calculate cutting efficiency
- [ ] Form validation works correctly
- [ ] Cascade delete works when product deleted
- [ ] Index page shows default size in table
- [ ] Edit page loads existing sizes correctly
- [ ] No breaking changes in other modules

---

## 📝 Decision Required

**Please review this proposal and decide:**

1. ✅ **APPROVE** - Proceed with implementation
2. ⏸️ **DEFER** - Implement later (use current structure for now)
3. 🔄 **REVISE** - Changes needed (provide feedback)

**What would you like to do?**

---

**Prepared by**: AI Assistant (Copilot)  
**Document version**: 1.0
