<?php

namespace App\Services;

use App\Enums\Expense\ExpenseFieldsEnum;
use App\Enums\Order\OrderFieldsEnum;
use App\Enums\Order\OrderStatusEnum;
use App\Helpers\BaseHelper;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;

class DashboardService
{
    public function getData(?string $date = null): array
    {
        $date = $date ? Carbon::parse($date) : Carbon::now();

        // Get total order count, total profit, and total loss for the current month
        $selectedMonthOrders = Order::query()
            ->when($date, function ($query, $date) {
                $query->whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year);
            })
            ->get();
        $selectedMonthTotalOrders = $selectedMonthOrders->count();
        // Note: Using 'total' for revenue (profit/loss removed in v2)
        $selectedMonthTotalRevenue = $selectedMonthOrders->sum(OrderFieldsEnum::TOTAL->value);

        $lastMonthOrders = Order::query()
            ->when($date, function ($query, $date) {
                $query->whereMonth('created_at', $date->subMonth()->month)
                    ->whereYear('created_at', $date->subMonth()->year);
            })
            ->get();
        $lastMonthTotalOrders = $lastMonthOrders->count();
        $lastMonthTotalRevenue = $lastMonthOrders->sum(OrderFieldsEnum::TOTAL->value);

        // Calculate percentage change for total orders and revenue
        $orderPercentageChange = ($lastMonthTotalOrders != 0) ? (($selectedMonthTotalOrders - $lastMonthTotalOrders) / $lastMonthTotalOrders) * 100 : 0;
        $revenuePercentageChange = ($lastMonthTotalRevenue != 0) ? (($selectedMonthTotalRevenue - $lastMonthTotalRevenue) / $lastMonthTotalRevenue) * 100 : 0;

        $selectedMonthTotalExpenses = Expense::query()
            ->when($date, function ($query, $date) {
                $query->whereMonth(ExpenseFieldsEnum::EXPENSE_DATE->value, $date->month)
                    ->whereYear(ExpenseFieldsEnum::EXPENSE_DATE->value, $date->year);
            })
            ->sum(ExpenseFieldsEnum::AMOUNT->value);
        $lastMonthTotalExpenses = Expense::query()
            ->when($date, function ($query, $date) {
                $query->whereMonth(ExpenseFieldsEnum::EXPENSE_DATE->value, $date->subMonth()->month)
                    ->whereYear(ExpenseFieldsEnum::EXPENSE_DATE->value, $date->subMonth()->year);
            })
            ->sum(ExpenseFieldsEnum::AMOUNT->value);
        $expensePercentageChange = ($lastMonthTotalExpenses != 0) ? (($selectedMonthTotalExpenses - $lastMonthTotalExpenses) / $lastMonthTotalExpenses) * 100 : 0;

        // Calculate Net Profit (Revenue - Expenses)
        $selectedMonthNetProfit = $selectedMonthTotalRevenue - $selectedMonthTotalExpenses;
        $lastMonthNetProfit = $lastMonthTotalRevenue - $lastMonthTotalExpenses;
        $netProfitPercentageChange = ($lastMonthNetProfit != 0) ? (($selectedMonthNetProfit - $lastMonthNetProfit) / abs($lastMonthNetProfit)) * 100 : 0;

        // Get total customers count
        $totalCustomers = Customer::count();
        $lastMonthCustomers = Customer::whereMonth('created_at', '<', $date->month)
            ->whereYear('created_at', '<=', $date->year)
            ->count();
        $customersPercentageChange = ($lastMonthCustomers != 0) ? (($totalCustomers - $lastMonthCustomers) / $lastMonthCustomers) * 100 : 0;

        // Get total products count
        $totalProducts = Product::count();
        $newProductsThisMonth = Product::whereMonth('created_at', $date->month)
            ->whereYear('created_at', $date->year)
            ->count();

        // Get low stock count (products where quantity <= reorder_level)
        $lowStockCount = Product::whereRaw('quantity <= reorder_level')->count();

