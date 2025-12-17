<?php

namespace Database\Seeders;

use App\Enums\Product\ProductFieldsEnum;
use App\Enums\Product\ProductStatusEnum;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\UnitType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = Supplier::all();
        $unitTypes = UnitType::all();
        $categories = Category::all();

        $productsPayload = [];
        foreach ($categories as $category) {
            for ($i = 0; $i < 5; $i++) {
                $buyingPrice = rand(10000, 500000);
                $productsPayload[] = [
                    ProductFieldsEnum::CATEGORY_ID->value    => $category->id,
                    ProductFieldsEnum::SUPPLIER_ID->value    => $suppliers->random()->id,
                    ProductFieldsEnum::NAME->value           => fake()->words(3, true),
                    ProductFieldsEnum::PRODUCT_NUMBER->value => 'P-' . Str::random(5),
                    ProductFieldsEnum::DESCRIPTION->value    => fake()->sentence(),
                    ProductFieldsEnum::PRODUCT_CODE->value   => Str::random(3),
                    ProductFieldsEnum::ROOT->value           => Str::random(3),
                    ProductFieldsEnum::BUYING_PRICE->value   => $buyingPrice,
                    ProductFieldsEnum::SELLING_PRICE->value  => $buyingPrice + rand(10000, 100000),
                    ProductFieldsEnum::BUYING_DATE->value    => fake()->date,
                    ProductFieldsEnum::UNIT_TYPE_ID->value   => $unitTypes->random()->id,
                    ProductFieldsEnum::QUANTITY->value       => rand(10, 500),
                    ProductFieldsEnum::PHOTO->value          => 'placeholder.jpg',
                    ProductFieldsEnum::STATUS->value         => ProductStatusEnum::ACTIVE->value,
                    ProductFieldsEnum::CREATED_AT->value     => now(),
                    "updated_at"                             => now(),
                ];
            }
        }

        Product::insert($productsPayload);
    }
}
