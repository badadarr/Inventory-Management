<?php

namespace App\Repositories;

use App\Exceptions\DBCommitException;
use App\Models\PurchaseOrder;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PurchaseOrderRepository
{
    const MAX_RETRY = 5;

    /**
     * Get all purchase orders with pagination
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
     * Check if purchase order exists
     */
    public function exists(array $filters = []): bool
    {
        return $this->getQuery($filters)->exists();
    }

    /**
     * Find a purchase order
     */
    public function find(array $filters = [], array $expand = []): ?PurchaseOrder
    {
        return $this->getQuery($filters)
            ->with($expand)
            ->first();
    }

    /**
     * Create new purchase order
     */
    public function create(array $payload): mixed
    {
        try {
            DB::beginTransaction();
            $purchaseOrder = PurchaseOrder::create($payload);
            DB::commit();
            return $purchaseOrder;
        } catch (Exception $exception) {
            DB::rollBack();
            throw new DBCommitException($exception);
        }
    }

    /**
     * Update purchase order
     */
    public function update(PurchaseOrder $purchaseOrder, array $changes): PurchaseOrder
    {
        try {
            DB::beginTransaction();
            $purchaseOrder->update($changes);
            DB::commit();
            return $purchaseOrder->fresh();
        } catch (Exception $exception) {
            DB::rollBack();
            throw new DBCommitException($exception);
        }
    }

    /**
     * Delete purchase order
     */
    public function delete(PurchaseOrder $purchaseOrder): bool
    {
        try {
            DB::beginTransaction();
            $purchaseOrder->delete();
            DB::commit();
            return true;
        } catch (Exception $exception) {
            DB::rollBack();
            throw new DBCommitException($exception);
        }
    }

    /**
     * Build query with filters
     */
    private function getQuery(array $filters = []): Builder
    {
        $query = PurchaseOrder::query();

        // Filter by supplier
        if (isset($filters['supplier_id']) && $filters['supplier_id']) {
            $query->where('supplier_id', $filters['supplier_id']);
        }

        // Filter by order number
        if (isset($filters['order_number']) && $filters['order_number']) {
            $query->where('order_number', 'LIKE', "%{$filters['order_number']}%");
        }

        // Filter by status
        if (isset($filters['status']) && $filters['status']) {
            $query->where('status', $filters['status']);
        }

        // Filter by date range
        if (isset($filters['order_date_from']) && $filters['order_date_from']) {
            $query->whereDate('order_date', '>=', $filters['order_date_from']);
        }

        if (isset($filters['order_date_to']) && $filters['order_date_to']) {
            $query->whereDate('order_date', '<=', $filters['order_date_to']);
        }

        // Filter by created_by
        if (isset($filters['created_by']) && $filters['created_by']) {
            $query->where('created_by', $filters['created_by']);
        }

        // Keyword search
        if (isset($filters['keyword']) && $filters['keyword']) {
            $keyword = $filters['keyword'];
            $query->where(function ($q) use ($keyword) {
                $q->where('order_number', 'LIKE', "%{$keyword}%");
            });
        }

        return $query;
    }
}
