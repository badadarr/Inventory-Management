<?php

namespace App\Http\Controllers;

use App\Services\SalesPointService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SalesPointController extends Controller
{
    public function __construct(private SalesPointService $salesPointService) {}

    public function index()
    {
        return Inertia::render('SalesPoint/Index', [
            'recap' => $this->salesPointService->getSalesRecap()->toArray()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sales_id' => 'required|exists:sales,id',
            'order_id' => 'required|exists:orders,id',
            'product_type' => 'required|in:box,kertas_nasi_padang',
            'jumlah_cetak' => 'required|integer|min:0',
            'points' => 'required|numeric|min:0'
        ]);

        $this->salesPointService->create($validated);
        return redirect()->back()->with('success', 'Sales point created successfully');
    }
}
