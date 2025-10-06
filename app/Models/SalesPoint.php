<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesPoint extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'jumlah_cetak' => 'integer',
        'points' => 'double',
    ];

    public function sales(): BelongsTo
    {
        return $this->belongsTo(Sales::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
