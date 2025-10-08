<?php

namespace App\Services;

use App\Enums\Core\SortOrderEnum;
use App\Exceptions\DBCommitException;
use App\Helpers\ArrayHelper;
use App\Helpers\BaseHelper;
use App\Models\PurchaseOrder;
use App\Repositories\PurchaseOrderRepository;
use App\Repositories\StockMovementRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class PurchaseOrderService
{
    public function __construct(
        private readonly PurchaseOrderRepository $repository,
        private readonly StockMovementRepository $stockMovementRepository
    )
    {
    }

    /**
     * Get all purchase orders
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
            expand: $queryParameters["expand"] ?? ['supplier', 'createdBy'],
            sortBy: $queryParameters["sort_by"] ?? 'id',
            sortOrder: $queryParameters["sort_order"] ?? SortOrderEnum::DESC->value,
        );
    }

    /**
     * Find purchase order by ID
     */
    public function findByIdOrFail(int $id, array $expands = []): ?PurchaseOrder
    {
        $purchaseOrder = $this->repository->find(['id' => $id], $expands);

        if (!$purchaseOrder) {
            throw new \Exception('Purchase order not found');
        }

        return $purchaseOrder;
    }

    /**
     * Create new purchase order
     */
    public function create(array $payload): PurchaseOrder
    {
        // Generate order number if not provided
        if (empty($payload['order_number'])) {
            $payload['order_number'] = $this->generateOrderNumber();
        }

        // Set created_by to current user
        $payload['created_by'] = Auth::id();

        return $this->repository->create($payload);
    }

    /**
     * Update purchase order
     */
    public function update(PurchaseOrder $purchaseOrder, array $changes): PurchaseOrder
    {
        return $this->repository->update($purchaseOrder, $changes);
    }

    /**
     * Receive purchase order and update stock
     */
    public function receive(PurchaseOrder $purchaseOrder, array $items): PurchaseOrder
    {
        try {
            // Update PO status to received
            $purchaseOrder = $this->repository->update($purchaseOrder, [
                'status' => 'received',
                'received_date' => now(),
            ]);

            // Update stock for each item
            foreach ($items as $item) {
                // Update product quantity
                $product = \App\Models\Product::find($item['product_id']);
                if ($product) {
                    $oldQuantity = $product->quantity;
                    $newQuantity = $oldQuantity + $item['quantity'];
                    $product->update(['quantity' => $newQuantity]);

                    // Record stock movement
                    $this->stockMovementRepository->recordStockIn(
                        productId: $product->id,
                        quantity: $item['quantity'],
                        balanceAfter: $newQuantity,
                        referenceType: 'purchase_order',
                        referenceId: $purchaseOrder->id,
                        createdBy: Auth::id(),
                        notes: "Received from PO #{$purchaseOrder->order_number}"
                    );
                }
            }

            return $purchaseOrder;
        } catch (\Exception $exception) {
            throw new DBCommitException($exception);
        }
    }

    /**
     * Delete purchase order
     */
    public function delete(PurchaseOrder $purchaseOrder): bool
    {
        return $this->repository->delete($purchaseOrder);
    }

    /**
     * Generate unique order number
     */
    private function generateOrderNumber(): string
    {
        $prefix = 'PO';
        $date = date('Ymd');
        $lastOrder = PurchaseOrder::whereDate('created_at', today())
            ->orderBy('id', 'DESC')
            ->first();

        $sequence = $lastOrder ? (int)substr($lastOrder->order_number, -4) + 1 : 1;

        return sprintf('%s-%s-%04d', $prefix, $date, $sequence);
    }
}
