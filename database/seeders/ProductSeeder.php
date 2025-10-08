<?php

namespace Database\Seeders;

use App\Enums\Product\ProductFieldsEnum;
use App\Enums\Product\ProductStatusEnum;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\UnitType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
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
        foreach ($categories as $category) {
            $categorySlug = Str::slug($category->name);
            $response = Http::get("https://dummyjson.com/products/category/{$categorySlug}");
            if ($response->successful()) {
                $products = array_splice($response->object()->products, 2);

                $productsPayload = [];
                foreach ($products as $product) {
                    // Download the image
                    $imageContent = Http::retry(2)->get($product->images[0])->body();
                    $imageName = basename($product->images[0]);
                    $imagePath = 'products/' . $imageName;
                    Storage::put($imagePath, $imageContent);

                    $productsPayload[] = [
                        ProductFieldsEnum::CATEGORY_ID->value          => $category->id,
                        ProductFieldsEnum::SUPPLIER_ID->value          => $suppliers->random()->id,
                        ProductFieldsEnum::UNIT_TYPE_ID->value         => $unitTypes->random()->id,
                        ProductFieldsEnum::PRODUCT_CODE->value         => 'PRD-' . strtoupper(Str::random(6)),
                        ProductFieldsEnum::NAME->value                 => $product->title,
                        ProductFieldsEnum::BUYING_PRICE->value         => $product->price,
                        ProductFieldsEnum::SELLING_PRICE->value        => $product->price + rand(10, 100),
                        ProductFieldsEnum::QUANTITY->value             => $product->stock,
                        ProductFieldsEnum::REORDER_LEVEL->value        => max(10, $product->stock * 0.2), // 20% of stock
                        ProductFieldsEnum::PHOTO->value                => $imageName,
                        ProductFieldsEnum::STATUS->value               => ProductStatusEnum::ACTIVE->value,
                        ProductFieldsEnum::KETERANGAN_TAMBAHAN->value  => $product->description,
                        ProductFieldsEnum::CREATED_AT->value           => now(),
                        "updated_at"                                   => now(),
                    ];
                }

                Product::insert($productsPayload);
            } else {
                $this->command->error('Failed to seed product data.');
            }
        }
    }
}
