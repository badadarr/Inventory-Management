<?php

namespace App\Repositories;

use App\Exceptions\DBCommitException;
use App\Models\StockMovement;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class StockMovementRepository
{
    /**
     * Get all stock movements with pagination
     */
    public function getAll(
        int    $page,
        int    $perPage,
        array  $filters = [],
        array  $fields = [],
        array  $expand = [],
        string $sortBy = "id",
        string $sortOrder = "DESC"
    ): LengthAwarePaginator
    {
        $query = $this->getQuery($filters)
            ->orderBy($sortBy, $sortOrder)
            ->with($expand);

        if (count($fields) > 0) {
            $query = $query->select($fields);
        }

        return $query->paginate(
            perPage: $perPage,
            page: $page
        )->withQueryString();
    }

    /**
     * Get stock movements by product
     */
    public function getByProduct(int $productId): \Illuminate\Database\Eloquent\Collection|array
    {
        return StockMovement::where('product_id', $productId)
            ->with(['product', 'createdBy'])
            ->orderBy('created_at', 'DESC')
            ->get();
    }

    /**
     * Create stock movement
     */
    public function create(array $payload): StockMovement
    {
        try {
            DB::beginTransaction();
            $movement = StockMovement::create($payload);
            DB::commit();
            return $movement;
        } catch (Exception $exception) {
            DB::rollBack();
            throw new DBCommitException($exception);
        }
    }

    /**
     * Record stock IN movement
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
        return $this->create([
            'product_id' => $productId,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'movement_type' => 'in',
            'quantity' => $quantity,
            'balance_after' => $balanceAfter,
            'created_by' => $createdBy,
            'notes' => $notes,
        ]);
    }

    /**
     * Record stock OUT movement
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
        return $this->create([
            'product_id' => $productId,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'movement_type' => 'out',
            'quantity' => $quantity,
            'balance_after' => $balanceAfter,
            'created_by' => $createdBy,
            'notes' => $notes,
        ]);
    }

    /**
     * Build query with filters
     */
    private function getQuery(array $filters = []): Builder
    {
        $query = StockMovement::query();

        // Filter by product
        if (isset($filters['product_id']) && $filters['product_id']) {
            $query->where('product_id', $filters['product_id']);
        }

        // Filter by movement type
        if (isset($filters['movement_type']) && $filters['movement_type']) {
            $query->where('movement_type', $filters['movement_type']);
        }

        // Filter by reference type
        if (isset($filters['reference_type']) && $filters['reference_type']) {
            $query->where('reference_type', $filters['reference_type']);
        }

        // Filter by date range
        if (isset($filters['date_from']) && $filters['date_from']) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to']) && $filters['date_to']) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        // Filter by created_by
        if (isset($filters['created_by']) && $filters['created_by']) {
            $query->where('created_by', $filters['created_by']);
        }

        return $query;
    }
}