        // Get completion rate (completed orders / total orders * 100)
        $completedOrders = $selectedMonthOrders->where('status', OrderStatusEnum::COMPLETED->value)->count();
        $completionRate = ($selectedMonthTotalOrders > 0) ? ($completedOrders / $selectedMonthTotalOrders) * 100 : 0;
        $lastMonthCompletedOrders = $lastMonthOrders->where('status', OrderStatusEnum::COMPLETED->value)->count();
        $lastMonthCompletionRate = ($lastMonthTotalOrders > 0) ? ($lastMonthCompletedOrders / $lastMonthTotalOrders) * 100 : 0;
        $completionRateChange = $lastMonthCompletionRate != 0 ? ($completionRate - $lastMonthCompletionRate) : 0;

        return [
            "total_orders"      => [
                "selected"          => $selectedMonthTotalOrders,
                "percentage_change" => abs(BaseHelper::numberFormat($orderPercentageChange)),
                "stateArray"        => $orderPercentageChange < 0 ? "down" : "up"
            ],
            "total_revenue"      => [
                "selected"          => (double) $selectedMonthTotalRevenue,
                "percentage_change" => abs(BaseHelper::numberFormat($revenuePercentageChange)),
                "stateArray"        => $revenuePercentageChange < 0 ? "down" : "up"
            ],
            "total_expense"     => [
                "selected"          => (double) $selectedMonthTotalExpenses,
                "percentage_change" => abs(BaseHelper::numberFormat($expensePercentageChange)),
                "stateArray"        => $expensePercentageChange < 0 ? "down" : "up"
            ],
            "total_customers"   => [
                "selected"          => $totalCustomers,
                "percentage_change" => abs(BaseHelper::numberFormat($customersPercentageChange)),
                "stateArray"        => $customersPercentageChange < 0 ? "down" : "up"
            ],
            "net_profit"        => [
                "selected"          => (double) $selectedMonthNetProfit,
                "percentage_change" => abs(BaseHelper::numberFormat($netProfitPercentageChange)),
                "stateArray"        => $netProfitPercentageChange < 0 ? "down" : "up"
            ],
            "total_products"    => [
                "selected"          => $totalProducts,
                "new_this_month"    => $newProductsThisMonth
            ],
            "low_stock_count"   => [
                "selected"          => $lowStockCount
            ],
            "completion_rate"   => [
                "selected"          => round($completionRate, 1),
                "percentage_change" => abs(round($completionRateChange, 1)),
                "stateArray"        => $completionRateChange < 0 ? "down" : "up"
            ],
            "revenue_expenses_chart" => $this->prepareRevenueExpensesChart(),
            "order_status_chart" => $this->prepareOrderStatusChart(),
            "top_customers_chart" => $this->prepareTopCustomersChart(),
        ];
    }

    private function prepareProfitLineChart(): array
    {
        // Note: Using 'total' instead of 'profit' (removed in v2)
        $currentYearProfit = Order::selectRaw('EXTRACT(MONTH FROM created_at) as month, SUM(total) as total_profit')
            ->whereYear('created_at', Carbon::now()->year)
            ->where('created_at', '>=', Carbon::now()->subMonths(7))
            ->groupBy('month')
            ->pluck('total_profit', 'month');

        $lastYearProfit = Order::selectRaw('EXTRACT(MONTH FROM created_at) as month, SUM(total) as total_profit')
            ->whereYear('created_at', Carbon::now()->subYear()->year)
            ->where('created_at', '>=', Carbon::now()->subYear()->subMonths(7))
            ->groupBy('month')
            ->pluck('total_profit', 'month');

        // Loop to get the last 7 months
        $months = [];
        $currentYearProfitValues = [];
        $lastYearProfitValues = [];
        for ($i = 6; $i >= 0; $i--) {
            $carbon = Carbon::now()->subMonths($i);
            $months[] = $carbon->format('F');
            $currentYearProfitValues[] = (double) ($currentYearProfit[$carbon->month] ?? 0);
            $lastYearProfitValues[] = (double) ($lastYearProfit[$carbon->month] ?? 0);
        }

        return [
            "months"       => $months,
            "current_year" => $currentYearProfitValues,
            "last_year"    => $lastYearProfitValues,
        ];
    }

    private function prepareOrderBarChart(): array
    {
        $currentYearOrders = Order::selectRaw('EXTRACT(MONTH FROM created_at) as month, COUNT(*) as total_orders')
            ->whereYear('created_at', Carbon::now()->year)
            ->where('created_at', '>=', Carbon::now()->subMonths(7))
            ->groupBy('month')
            ->pluck('total_orders', 'month');

        $lastYearOrders = Order::selectRaw('EXTRACT(MONTH FROM created_at) as month, COUNT(*) as total_orders')
            ->whereYear('created_at', Carbon::now()->subYear()->year)
            ->where('created_at', '>=', Carbon::now()->subYear()->subMonths(7))
            ->groupBy('month')
            ->pluck('total_orders', 'month');

        // Loop to get the last 7 months
        $months = [];
        $currentYearOrdersValues = [];
        $lastYearOrdersValues = [];
        for ($i = 6; $i >= 0; $i--) {
            $carbon = Carbon::now()->subMonths($i);
            $months[] = $carbon->format('F');
            $currentYearOrdersValues[] = (double) ($currentYearOrders[$carbon->month] ?? 0);
            $lastYearOrdersValues[] = (double) ($lastYearOrders[$carbon->month] ?? 0);
        }

        return [
            "months"       => $months,
            "current_year" => $currentYearOrdersValues,
            "last_year"    => $lastYearOrdersValues,
        ];
    }

    private function prepareRevenueExpensesChart(): array
    {
        // Get revenue data (orders total) for last 7 months
        $revenueData = Order::selectRaw('EXTRACT(MONTH FROM created_at) as month, SUM(total) as total_revenue')
            ->whereYear('created_at', Carbon::now()->year)
            ->where('created_at', '>=', Carbon::now()->subMonths(7))
            ->groupBy('month')
            ->pluck('total_revenue', 'month');

        // Get expenses data for last 7 months
        $expensesData = Expense::selectRaw('EXTRACT(MONTH FROM expense_date) as month, SUM(amount) as total_expense')
            ->whereYear('expense_date', Carbon::now()->year)
            ->where('expense_date', '>=', Carbon::now()->subMonths(7))
            ->groupBy('month')
            ->pluck('total_expense', 'month');

        // Loop to get the last 7 months
        $months = [];
        $revenueValues = [];
        $expenseValues = [];
        for ($i = 6; $i >= 0; $i--) {
            $carbon = Carbon::now()->subMonths($i);
            $months[] = $carbon->format('M'); // Short month name for chart
            $revenueValues[] = (double) ($revenueData[$carbon->month] ?? 0);
            $expenseValues[] = (double) ($expensesData[$carbon->month] ?? 0);
        }

        return [
            "months"   => $months,
            "revenue"  => $revenueValues,
            "expenses" => $expenseValues,
        ];
    }

    private function prepareOrderStatusChart(): array
    {
        // Get order counts by status for current month
        $statusCounts = Order::selectRaw('status, COUNT(*) as count')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('status')
            ->pluck('count', 'status');

        return [
            "labels" => [
                OrderStatusEnum::COMPLETED->label(),
                OrderStatusEnum::PENDING->label(),
                OrderStatusEnum::CANCELLED->label(),
            ],
            "data" => [
                (int) ($statusCounts[OrderStatusEnum::COMPLETED->value] ?? 0),
                (int) ($statusCounts[OrderStatusEnum::PENDING->value] ?? 0),
                (int) ($statusCounts[OrderStatusEnum::CANCELLED->value] ?? 0),
            ]
        ];
    }

    private function prepareTopCustomersChart(): array
    {
        // Get top 5 customers by total order value
        $topCustomers = Order::selectRaw('customer_id, customers.name as customer_name, SUM(orders.total) as total_spent')
            ->join('customers', 'customers.id', '=', 'orders.customer_id')
            ->whereMonth('orders.created_at', Carbon::now()->month)
            ->whereYear('orders.created_at', Carbon::now()->year)
            ->groupBy('customer_id', 'customers.name')
            ->orderByDesc('total_spent')
            ->limit(5)
            ->get();

        return [
            "labels" => $topCustomers->pluck('customer_name')->toArray(),
            "data"   => $topCustomers->pluck('total_spent')->map(fn($value) => (double) $value)->toArray(),
        ];
    }
}
