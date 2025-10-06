<?php

namespace App\Models;

use App\Helpers\BaseHelper;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sales extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    const PHOTO_PATH = "sales";

    protected function photo(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => BaseHelper::storageLink(
                fileName: $value,
                folderPath: self::PHOTO_PATH
            ),
        );
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function salesPoints(): HasMany
    {
        return $this->hasMany(SalesPoint::class);
    }
}
