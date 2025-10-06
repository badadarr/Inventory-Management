<?php

namespace App\Http\Controllers;

use App\Services\SalesService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SalesController extends Controller
{
    public function __construct(private SalesService $salesService) {}

    public function index()
    {
        $sales = $this->salesService->getAll();
        
        return Inertia::render('Sales/Index', [
            'sales' => $sales ? $sales->toArray() : []
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
            'status' => 'required|in:active,inactive'
        ]);

        $this->salesService->create($validated);
        return redirect()->back()->with('success', 'Sales created successfully');
    }

    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
            'status' => 'required|in:active,inactive'
        ]);

        $this->salesService->update($id, $validated);
        return redirect()->back()->with('success', 'Sales updated successfully');
    }

    public function destroy(int $id)
    {
        $this->salesService->delete($id);
        return redirect()->back()->with('success', 'Sales deleted successfully');
    }
}
