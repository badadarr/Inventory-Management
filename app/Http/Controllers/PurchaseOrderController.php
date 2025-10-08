<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseOrder\PurchaseOrderCreateRequest;
use App\Http\Requests\PurchaseOrder\PurchaseOrderIndexRequest;
use App\Http\Requests\PurchaseOrder\PurchaseOrderUpdateRequest;
use App\Services\PurchaseOrderService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PurchaseOrderController extends Controller
{
    public function __construct(
        private readonly PurchaseOrderService $service
    )
    {
    }

    /**
     * Display listing of purchase orders
     */
    public function index(PurchaseOrderIndexRequest $request): Response
    {
        return Inertia::render(
            component: 'PurchaseOrder/Index',
            props: [
                'purchaseOrders' => $this->service->getAll($request->validated()),
                'filters' => [
                    // Add filter definitions here
                ],
            ]
        );
    }

    /**
     * Show create form
     */
    public function create(): Response
    {
        return Inertia::render('PurchaseOrder/Create');
    }

    /**
     * Store new purchase order
     */
    public function store(PurchaseOrderCreateRequest $request): RedirectResponse
    {
        try {
            $this->service->create($request->validated());
            return redirect()
                ->route('purchase-orders.index')
                ->with('success', 'Purchase order created successfully');
        } catch (\Exception $exception) {
            return redirect()
                ->back()
                ->with('error', $exception->getMessage());
        }
    }

    /**
     * Show edit form
     */
    public function edit(int $id): Response
    {
        return Inertia::render('PurchaseOrder/Edit', [
            'purchaseOrder' => $this->service->findByIdOrFail($id, ['supplier', 'createdBy']),
        ]);
    }

    /**
     * Update purchase order
     */
    public function update(int $id, PurchaseOrderUpdateRequest $request): RedirectResponse
    {
        try {
            $purchaseOrder = $this->service->findByIdOrFail($id);
            $this->service->update($purchaseOrder, $request->validated());
            
            return redirect()
                ->route('purchase-orders.index')
                ->with('success', 'Purchase order updated successfully');
        } catch (\Exception $exception) {
            return redirect()
                ->back()
                ->with('error', $exception->getMessage());
        }
    }

    /**
     * Delete purchase order
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            $purchaseOrder = $this->service->findByIdOrFail($id);
            $this->service->delete($purchaseOrder);
            
            return redirect()
                ->route('purchase-orders.index')
                ->with('success', 'Purchase order deleted successfully');
        } catch (\Exception $exception) {
            return redirect()
                ->back()
                ->with('error', $exception->getMessage());
        }
    }

    /**
     * Receive purchase order
     */
    public function receive(int $id): RedirectResponse
    {
        try {
            $purchaseOrder = $this->service->findByIdOrFail($id);
            
            // Items should be passed from request or stored separately
            $items = request()->input('items', []);
            
            $this->service->receive($purchaseOrder, $items);
            
            return redirect()
                ->route('purchase-orders.index')
                ->with('success', 'Purchase order received successfully');
        } catch (\Exception $exception) {
            return redirect()
                ->back()
                ->with('error', $exception->getMessage());
        }
    }
}
