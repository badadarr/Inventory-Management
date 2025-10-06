<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        "sub_total"      => "double",
        "tax_total"      => "double",
        "discount_total" => "double",
        "charge"         => "double",
        "total"          => "double",
        "paid"           => "double",
        "due"            => "double",
        "profit"         => "double",
        "loss"           => "double",
        "tanggal_po"     => "date",
        "tanggal_kirim"  => "date",
        "volume"         => "integer",
        "harga_jual_pcs" => "double",
        "jumlah_cetak"   => "integer",
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function salesPoints(): HasMany
    {
        return $this->hasMany(SalesPoint::class);
    }

    public function getTotalOutstandingAttribute()
    {
        return $this->total + ($this->charge ?? 0) - $this->paid;
    }
}
