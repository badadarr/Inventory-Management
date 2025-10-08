<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, useForm} from '@inertiajs/vue3';
import HeaderStats from "@/Components/Headers/HeaderStats.vue";
import LowStockWidget from "@/Components/Dashboard/LowStockWidget.vue";
import RevenueExpensesChart from "@/Components/Dashboard/RevenueExpensesChart.vue";
import OrderStatusChart from "@/Components/Dashboard/OrderStatusChart.vue";
import TopCustomersChart from "@/Components/Dashboard/TopCustomersChart.vue";
import {watch} from "vue";

const props = defineProps({
    date: String,
    total_orders: Object,
    total_revenue: Object,
    total_expense: Object,
    total_customers: Object,
    net_profit: Object,
    total_products: Object,
    low_stock_count: Object,
    completion_rate: Object,
    revenue_expenses_chart: Object,
    order_status_chart: Object,
    top_customers_chart: Object,
});

const form = useForm({
    date: props.date,
});

watch(() => form.date, async (newDateRange, oldDateRange) => {
    form.get(route('dashboard'), {
        preserveScroll: true,
    });
})
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #breadcrumb>
<!--            <Datepicker-->
<!--                v-model="form.date"-->
<!--                model-type="yyyy-MM-dd"-->
<!--                format="yyyy-MM-dd"-->
<!--                range-->
<!--                auto-apply-->
<!--                :enable-time-picker="false"-->
<!--                placeholder="Select date range"-->
<!--            />-->
            <input
                type="month"
                placeholder="Select Month"
                v-model="form.date"
                class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 relative bg-white rounded text-sm shadow outline-none focus:outline-none focus:ring w-full pl-10"
            />
        </template>

        <template #headerState>
            <HeaderStats
                :total_orders="total_orders"
                :total_revenue="total_revenue"
                :total_expense="total_expense"
                :total_customers="total_customers"
                :net_profit="net_profit"
                :total_products="total_products"
                :low_stock_count="low_stock_count"
                :completion_rate="completion_rate"
            />
        </template>

        <div class="pt-8">
            <!-- Low Stock Alert Widget (Purchase Orders) - TOP PRIORITY -->
            <div class="flex flex-wrap mb-6">
                <div class="w-full px-4">
                    <LowStockWidget />
                </div>
            </div>

            <!-- Secondary Charts: Order Status & Top Customers -->
            <div class="flex flex-wrap mb-6">
                <div class="w-full xl:w-6/12 mb-6 xl:mb-0 px-4">
                    <OrderStatusChart :chartData="order_status_chart" />
                </div>
                <div class="w-full xl:w-6/12 px-4">
                    <TopCustomersChart :chartData="top_customers_chart" />
                </div>
            </div>

            <!-- Main Chart: Revenue vs Expenses -->
            <div class="flex flex-wrap">
                <div class="w-full px-4">
                    <RevenueExpensesChart :chartData="revenue_expenses_chart" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
