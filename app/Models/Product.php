<?php

namespace App\Models;

use App\Helpers\BaseHelper;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    const PHOTO_PATH = "products";

    protected $casts = [
        "buying_price"  => "double",
        "selling_price" => "double",
        "quantity"      => "double",
        "reorder_level" => "double",
    ];

    protected function photo(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => BaseHelper::storageLink(
                fileName: $value,
                folderPath: self::PHOTO_PATH
            ),
        );
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function unitType(): BelongsTo
    {
        return $this->belongsTo(UnitType::class, 'unit_type_id');
    }

    public function customerPrices()
    {
        return $this->hasMany(ProductCustomerPrice::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get all sizes for this product
     */
    public function sizes(): HasMany
    {
        return $this->hasMany(ProductSize::class)->orderBy('sort_order');
    }

    /**
     * Get the default size for this product
     */
    public function defaultSize(): HasOne
    {
        return $this->hasOne(ProductSize::class)->where('is_default', true);
    }

    /**
     * Check if product needs reorder
     */
    public function needsReorder(): bool
    {
        return $this->quantity <= $this->reorder_level;
    }

    /**
     * Get custom price for specific customer
     */
    public function getCustomerPrice(int $customerId)
    {
        $customPrice = $this->customerPrices()->where('customer_id', $customerId)->first();
        return $customPrice ? $customPrice->custom_price : $this->selling_price;
    }
}
