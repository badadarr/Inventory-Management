<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductCustomerPrice\ProductCustomerPriceUpsertRequest;
use App\Services\ProductCustomerPriceService;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;

class ProductCustomerPriceController extends Controller
{
    public function __construct(
        private readonly ProductCustomerPriceService $service,
        private readonly ProductService $productService
    )
    {
    }

    /**
     * Get custom prices by product - Inertia page
     */
    public function byProduct(int $productId): Response
    {
        $product = $this->productService->findByIdOrFail($productId);
        $product->load('category'); // Load category relationship
        
        $customPrices = $this->service->getByProduct($productId);
        
        // Get all customers for dropdown
        $customers = \App\Models\Customer::select('id', 'name')
            ->orderBy('name')
            ->get();
        
        return Inertia::render('ProductCustomerPrice/Index', [
            'product' => $product,
            'customPrices' => $customPrices,
            'customers' => $customers
        ]);
    }

    /**
     * Get custom prices by customer
     */
    public function byCustomer(int $customerId): JsonResponse
    {
        return response()->json([
            'data' => $this->service->getByCustomer($customerId)
        ]);
    }

    /**
     * Create or update custom price
     */
    public function upsert(ProductCustomerPriceUpsertRequest $request): JsonResponse
    {
        try {
            $customPrice = $this->service->upsert($request->validated());
            
            return response()->json([
                'success' => true,
                'data' => $customPrice,
                'message' => 'Custom price saved successfully'
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    /**
     * Delete custom price
     */
    public function destroy(int $productId, int $customerId): JsonResponse
    {
        try {
            $customPrice = $this->service->find($productId, $customerId);
            
            if (!$customPrice) {
                return response()->json([
                    'success' => false,
                    'message' => 'Custom price not found'
                ], 404);
            }
            
            $this->service->delete($customPrice);
            
            return response()->json([
                'success' => true,
                'message' => 'Custom price deleted successfully'
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], 500);
        }
    }
}
