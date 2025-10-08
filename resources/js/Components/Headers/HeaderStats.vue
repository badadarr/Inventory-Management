<template>
    <!-- Row 1: Financial Metrics -->
    <div class="flex flex-wrap mb-6">
        <div class="w-full lg:w-6/12 xl:w-3/12 px-4 mb-6 xl:mb-0">
            <card-stats
                statSubtitle="REVENUE"
                :statTitle="'Rp ' + (total_revenue.selected / 1000000).toFixed(1) + 'M'"
                :statArrow="total_revenue.stateArray"
                :statPercent="total_revenue.percentage_change"
                :statPercentColor="total_revenue.stateArray === 'up' ? 'text-emerald-500' : 'text-red-500'"
                statDescripiron="Since last month"
                statIconName="fas fa-dollar-sign"
                statIconColor="bg-blue-500"
            />
        </div>
        <div class="w-full lg:w-6/12 xl:w-3/12 px-4 mb-6 xl:mb-0">
            <card-stats
                statSubtitle="ORDERS"
                :statTitle="total_orders.selected"
                :statArrow="total_orders.stateArray"
                :statPercent="total_orders.percentage_change"
                :statPercentColor="total_orders.stateArray === 'up' ? 'text-emerald-500' : 'text-red-500'"
                statDescripiron="Since last month"
                statIconName="fas fa-shopping-cart"
                statIconColor="bg-emerald-500"
            />
        </div>
        <div class="w-full lg:w-6/12 xl:w-3/12 px-4 mb-6 xl:mb-0">
            <card-stats
                statSubtitle="EXPENSES"
                :statTitle="'Rp ' + (total_expense.selected / 1000000).toFixed(1) + 'M'"
                :statArrow="total_expense.stateArray"
                :statPercent="total_expense.percentage_change"
                :statPercentColor="total_expense.stateArray === 'down' ? 'text-emerald-500' : 'text-red-500'"
                statDescripiron="Since last month"
                statIconName="fas fa-money-bill-wave"
                statIconColor="bg-red-500"
            />
        </div>
        <div class="w-full lg:w-6/12 xl:w-3/12 px-4 mb-6 xl:mb-0">
            <card-stats
                statSubtitle="CUSTOMERS"
                :statTitle="total_customers.selected"
                :statArrow="total_customers.stateArray"
                :statPercent="total_customers.percentage_change"
                :statPercentColor="total_customers.stateArray === 'up' ? 'text-emerald-500' : 'text-red-500'"
                statDescripiron="Since last month"
                statIconName="fas fa-users"
                statIconColor="bg-purple-500"
            />
        </div>
    </div>

    <!-- Row 2: Performance Metrics -->
    <div class="flex flex-wrap">
        <div class="w-full lg:w-6/12 xl:w-3/12 px-4 mb-6 xl:mb-0">
            <card-stats
                statSubtitle="NET PROFIT"
                :statTitle="'Rp ' + (net_profit.selected / 1000000).toFixed(1) + 'M'"
                :statArrow="net_profit.stateArray"
                :statPercent="net_profit.percentage_change"
                :statPercentColor="net_profit.stateArray === 'up' ? 'text-emerald-500' : 'text-red-500'"
                statDescripiron="Since last month"
                statIconName="fas fa-chart-line"
                statIconColor="bg-green-500"
            />
        </div>
        <div class="w-full lg:w-6/12 xl:w-3/12 px-4 mb-6 xl:mb-0">
            <card-stats
                statSubtitle="PRODUCTS"
                :statTitle="total_products.selected"
                :statArrow="'up'"
                :statPercent="total_products.new_this_month + ' new'"
                statPercentColor="text-emerald-500"
                statDescripiron="This month"
                statIconName="fas fa-box"
                statIconColor="bg-orange-500"
            />
        </div>
        <div class="w-full lg:w-6/12 xl:w-3/12 px-4 mb-6 xl:mb-0">
            <card-stats
                statSubtitle="LOW STOCK"
                :statTitle="low_stock_count.selected"
                statArrow="down"
                statPercent="Urgent"
                statPercentColor="text-red-500"
                statDescripiron="Need restock"
                statIconName="fas fa-exclamation-triangle"
                statIconColor="bg-yellow-500"
            />
        </div>
        <div class="w-full lg:w-6/12 xl:w-3/12 px-4 mb-6 xl:mb-0">
            <card-stats
                statSubtitle="COMPLETION"
                :statTitle="completion_rate.selected + '%'"
                :statArrow="completion_rate.stateArray"
                :statPercent="completion_rate.percentage_change + '%'"
                :statPercentColor="completion_rate.stateArray === 'up' ? 'text-emerald-500' : 'text-red-500'"
                statDescripiron="Orders completed"
                statIconName="fas fa-check-circle"
                statIconColor="bg-teal-500"
            />
        </div>
    </div>
</template>

<script setup>
import CardStats from "@/Components/Cards/CardStats.vue";

defineProps({
    total_orders: Object,
    total_revenue: Object,
    total_expense: Object,
    total_customers: Object,
    net_profit: Object,
    total_products: Object,
    low_stock_count: Object,
    completion_rate: Object,
});
</script>
