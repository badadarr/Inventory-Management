<?php

namespace App\Repositories;

use App\Models\SalesPoint;
use Illuminate\Database\Eloquent\Collection;

class SalesPointRepository
{
    public function getBySalesId(int $salesId): Collection
    {
        return SalesPoint::with(['order.customer'])
            ->where('sales_id', $salesId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function create(array $data): SalesPoint
    {
        return SalesPoint::create($data);
    }

    public function getSalesRecap()
    {
        return SalesPoint::selectRaw('sales_id, product_type, SUM(jumlah_cetak) as total_cetak, SUM(points) as total_points')
            ->with('sales:id,name')
            ->groupBy('sales_id', 'product_type')
            ->paginate(15);
    }
}
