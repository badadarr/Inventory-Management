<?php

namespace App\Services;

use App\Enums\Core\SortOrderEnum;
use App\Enums\Product\ProductFieldsEnum;
use App\Enums\Product\ProductFiltersEnum;
use App\Exceptions\DBCommitException;
use App\Exceptions\ProductNotFoundException;
use App\Helpers\ArrayHelper;
use App\Helpers\BaseHelper;
use App\Models\Product;
use App\Models\ProductSize;
use App\Repositories\ProductRepository;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductService
{
    public function __construct(
        private readonly ProductRepository  $repository,
        private readonly FileManagerService $fileManagerService
    )
    {
    }

    /**
     * @param array $queryParameters
     * @return LengthAwarePaginator
     */
    public function getAll(array $queryParameters): LengthAwarePaginator
    {
        $page = $queryParameters["page"] ?? 1;
        $perPage = BaseHelper::perPage($queryParameters["per_page"] ?? null);

        return $this->repository->getAll(
            page: $page,
            perPage: $perPage,
            filters: ArrayHelper::getFiltersValues($queryParameters, ProductFiltersEnum::values()),
            fields: $queryParameters["fields"] ?? [],
            expand: $queryParameters["expand"] ?? [],
            sortBy: $queryParameters["sort_by"] ?? ProductFieldsEnum::CREATED_AT->value,
            sortOrder: $queryParameters["sort_order"] ?? SortOrderEnum::DESC->value,
        );
    }

    /**
     * @param int $id
     * @param array $expands
     * @return Product|null
     * @throws ProductNotFoundException
     */
    public function findByIdOrFail(int $id, array $expands = []): ?Product
    {
        // Always include sizes in the query
        if (!in_array('sizes', $expands)) {
            $expands[] = 'sizes';
        }

        $product = $this->repository->find([
            ProductFiltersEnum::ID->value => $id
        ], $expands);

        if (!$product) {
            throw new ProductNotFoundException('Product not found by the given id.');
        }

        return $product;
    }

    /**
     * @param array $payload
     * @return mixed
     * @throws DBCommitException
     */
    public function create(array $payload): mixed
    {
        return DB::transaction(function () use ($payload) {
            $photo = $this->fileManagerService->uploadFile(
                file: $payload['photo'],
                uploadPath: Product::PHOTO_PATH
            );

            $processPayload = [
                ProductFieldsEnum::CATEGORY_ID->value    => $payload[ProductFieldsEnum::CATEGORY_ID->value],
                ProductFieldsEnum::SUPPLIER_ID->value    => $payload[ProductFieldsEnum::SUPPLIER_ID->value] ?? null,
                ProductFieldsEnum::NAME->value           => $payload[ProductFieldsEnum::NAME->value],
                ProductFieldsEnum::BAHAN->value          => $payload[ProductFieldsEnum::BAHAN->value] ?? null,
                ProductFieldsEnum::GRAMATUR->value       => $payload[ProductFieldsEnum::GRAMATUR->value] ?? null,
                ProductFieldsEnum::ALAMAT_PENGIRIMAN->value => $payload[ProductFieldsEnum::ALAMAT_PENGIRIMAN->value] ?? null,
                ProductFieldsEnum::PRODUCT_CODE->value   => $payload[ProductFieldsEnum::PRODUCT_CODE->value] ?? null,
                ProductFieldsEnum::BUYING_PRICE->value   => $payload[ProductFieldsEnum::BUYING_PRICE->value],
                ProductFieldsEnum::SELLING_PRICE->value  => $payload[ProductFieldsEnum::SELLING_PRICE->value],
                ProductFieldsEnum::UNIT_TYPE_ID->value   => $payload[ProductFieldsEnum::UNIT_TYPE_ID->value],
                ProductFieldsEnum::QUANTITY->value       => $payload[ProductFieldsEnum::QUANTITY->value],
                ProductFieldsEnum::REORDER_LEVEL->value  => $payload[ProductFieldsEnum::REORDER_LEVEL->value] ?? null,
                ProductFieldsEnum::KETERANGAN_TAMBAHAN->value => $payload[ProductFieldsEnum::KETERANGAN_TAMBAHAN->value] ?? null,
                ProductFieldsEnum::PHOTO->value          => $photo,
                ProductFieldsEnum::STATUS->value         => $payload[ProductFieldsEnum::STATUS->value],
            ];

            $product = $this->repository->create($processPayload);

            // Handle product sizes
            if (isset($payload['sizes']) && is_array($payload['sizes'])) {
                foreach ($payload['sizes'] as $index => $sizeData) {
                    $sizePayload = [
                        'product_id' => $product->id,
                        'size_name' => $sizeData['size_name'] ?? null,
                        'ukuran_potongan' => $sizeData['ukuran_potongan'],
                        'ukuran_plano' => $sizeData['ukuran_plano'] ?? null,
                        'width' => $sizeData['width'] ?? null,
                        'height' => $sizeData['height'] ?? null,
                        'plano_width' => $sizeData['plano_width'] ?? null,
                        'plano_height' => $sizeData['plano_height'] ?? null,
                        'notes' => $sizeData['notes'] ?? null,
                        'is_default' => $sizeData['is_default'] ?? ($index === 0), // First size is default if not specified
                        'sort_order' => $sizeData['sort_order'] ?? $index,
                    ];

                    // Auto-calculate quantity_per_plano if dimensions are provided
                    if (isset($sizePayload['width'], $sizePayload['height'], $sizePayload['plano_width'], $sizePayload['plano_height'])) {
                        $size = new ProductSize($sizePayload);
                        $sizePayload['quantity_per_plano'] = $size->calculateQuantityPerPlano();
                        $sizePayload['waste_percentage'] = $size->calculateEfficiency() ? (100 - $size->calculateEfficiency()) : null;
                    }

                    ProductSize::create($sizePayload);
                }
            }

            // Load sizes relationship before returning
            return $product->load('sizes');
        });
    }

    /**
     * @param int $id
     * @param array $payload
     * @return Product
     * @throws ProductNotFoundException
     * @throws Exception
     */
    public function update(int $id, array $payload): Product
    {
        return DB::transaction(function () use ($id, $payload) {
            $product = $this->findByIdOrFail($id);

            $photo = $product->getRawOriginal(ProductFieldsEnum::PHOTO->value);
            if (isset($payload['photo'])) {
                $photo = $this->fileManagerService->uploadFile(
                    file: $payload['photo'],
                    uploadPath: Product::PHOTO_PATH,
                    deleteFileName: $photo
                );
            }

            $processPayload = [
                ProductFieldsEnum::CATEGORY_ID->value   => $payload[ProductFieldsEnum::CATEGORY_ID->value] ?? $product->category_id,
                ProductFieldsEnum::SUPPLIER_ID->value   => $payload[ProductFieldsEnum::SUPPLIER_ID->value] ?? $product->supplier_id,
                ProductFieldsEnum::NAME->value          => $payload[ProductFieldsEnum::NAME->value] ?? $product->name,
                ProductFieldsEnum::BAHAN->value         => $payload[ProductFieldsEnum::BAHAN->value] ?? $product->bahan,
                ProductFieldsEnum::GRAMATUR->value      => $payload[ProductFieldsEnum::GRAMATUR->value] ?? $product->gramatur,
                ProductFieldsEnum::ALAMAT_PENGIRIMAN->value => $payload[ProductFieldsEnum::ALAMAT_PENGIRIMAN->value] ?? $product->alamat_pengiriman,
                ProductFieldsEnum::PRODUCT_CODE->value  => $payload[ProductFieldsEnum::PRODUCT_CODE->value] ?? $product->product_code,
                ProductFieldsEnum::BUYING_PRICE->value  => $payload[ProductFieldsEnum::BUYING_PRICE->value] ?? $product->buying_price,
                ProductFieldsEnum::SELLING_PRICE->value => $payload[ProductFieldsEnum::SELLING_PRICE->value] ?? $product->selling_price,
                ProductFieldsEnum::UNIT_TYPE_ID->value  => $payload[ProductFieldsEnum::UNIT_TYPE_ID->value] ?? $product->unit_type_id,
                ProductFieldsEnum::QUANTITY->value      => $payload[ProductFieldsEnum::QUANTITY->value] ?? $product->quantity,
                ProductFieldsEnum::REORDER_LEVEL->value => $payload[ProductFieldsEnum::REORDER_LEVEL->value] ?? $product->reorder_level,
                ProductFieldsEnum::KETERANGAN_TAMBAHAN->value => $payload[ProductFieldsEnum::KETERANGAN_TAMBAHAN->value] ?? $product->keterangan_tambahan,
                ProductFieldsEnum::PHOTO->value         => $photo,
                ProductFieldsEnum::STATUS->value        => $payload[ProductFieldsEnum::STATUS->value] ?? $product->status,
            ];

            $updatedProduct = $this->repository->update($product, $processPayload);

            // Handle product sizes update
            if (isset($payload['sizes']) && is_array($payload['sizes'])) {
                // Delete existing sizes
                $product->sizes()->delete();

                // Create new sizes
                foreach ($payload['sizes'] as $index => $sizeData) {
                    $sizePayload = [
                        'product_id' => $product->id,
                        'size_name' => $sizeData['size_name'] ?? null,
                        'ukuran_potongan' => $sizeData['ukuran_potongan'],
                        'ukuran_plano' => $sizeData['ukuran_plano'] ?? null,
                        'width' => $sizeData['width'] ?? null,
                        'height' => $sizeData['height'] ?? null,
                        'plano_width' => $sizeData['plano_width'] ?? null,
                        'plano_height' => $sizeData['plano_height'] ?? null,
                        'notes' => $sizeData['notes'] ?? null,
                        'is_default' => $sizeData['is_default'] ?? ($index === 0),
                        'sort_order' => $sizeData['sort_order'] ?? $index,
                    ];

                    // Auto-calculate quantity_per_plano if dimensions are provided
                    if (isset($sizePayload['width'], $sizePayload['height'], $sizePayload['plano_width'], $sizePayload['plano_height'])) {
                        $size = new ProductSize($sizePayload);
                        $sizePayload['quantity_per_plano'] = $size->calculateQuantityPerPlano();
                        $sizePayload['waste_percentage'] = $size->calculateEfficiency() ? (100 - $size->calculateEfficiency()) : null;
                    }

                    ProductSize::create($sizePayload);
                }
            }

            // Load sizes relationship before returning
            return $updatedProduct->load('sizes');
        });
    }

    /**
     * @param int $id
     * @return bool|null
     * @throws ProductNotFoundException
     */
    public function delete(int $id): ?bool
    {
        $product = $this->findByIdOrFail($id);
        $photo = $product->getRawOriginal(ProductFieldsEnum::PHOTO->value);
        if ($photo) {
            $this->fileManagerService->delete(
                fileName: $photo,
                path: Product::PHOTO_PATH,
            );
        }

        // Todo: prevent delete for available orders

        return $this->repository->delete($product);
    }

    /**
     * Get products with low stock (need reordering) - Inventory v2
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getLowStockProducts()
    {
        return Product::whereColumn('quantity', '<=', 'reorder_level')
            ->with(['category', 'supplier', 'unitType'])
            ->get();
    }

    /**
     * Get products needing reorder for dashboard alert - Inventory v2
     * @return int
     */
    public function getLowStockCount(): int
    {
        return Product::whereColumn('quantity', '<=', 'reorder_level')->count();
    }
}
