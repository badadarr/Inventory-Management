<?php

namespace App\Repositories;

use App\Exceptions\DBCommitException;
use App\Models\ProductCustomerPrice;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ProductCustomerPriceRepository
{
    /**
     * Get all custom prices for a product
     */
    public function getByProduct(int $productId): \Illuminate\Database\Eloquent\Collection|array
    {
        return ProductCustomerPrice::where('product_id', $productId)
            ->with(['customer', 'product'])
            ->get();
    }

    /**
     * Get all custom prices for a customer
     */
    public function getByCustomer(int $customerId): \Illuminate\Database\Eloquent\Collection|array
    {
        return ProductCustomerPrice::where('customer_id', $customerId)
            ->with(['customer', 'product'])
            ->get();
    }

    /**
     * Find specific custom price
     */
    public function find(int $productId, int $customerId): ?ProductCustomerPrice
    {
        return ProductCustomerPrice::where('product_id', $productId)
            ->where('customer_id', $customerId)
            ->first();
    }

    /**
     * Create or update custom price
     */
    public function upsert(array $payload): ProductCustomerPrice
    {
        try {
            DB::beginTransaction();
            
            $customPrice = ProductCustomerPrice::updateOrCreate(
                [
                    'product_id' => $payload['product_id'],
                    'customer_id' => $payload['customer_id'],
                ],
                [
                    'custom_price' => $payload['custom_price'],
                    'notes' => $payload['notes'] ?? null,
                ]
            );
            
            DB::commit();
            return $customPrice;
        } catch (Exception $exception) {
            DB::rollBack();
            throw new DBCommitException($exception);
        }
    }

    /**
     * Delete custom price
     */
    public function delete(ProductCustomerPrice $customPrice): bool
    {
        try {
            DB::beginTransaction();
            $customPrice->delete();
            DB::commit();
            return true;
        } catch (Exception $exception) {
            DB::rollBack();
            throw new DBCommitException($exception);
        }
    }
}
