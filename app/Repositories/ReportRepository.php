<?php

namespace App\Repositories;

use App\Models\Order;
use Illuminate\Support\Facades\DB;

class ReportRepository
{
    public function getOutstandingReport(array $filters = [])
    {
        $query = Order::with(['customer:id,name', 'orderItems'])
            ->where('due', '>', 0);

        if (!empty($filters['start_date'])) {
            $query->whereDate('tanggal_po', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->whereDate('tanggal_po', '<=', $filters['end_date']);
        }

        return $query->orderBy('tanggal_po', 'desc')->paginate(15);
    }

    public function getTopCustomers(array $filters = [])
    {
        $query = Order::select(
            'customer_id',
            DB::raw('SUM(jumlah_cetak) as total_lembar'),
            DB::raw('SUM(total) as total_penjualan')
        )
        ->with('customer:id,name')
        ->groupBy('customer_id');

        if (!empty($filters['start_date'])) {
            $query->whereDate('created_at', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->whereDate('created_at', '<=', $filters['end_date']);
        }

        $perPage = $filters['limit'] ?? 10;
        return $query->orderBy('total_penjualan', 'desc')
            ->paginate($perPage);
    }
}
