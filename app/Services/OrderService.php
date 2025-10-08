<?php

namespace App\Services;

use App\Enums\Cart\CartExpandEnum;
use App\Enums\Cart\CartFiltersEnum;
use App\Enums\Core\SortOrderEnum;
use App\Enums\Order\OrderExpandEnum;
use App\Enums\Order\OrderFieldsEnum;
use App\Enums\Order\OrderFiltersEnum;
use App\Enums\Order\OrderStatusEnum;
use App\Enums\OrderItem\OrderItemFieldsEnum;
use App\Enums\Product\ProductFieldsEnum;
use App\Enums\Product\ProductStatusEnum;
use App\Enums\Transaction\TransactionFieldsEnum;
use App\Exceptions\DBCommitException;
use App\Exceptions\OrderCreateException;
use App\Exceptions\OrderNotFoundException;
use App\Exceptions\OrderSettleException;
use App\Exceptions\ProductNotFoundException;
use App\Helpers\ArrayHelper;
use App\Helpers\BaseHelper;
use App\Models\Order;
use App\Repositories\OrderRepository;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(
        private readonly OrderRepository    $repository,
        private readonly OrderItemService   $orderItemService,
        private readonly CartService        $cartService,
        private readonly ProductService     $productService,
        private readonly TransactionService $transactionService,
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
            filters: ArrayHelper::getFiltersValues($queryParameters, OrderFiltersEnum::values()),
            fields: $queryParameters["fields"] ?? [],
            expand: $queryParameters["expand"] ?? [],
            sortBy: $queryParameters["sort_by"] ?? OrderFieldsEnum::CREATED_AT->value,
            sortOrder: $queryParameters["sort_order"] ?? SortOrderEnum::DESC->value,
        );
    }

    /**
     * @param int $id
     * @param array $expands
     * @return Order|null
     * @throws OrderNotFoundException
     */
    public function findByIdOrFail(int $id, array $expands = []): ?Order
    {
        $order = $this->repository->find([
            OrderFiltersEnum::ID->value => $id
        ], $expands);

        if (!$order) {
            throw new OrderNotFoundException('Order not found by the given id.');
        }

        return $order;
    }

    /**
     * Create order directly from order items (not from cart)
     * Used by Order/Create.vue form
     * 
     * @param array $payload
     * @param int $userId
     * @return Order
     * @throws DBCommitException
     * @throws OrderCreateException
     * @throws ProductNotFoundException
     */
    public function createDirect(array $payload, int $userId): Order
    {
        try {
            DB::beginTransaction();
            
            // Get order items from payload
            $orderItems = $payload['order_items'] ?? [];
            
            if (empty($orderItems)) {
                throw new OrderCreateException("Order items cannot be empty.");
            }

            // Calculate sub_total and prepare order items
            $cartSubtotal = 0;
            $processOrderItemPayloads = [];
            
            foreach ($orderItems as $item) {
                // Get product
                $product = $this->productService->findByIdOrFail($item['product_id'], []);
                
                // Check product is active
                if ($product->status == ProductStatusEnum::INACTIVE->value) {
                    throw new OrderCreateException("Product id {$product->id} is not active now.");
                }

                // Check product quantity is available
                if ($item['quantity'] > $product->quantity) {
                    throw new OrderCreateException("Product quantity not available for {$product->name}. Available: {$product->quantity}, Requested: {$item['quantity']}");
                }

                $itemPrice = $item['price'] ?? $product->selling_price;
                $itemSubtotal = $itemPrice * $item['quantity'];
                $cartSubtotal += $itemSubtotal;

                $processOrderItemPayloads[] = [
                    OrderItemFieldsEnum::PRODUCT_ID->value => $product->id,
                    OrderItemFieldsEnum::QUANTITY->value   => $item['quantity'],
                    'unit_price'                           => $itemPrice,
                    'subtotal'                             => $itemSubtotal,
                ];

                // Decrement product quantity
                $this->productService->update(
                    id: $product->id,
                    payload: [
                        ProductFieldsEnum::QUANTITY->value => $product->quantity - $item['quantity'],
                    ]
                );
            }

            // Calculate tax_total
            $taxData = BaseHelper::calculateTax(amount: $cartSubtotal);
            $taxTotal = $taxData["totalTax"];

            // Calculate discount_total with custom discount
            $discountDefaultData = BaseHelper::calculateDefaultDiscount(amount: $cartSubtotal);
            $customDiscount = $payload['custom_discount'] ?? [];
            $discountTotal = $discountDefaultData['totalDiscount'];
            if ($customDiscount) {
                $customDiscountData = BaseHelper::calculateCustomDiscount(
                    amount: $cartSubtotal,
                    discount: $customDiscount['discount'],
                    discountType: $customDiscount['discount_type']
                );
                $discountTotal = $customDiscountData['totalDiscount'] + $discountTotal;
            }

            // Calculate total
            $total = $cartSubtotal - $discountTotal;
            $totalWithTax = $total + $taxTotal;

            // Calculate due
            $paid = $payload[OrderFieldsEnum::PAID->value] ?? 0;
            $due = $total - $paid;
            $dueWithTax = $totalWithTax - $paid;

            // Decide status
            $status = $this->decideStatus(
                paid: $paid,
                total: $totalWithTax
            );

            $processPayload = [
                OrderFieldsEnum::CUSTOMER_ID->value    => $payload[OrderFieldsEnum::CUSTOMER_ID->value] ?? null,
                OrderFieldsEnum::SALES_ID->value       => $payload[OrderFieldsEnum::SALES_ID->value] ?? null,
                OrderFieldsEnum::ORDER_NUMBER->value   => 'O-' . Str::random(5),
                OrderFieldsEnum::SUB_TOTAL->value      => $cartSubtotal,
                OrderFieldsEnum::TAX_TOTAL->value      => $taxTotal,
                OrderFieldsEnum::DISCOUNT_TOTAL->value => $discountTotal,
                OrderFieldsEnum::TOTAL->value          => $totalWithTax,
                OrderFieldsEnum::PAID->value           => $paid,
                OrderFieldsEnum::DUE->value            => max($dueWithTax, 0),
                OrderFieldsEnum::STATUS->value         => $status,
                OrderFieldsEnum::TANGGAL_PO->value     => $payload[OrderFieldsEnum::TANGGAL_PO->value] ?? null,
                OrderFieldsEnum::TANGGAL_KIRIM->value  => $payload[OrderFieldsEnum::TANGGAL_KIRIM->value] ?? null,
                OrderFieldsEnum::JENIS_BAHAN->value    => $payload[OrderFieldsEnum::JENIS_BAHAN->value] ?? null,
                OrderFieldsEnum::GRAMASI->value        => $payload[OrderFieldsEnum::GRAMASI->value] ?? null,
                OrderFieldsEnum::VOLUME->value         => $payload[OrderFieldsEnum::VOLUME->value] ?? null,
                OrderFieldsEnum::HARGA_JUAL_PCS->value => $payload[OrderFieldsEnum::HARGA_JUAL_PCS->value] ?? null,
                OrderFieldsEnum::JUMLAH_CETAK->value   => $payload[OrderFieldsEnum::JUMLAH_CETAK->value] ?? null,
                OrderFieldsEnum::CATATAN->value        => $payload[OrderFieldsEnum::CATATAN->value] ?? null,
                OrderFieldsEnum::CREATED_BY->value     => $userId,
            ];

            // Create order
            $order = $this->repository->create($processPayload);

            // Insert order items
            $this->orderItemService->insert(
                payloads: $processOrderItemPayloads,
                orderId: $order->id
            );

            // Create transaction if paid
            if ($paid > 0) {
                $this->transactionService->create([
                    TransactionFieldsEnum::ORDER_ID->value     => $order->id,
                    TransactionFieldsEnum::AMOUNT->value       => $paid,
                    TransactionFieldsEnum::PAID_THROUGH->value => $payload['paid_through'] ?? 'cash',
                ]);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $order;
    }

    /**
     * Update existing order
     * 
     * @param int $id
     * @param array $payload
     * @param int $userId
     * @return Order
     * @throws DBCommitException
     * @throws OrderNotFoundException
     * @throws ProductNotFoundException
     */
    public function update(int $id, array $payload, int $userId): Order
    {
        try {
            DB::beginTransaction();
            
            // Find existing order
            $order = $this->findByIdOrFail($id, [OrderExpandEnum::ORDER_ITEMS->value]);
            
            // Get order items from payload
            $orderItems = $payload['order_items'] ?? [];
            
            if (empty($orderItems)) {
                throw new OrderCreateException("Order items cannot be empty.");
            }

            // First, restore stock from old order items
            foreach ($order->orderItems as $oldItem) {
                $oldProduct = $this->productService->findByIdOrFail($oldItem->product_id, []);
                $this->productService->update(
                    id: $oldProduct->id,
                    payload: [
                        ProductFieldsEnum::QUANTITY->value => $oldProduct->quantity + $oldItem->quantity,
                    ]
                );
            }

            // Delete old order items
            $order->orderItems()->delete();

            // Calculate sub_total and prepare new order items
            $cartSubtotal = 0;
            $processOrderItemPayloads = [];
            
            foreach ($orderItems as $item) {
                // Get product
                $product = $this->productService->findByIdOrFail($item['product_id'], []);
                
                // Check product is active
                if ($product->status == ProductStatusEnum::INACTIVE->value) {
                    throw new OrderCreateException("Product id {$product->id} is not active now.");
                }

                // Check product quantity is available
                if ($item['quantity'] > $product->quantity) {
                    throw new OrderCreateException("Product quantity not available for {$product->name}. Available: {$product->quantity}, Requested: {$item['quantity']}");
                }

                $itemPrice = $item['price'] ?? $product->selling_price;
                $itemSubtotal = $itemPrice * $item['quantity'];
                $cartSubtotal += $itemSubtotal;

                $processOrderItemPayloads[] = [
                    OrderItemFieldsEnum::PRODUCT_ID->value => $product->id,
                    OrderItemFieldsEnum::QUANTITY->value   => $item['quantity'],
                    'unit_price'                           => $itemPrice,
                    'subtotal'                             => $itemSubtotal,
                ];

                // Decrement product quantity
                $this->productService->update(
                    id: $product->id,
                    payload: [
                        ProductFieldsEnum::QUANTITY->value => $product->quantity - $item['quantity'],
                    ]
                );
            }

            // Calculate tax_total
            $taxData = BaseHelper::calculateTax(amount: $cartSubtotal);
            $taxTotal = $taxData["totalTax"];

            // Calculate discount_total with custom discount
            $discountDefaultData = BaseHelper::calculateDefaultDiscount(amount: $cartSubtotal);
            $customDiscount = $payload['custom_discount'] ?? [];
            $discountTotal = $discountDefaultData['totalDiscount'];
            if ($customDiscount) {
                $customDiscountData = BaseHelper::calculateCustomDiscount(
                    amount: $cartSubtotal,
                    discount: $customDiscount['discount'],
                    discountType: $customDiscount['discount_type']
                );
                $discountTotal = $customDiscountData['totalDiscount'] + $discountTotal;
            }

            // Calculate total
            $total = $cartSubtotal - $discountTotal;
            $totalWithTax = $total + $taxTotal;

            // Calculate due
            $paid = $payload[OrderFieldsEnum::PAID->value] ?? 0;
            $due = $total - $paid;
            $dueWithTax = $totalWithTax - $paid;

            // Decide status
            $status = $this->decideStatus(
                paid: $paid,
                total: $totalWithTax
            );

            // Update order
            $updatePayload = [
                OrderFieldsEnum::CUSTOMER_ID->value    => $payload[OrderFieldsEnum::CUSTOMER_ID->value] ?? null,
                OrderFieldsEnum::SALES_ID->value       => $payload[OrderFieldsEnum::SALES_ID->value] ?? null,
                OrderFieldsEnum::SUB_TOTAL->value      => $cartSubtotal,
                OrderFieldsEnum::TAX_TOTAL->value      => $taxTotal,
                OrderFieldsEnum::DISCOUNT_TOTAL->value => $discountTotal,
                OrderFieldsEnum::TOTAL->value          => $totalWithTax,
                OrderFieldsEnum::PAID->value           => $paid,
                OrderFieldsEnum::DUE->value            => max($dueWithTax, 0),
                OrderFieldsEnum::STATUS->value         => $status,
                OrderFieldsEnum::TANGGAL_PO->value     => $payload[OrderFieldsEnum::TANGGAL_PO->value] ?? null,
                OrderFieldsEnum::TANGGAL_KIRIM->value  => $payload[OrderFieldsEnum::TANGGAL_KIRIM->value] ?? null,
                OrderFieldsEnum::JENIS_BAHAN->value    => $payload[OrderFieldsEnum::JENIS_BAHAN->value] ?? null,
                OrderFieldsEnum::GRAMASI->value        => $payload[OrderFieldsEnum::GRAMASI->value] ?? null,
                OrderFieldsEnum::VOLUME->value         => $payload[OrderFieldsEnum::VOLUME->value] ?? null,
                OrderFieldsEnum::HARGA_JUAL_PCS->value => $payload[OrderFieldsEnum::HARGA_JUAL_PCS->value] ?? null,
                OrderFieldsEnum::JUMLAH_CETAK->value   => $payload[OrderFieldsEnum::JUMLAH_CETAK->value] ?? null,
                OrderFieldsEnum::CATATAN->value        => $payload[OrderFieldsEnum::CATATAN->value] ?? null,
            ];

            $updatedOrder = $this->repository->update($order, $updatePayload);

            // Insert new order items
            $this->orderItemService->insert(
                payloads: $processOrderItemPayloads,
                orderId: $updatedOrder->id
            );

            // Reload order with transaction
            $order = $this->findByIdOrFail($id, ['transaction']);
            
            // Update transaction if exists, or create new one
            $existingTransaction = $order->transaction;
            if ($paid > 0) {
                if ($existingTransaction) {
                    // Update existing transaction
                    $existingTransaction->update([
                        TransactionFieldsEnum::AMOUNT->value       => $paid,
                        TransactionFieldsEnum::PAID_THROUGH->value => $payload['paid_through'] ?? 'cash',
                    ]);
                } else {
                    // Create new transaction
                    $this->transactionService->create([
                        TransactionFieldsEnum::ORDER_ID->value     => $order->id,
                        TransactionFieldsEnum::AMOUNT->value       => $paid,
                        TransactionFieldsEnum::PAID_THROUGH->value => $payload['paid_through'] ?? 'cash',
                    ]);
                }
            } elseif ($existingTransaction && $paid == 0) {
                // If paid is now 0, delete transaction
                $existingTransaction->delete();
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $order;
    }

    /**
     * Create order from cart items (POS system)
     * 
     * @param array $payload
     * @param int $userId
     * @return Order
     * @throws DBCommitException
     * @throws OrderCreateException
     * @throws ProductNotFoundException
     */
    public function createForUser(array $payload, int $userId): Order
    {
        try {
            DB::beginTransaction();
            // Get carts for user
            $carts = $this->cartService->getAll([
                CartFiltersEnum::USER_ID->value => $userId,
                "expand"                        => [CartExpandEnum::PRODUCT->value],
                "per_page"                      => 500,
            ]);

            // Calculate sub_total
            $cartSubtotal = 0;
            $productBuyingSubtotal = 0;
            $processOrderItemPayloads = [];
            foreach ($carts as $cart) {
                // Check cart product is active
                if ($cart->product->status == ProductStatusEnum::INACTIVE->value) {
                    throw new OrderCreateException("Product id {$cart->product_id} is not active now.");
                }

                // Check product quantity is available
                if ($cart->quantity > $cart->product->quantity) {
                    throw new OrderCreateException("Product quantity not available for id {$cart->product_id}.");
                }

                $itemPrice = $cart->product->selling_price;
                $itemSubtotal = $itemPrice * $cart->quantity;
                $cartSubtotal += $itemSubtotal;
                $productBuyingSubtotal += $cart->product->buying_price * $cart->quantity;

                $processOrderItemPayloads[] = [
                    OrderItemFieldsEnum::PRODUCT_ID->value => $cart->product->id,
                    OrderItemFieldsEnum::QUANTITY->value   => $cart->quantity,
                    'unit_price'                           => $itemPrice,
                    'subtotal'                             => $itemSubtotal,
                ];

                // Decrement product quantity
                $this->productService->update(
                    id: $cart->product_id,
                    payload: [
                        ProductFieldsEnum::QUANTITY->value => $cart->product->quantity - $cart->quantity,
                    ]
                );

                // Delete cart
                $cart->delete();
            }

            // Calculate tax_total
            $taxData = BaseHelper::calculateTax(amount: $cartSubtotal);
            $taxTotal = $taxData["totalTax"];

            // Calculate discount_total with custom discount
            $discountDefaultData = BaseHelper::calculateDefaultDiscount(amount: $cartSubtotal);
            $customDiscount = $payload['custom_discount'] ?? [];
            $discountTotal = $discountDefaultData['totalDiscount'];
            if ($customDiscount) {
                $customDiscountData = BaseHelper::calculateCustomDiscount(
                    amount: $cartSubtotal,
                    discount: $customDiscount['discount'],
                    discountType: $customDiscount['discount_type']
                );
                $discountTotal = $customDiscountData['totalDiscount'] + $discountTotal;
            }

            // Calculate total
            $total = $cartSubtotal - $discountTotal;
            $totalWithTax = $total + $taxTotal;

            // Calculate due
            $paid = $payload[OrderFieldsEnum::PAID->value] ?? 0;
            $due = $total - $paid;
            $dueWithTax = $totalWithTax - $paid;

            // Decide status
            $status = $this->decideStatus(
                paid: $paid,
                total: $totalWithTax
            );

            $processPayload = [
                OrderFieldsEnum::CUSTOMER_ID->value    => $payload[OrderFieldsEnum::CUSTOMER_ID->value] ?? null,
                OrderFieldsEnum::SALES_ID->value       => $payload[OrderFieldsEnum::SALES_ID->value] ?? null,
                OrderFieldsEnum::ORDER_NUMBER->value   => 'O-' . Str::random(5),
                OrderFieldsEnum::SUB_TOTAL->value      => $cartSubtotal,
                OrderFieldsEnum::TAX_TOTAL->value      => $taxTotal,
                OrderFieldsEnum::DISCOUNT_TOTAL->value => $discountTotal,
                OrderFieldsEnum::TOTAL->value          => $totalWithTax,
                OrderFieldsEnum::PAID->value           => $paid,
                OrderFieldsEnum::DUE->value            => max($dueWithTax, 0),
                OrderFieldsEnum::STATUS->value         => $status,
                OrderFieldsEnum::TANGGAL_PO->value     => $payload[OrderFieldsEnum::TANGGAL_PO->value] ?? null,
                OrderFieldsEnum::TANGGAL_KIRIM->value  => $payload[OrderFieldsEnum::TANGGAL_KIRIM->value] ?? null,
                OrderFieldsEnum::JENIS_BAHAN->value    => $payload[OrderFieldsEnum::JENIS_BAHAN->value] ?? null,
                OrderFieldsEnum::GRAMASI->value        => $payload[OrderFieldsEnum::GRAMASI->value] ?? null,
                OrderFieldsEnum::VOLUME->value         => $payload[OrderFieldsEnum::VOLUME->value] ?? null,
                OrderFieldsEnum::HARGA_JUAL_PCS->value => $payload[OrderFieldsEnum::HARGA_JUAL_PCS->value] ?? null,
                OrderFieldsEnum::JUMLAH_CETAK->value   => $payload[OrderFieldsEnum::JUMLAH_CETAK->value] ?? null,
                OrderFieldsEnum::CATATAN->value        => $payload[OrderFieldsEnum::CATATAN->value] ?? null,
                OrderFieldsEnum::CREATED_BY->value     => $userId,
            ];

            // Create order
            $order = $this->repository->create($processPayload);

            // Insert order items
            $this->orderItemService->insert(
                payloads: $processOrderItemPayloads,
                orderId: $order->id
            );

            // Create transaction
            $this->transactionService->create([
                TransactionFieldsEnum::ORDER_ID->value     => $order->id,
                TransactionFieldsEnum::AMOUNT->value       => $paid,
                TransactionFieldsEnum::PAID_THROUGH->value => $payload[TransactionFieldsEnum::PAID_THROUGH->value],
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $order;
    }

    /**
     * @param int $id
     * @return Order
     * @throws OrderNotFoundException
     * @throws OrderSettleException
     * @throws Exception
     */
    public function settle(int $id): Order
    {
        $order = $this->findByIdOrFail(id: $id, expands: [OrderExpandEnum::ORDER_ITEMS->value]);

        if ($order->due <= 0) {
            throw new OrderSettleException("No due amount left to settle.");
        }

        // +discount
        $discountTotal = $order->discount_total + $order->due;

        // Calculate total
        $total = $order->sub_total - $discountTotal;
        $totalWithTax = $total + $order->tax_total;

        $processPayload = [
            OrderFieldsEnum::DISCOUNT_TOTAL->value => $discountTotal,
            OrderFieldsEnum::TOTAL->value          => $totalWithTax,
            OrderFieldsEnum::DUE->value            => 0,
            OrderFieldsEnum::STATUS->value         => OrderStatusEnum::COMPLETED->value,
        ];

        return $this->repository->update($order, $processPayload);
    }

    /**
     * @param int $id
     * @param array $payload
     * @return Order
     * @throws DBCommitException
     * @throws OrderNotFoundException
     */
    public function pay(int $id, array $payload): Order
    {
        $order = $this->findByIdOrFail(id: $id, expands: [OrderExpandEnum::ORDER_ITEMS->value]);

        $paid = $order->paid + $payload[TransactionFieldsEnum::AMOUNT->value];
        $due = max($order->total - $paid, 0);

        // Decide status - if fully paid, mark as completed
        $status = ($due <= 0) ? OrderStatusEnum::COMPLETED->value : OrderStatusEnum::PENDING->value;

        $processPayload = [
            OrderFieldsEnum::PAID->value   => $paid,
            OrderFieldsEnum::DUE->value    => $due,
            OrderFieldsEnum::STATUS->value => $status,
        ];

        try {
            DB::beginTransaction();

            // Update order
            $order = $this->repository->update($order, $processPayload);

            // Create transaction
            $this->transactionService->create([
                TransactionFieldsEnum::ORDER_ID->value     => $order->id,
                TransactionFieldsEnum::AMOUNT->value       => $payload[TransactionFieldsEnum::AMOUNT->value],
                TransactionFieldsEnum::PAID_THROUGH->value => $payload[TransactionFieldsEnum::PAID_THROUGH->value],
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $order;
    }

    /**
     * @param int $id
     * @return bool|null
     * @throws OrderNotFoundException
     */
    public function delete(int $id): ?bool
    {
        $order = $this->findByIdOrFail($id);
        return $this->repository->delete($order);
    }

    /**
     * @param int|float $paid
     * @param int|float $total
     * @return string
     */
    private function decideStatus(int|float $paid, int|float $total): string
    {
        // Simple status logic: if fully paid -> completed, otherwise -> pending
        if ($paid >= $total) {
            return OrderStatusEnum::COMPLETED->value;
        }
        return OrderStatusEnum::PENDING->value;
    }
}
