<?php

namespace App\Services;

use App\Enums\Core\SortOrderEnum;
use App\Helpers\BaseHelper;
use App\Models\StockMovement;
use App\Repositories\StockMovementRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class StockMovementService
{
    public function __construct(
        private readonly StockMovementRepository $repository
    )
    {
    }

    /**
     * Get all stock movements
     */
    public function getAll(array $queryParameters): LengthAwarePaginator
    {
        $page = $queryParameters["page"] ?? 1;
        $perPage = BaseHelper::perPage($queryParameters["per_page"] ?? null);

        return $this->repository->getAll(
            page: $page,
            perPage: $perPage,
            filters: $queryParameters,
            fields: $queryParameters["fields"] ?? [],
            expand: $queryParameters["expand"] ?? ['product', 'createdBy'],
            sortBy: $queryParameters["sort_by"] ?? 'id',
            sortOrder: $queryParameters["sort_order"] ?? SortOrderEnum::DESC->value,
        );
    }

    /**
     * Get stock movements by product
     */
    public function getByProduct(int $productId): \Illuminate\Database\Eloquent\Collection|array
    {
        return $this->repository->getByProduct($productId);
    }

    /**
     * Record stock IN
     */
    public function recordStockIn(
        int $productId,
        float $quantity,
        float $balanceAfter,
        string $referenceType,
        ?int $referenceId = null,
        ?int $createdBy = null,
        ?string $notes = null
    ): StockMovement
    {
        return $this->repository->recordStockIn(
            productId: $productId,
            quantity: $quantity,
            balanceAfter: $balanceAfter,
            referenceType: $referenceType,
            referenceId: $referenceId,
            createdBy: $createdBy,
            notes: $notes
        );
    }

    /**
     * Record stock OUT
     */
    public function recordStockOut(
        int $productId,
        float $quantity,
        float $balanceAfter,
        string $referenceType,
        ?int $referenceId = null,
        ?int $createdBy = null,
        ?string $notes = null
    ): StockMovement
    {
        return $this->repository->recordStockOut(
            productId: $productId,
            quantity: $quantity,
            balanceAfter: $balanceAfter,
            referenceType: $referenceType,
            referenceId: $referenceId,
            createdBy: $createdBy,
            notes: $notes
        );
    }
}
