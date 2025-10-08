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

    public function sales(): BelongsTo
    {
        return $this->belongsTo(Sales::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function salesPoints(): HasMany
    {
        return $this->hasMany(SalesPoint::class);
    }

    public function getTotalOutstandingAttribute()
    {
        return $this->total + ($this->charge ?? 0) - $this->paid;
    }

    /**
     * Check if order is paid
     */
    public function isPaid(): bool
    {
        return $this->paid >= $this->total;
    }

    /**
     * Check if order is partially paid
     */
    public function isPartiallyPaid(): bool
    {
        return $this->paid > 0 && $this->paid < $this->total;
    }
}
