<?php

namespace App\Models;

use App\Helpers\BaseHelper;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    const PHOTO_PATH = "customers";

    protected $casts = [
        'harga_komisi_standar' => 'double',
        'harga_komisi_extra' => 'double',
        'repeat_order_count' => 'integer',
        'tanggal_join' => 'date',
    ];

    public function sales()
    {
        return $this->belongsTo(Sales::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function productCustomerPrices()
    {
        return $this->hasMany(ProductCustomerPrice::class);
    }

    protected function photo(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => BaseHelper::storageLink(
                fileName: $value,
                folderPath: self::PHOTO_PATH
            ),
        );
    }

    /**
     * Increment repeat order count
     */
    public function incrementRepeatOrder()
    {
        $this->increment('repeat_order_count');
        if ($this->repeat_order_count > 0 && $this->status_customer === 'baru') {
            $this->update(['status_customer' => 'repeat']);
        }
    }
}
