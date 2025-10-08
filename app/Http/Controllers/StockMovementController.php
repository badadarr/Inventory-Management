<?php

namespace App\Http\Controllers;

use App\Services\StockMovementService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StockMovementController extends Controller
{
    public function __construct(
        private readonly StockMovementService $service
    )
    {
    }

    /**
     * Display listing of stock movements
     */
    public function index(Request $request): Response
    {
        return Inertia::render(
            component: 'StockMovement/Index',
            props: [
                'movements' => $this->service->getAll($request->all()),
                'filters' => [
                    // Add filter definitions here
                ],
            ]
        );
    }

    /**
     * Get stock movements by product
     */
    public function byProduct(int $productId)
    {
        return response()->json([
            'data' => $this->service->getByProduct($productId)
        ]);
    }
}
