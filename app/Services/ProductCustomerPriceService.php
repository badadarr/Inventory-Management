<?php

namespace App\Services;

use App\Models\ProductCustomerPrice;
use App\Repositories\ProductCustomerPriceRepository;

class ProductCustomerPriceService
{
    public function __construct(
        private readonly ProductCustomerPriceRepository $repository
    )
    {
    }

    /**
     * Get custom prices by product
     */
    public function getByProduct(int $productId): \Illuminate\Database\Eloquent\Collection|array
    {
        return $this->repository->getByProduct($productId);
    }

    /**
     * Get custom prices by customer
     */
    public function getByCustomer(int $customerId): \Illuminate\Database\Eloquent\Collection|array
    {
        return $this->repository->getByCustomer($customerId);
    }

    /**
     * Find specific custom price
     */
    public function find(int $productId, int $customerId): ?ProductCustomerPrice
    {
        return $this->repository->find($productId, $customerId);
    }

    /**
     * Create or update custom price
     */
    public function upsert(array $payload): ProductCustomerPrice
    {
        return $this->repository->upsert($payload);
    }

    /**
     * Delete custom price
     */
    public function delete(ProductCustomerPrice $customPrice): bool
    {
        return $this->repository->delete($customPrice);
    }
}
