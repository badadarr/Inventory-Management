<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { getCurrency, numberFormat } from "@/Utils/Helper.js";
import { computed } from 'vue';
import Button from "@/Components/Button.vue";
import OrderTimeline from "@/Components/Timeline/OrderTimeline.vue";

const props = defineProps({
    order: {
        type: Object,
        required: true
    },
});

// Format date for display
const formatDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const formatDateTime = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

// Status badge color
const statusColor = computed(() => {
    switch (props.order.status) {
        case 'completed':
            return 'bg-emerald-100 text-emerald-700';
        case 'pending':
            return 'bg-yellow-100 text-yellow-700';
        case 'cancelled':
            return 'bg-red-100 text-red-700';
        default:
            return 'bg-gray-100 text-gray-700';
    }
});

const statusIcon = computed(() => {
    switch (props.order.status) {
        case 'completed':
            return 'fa-check-circle';
        case 'pending':
            return 'fa-clock';
        case 'cancelled':
            return 'fa-times-circle';
        default:
            return 'fa-question-circle';
    }
});

// Print function
const printInvoice = () => {
    window.print();
};
</script>

<template>

    <Head title="Order Detail" />

    <AuthenticatedLayout>
        <template #breadcrumb>
            <div class="flex justify-between items-center w-full">
                <h2 class="text-white text-2xl font-bold">
                    Order Detail - {{ order.order_number }}
                </h2>
            </div>
        </template>

        <div class="py-12 relative z-50">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 relative z-50">
                <!-- Action Buttons -->
                <div class="flex justify-end gap-2 mb-6 print:hidden">
                    <Button @click="printInvoice" class="bg-blue-600 hover:bg-blue-700">
                        <i class="fa fa-print mr-2"></i>Print Invoice
                    </Button>
                    <Link v-if="order.status !== 'completed'" :href="route('orders.edit', order.id)">
                    <Button class="bg-emerald-600 hover:bg-emerald-700">
                        <i class="fa fa-edit mr-2"></i>Edit Order
                    </Button>
                    </Link>
                    <Link :href="route('orders.index')">
                    <Button class="bg-gray-600 hover:bg-gray-700">
                        <i class="fa fa-arrow-left mr-2"></i>Back to Orders
                    </Button>
                    </Link>
                </div>

                <!-- Status Banner -->
                <div :class="['mb-6 p-4 rounded-lg border-2 print:border relative z-50', statusColor]">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <i :class="['fas text-2xl', statusIcon]"></i>
                            <div>
                                <div class="font-semibold text-lg">Order Status: {{ order.status.toUpperCase() }}</div>
                                <div class="text-sm opacity-80">Order Number: {{ order.order_number }}</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm opacity-80">Created</div>
                            <div class="font-semibold">{{ formatDateTime(order.created_at) }}</div>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg print:shadow-none relative z-50">
                    <div class="p-6 text-gray-900">
                        <!-- Header Section (Company & Order Info) -->
                        <div class="border-b-2 border-gray-200 pb-6 mb-6 print:border-black">
                            <div class="grid grid-cols-2 gap-8">
                                <!-- Company Info -->
                                <div>
                                    <h3 class="text-2xl font-bold text-gray-900 mb-2">PT. Your Company Name</h3>
                                    <p class="text-gray-600 text-sm">
                                        Jl. Your Company Address<br>
                                        City, Postal Code<br>
                                        Phone: (021) 1234-5678<br>
                                        Email: info@company.com
                                    </p>
                                </div>

                                <!-- Order Info -->
                                <div class="text-right">
                                    <h3 class="text-3xl font-bold text-gray-900 mb-2">INVOICE</h3>
                                    <table class="ml-auto">
                                        <tbody class="text-sm">
                                            <tr>
                                                <td class="pr-4 py-1 text-gray-600 font-semibold">Order Number:</td>
                                                <td class="py-1 text-gray-900">{{ order.order_number }}</td>
                                            </tr>
                                            <tr>
                                                <td class="pr-4 py-1 text-gray-600 font-semibold">Order Date:</td>
                                                <td class="py-1 text-gray-900">{{ formatDate(order.created_at) }}</td>
                                            </tr>
                                            <tr v-if="order.tanggal_po">
                                                <td class="pr-4 py-1 text-gray-600 font-semibold">PO Date:</td>
                                                <td class="py-1 text-gray-900">{{ formatDate(order.tanggal_po) }}</td>
                                            </tr>
                                            <tr v-if="order.tanggal_kirim">
                                                <td class="pr-4 py-1 text-gray-600 font-semibold">Delivery Date:</td>
                                                <td class="py-1 text-gray-900">{{ formatDate(order.tanggal_kirim) }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Customer & Sales Info -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- Customer Details -->
                            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200 print:border-gray-300">
                                <h4 class="font-bold text-gray-900 mb-3 flex items-center">
                                    <i class="fas fa-user-circle mr-2 text-blue-600"></i>
                                    Customer Information
                                </h4>
                                <table class="w-full text-sm">
                                    <tbody>
                                        <tr>
                                            <td class="py-1 text-gray-600 font-semibold">Name:</td>
                                            <td class="py-1 text-gray-900">{{ order.customer?.name || 'Walk-in' }}</td>
                                        </tr>
                                        <tr v-if="order.customer?.email">
                                            <td class="py-1 text-gray-600 font-semibold">Email:</td>
                                            <td class="py-1 text-gray-900">{{ order.customer.email }}</td>
                                        </tr>
                                        <tr v-if="order.customer?.phone">
                                            <td class="py-1 text-gray-600 font-semibold">Phone:</td>
                                            <td class="py-1 text-gray-900">{{ order.customer.phone }}</td>
                                        </tr>
                                        <tr v-if="order.customer?.address">
                                            <td class="py-1 text-gray-600 font-semibold align-top">Address:</td>
                                            <td class="py-1 text-gray-900">{{ order.customer.address }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Sales & Material Info -->
                            <div class="space-y-4">
                                <!-- Sales Person -->
                                <div v-if="order.sales"
                                    class="bg-emerald-50 p-4 rounded-lg border border-emerald-200 print:border-gray-300">
                                    <h4 class="font-bold text-gray-900 mb-3 flex items-center">
                                        <i class="fas fa-user-tie mr-2 text-emerald-600"></i>
                                        Sales Person
                                    </h4>
                                    <p class="text-gray-900 font-semibold">{{ order.sales.name }}</p>
                                </div>

                                <!-- Material Details -->
                                <div v-if="order.jenis_bahan || order.gramasi || order.volume"
                                    class="bg-purple-50 p-4 rounded-lg border border-purple-200 print:border-gray-300">
                                    <h4 class="font-bold text-gray-900 mb-3 flex items-center">
                                        <i class="fas fa-boxes mr-2 text-purple-600"></i>
                                        Material Details
                                    </h4>
                                    <table class="w-full text-sm">
                                        <tbody>
                                            <tr v-if="order.jenis_bahan">
                                                <td class="py-1 text-gray-600 font-semibold">Material Type:</td>
                                                <td class="py-1 text-gray-900">{{ order.jenis_bahan }}</td>
                                            </tr>
                                            <tr v-if="order.gramasi">
                                                <td class="py-1 text-gray-600 font-semibold">Grammage:</td>
                                                <td class="py-1 text-gray-900">{{ order.gramasi }}</td>
                                            </tr>
                                            <tr v-if="order.volume">
                                                <td class="py-1 text-gray-600 font-semibold">Volume:</td>
                                                <td class="py-1 text-gray-900">{{ numberFormat(order.volume) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Pricing Info -->
                        <div v-if="order.harga_jual_pcs || order.jumlah_cetak"
                            class="bg-orange-50 p-4 rounded-lg border border-orange-200 mb-6 print:border-gray-300">
                            <h4 class="font-bold text-gray-900 mb-3 flex items-center">
                                <i class="fas fa-tags mr-2 text-orange-600"></i>
                                Pricing Information
                            </h4>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div v-if="order.harga_jual_pcs">
                                    <span class="text-gray-600 font-semibold">Price per PCS:</span>
                                    <span class="ml-2 text-gray-900">{{ getCurrency() }}{{
                                        numberFormat(order.harga_jual_pcs)
                                        }}</span>
                                </div>
                                <div v-if="order.jumlah_cetak">
                                    <span class="text-gray-600 font-semibold">Print Quantity:</span>
                                    <span class="ml-2 text-gray-900">{{ numberFormat(order.jumlah_cetak) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Order Items -->
                        <div class="mb-6">
                            <h4 class="font-bold text-gray-900 mb-4 text-lg flex items-center">
                                <i class="fas fa-shopping-cart mr-2 text-blue-600"></i>
                                Order Items
                            </h4>
                            <div class="overflow-x-auto">
                                <table class="w-full border-collapse border border-gray-300 print:border-black">
                                    <thead class="bg-gray-100 print:bg-gray-200">
                                        <tr>
                                            <th
                                                class="border border-gray-300 px-4 py-3 text-left text-sm font-bold text-gray-900 print:border-black">
                                                #</th>
                                            <th
                                                class="border border-gray-300 px-4 py-3 text-left text-sm font-bold text-gray-900 print:border-black">
                                                Product</th>
                                            <th
                                                class="border border-gray-300 px-4 py-3 text-left text-sm font-bold text-gray-900 print:border-black">
                                                Code</th>
                                            <th
                                                class="border border-gray-300 px-4 py-3 text-right text-sm font-bold text-gray-900 print:border-black">
                                                Price</th>
                                            <th
                                                class="border border-gray-300 px-4 py-3 text-right text-sm font-bold text-gray-900 print:border-black">
                                                Quantity</th>
                                            <th
                                                class="border border-gray-300 px-4 py-3 text-right text-sm font-bold text-gray-900 print:border-black">
                                                Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(item, index) in order.orderItems" :key="item.id"
                                            class="hover:bg-gray-50">
                                            <td class="border border-gray-300 px-4 py-3 text-sm print:border-black">{{
                                                index + 1
                                                }}</td>
                                            <td
                                                class="border border-gray-300 px-4 py-3 text-sm font-medium text-gray-900 print:border-black">
                                                {{ item.product?.name || 'Unknown Product' }}
                                            </td>
                                            <td
                                                class="border border-gray-300 px-4 py-3 text-sm text-gray-600 print:border-black">
                                                {{ item.product?.product_code || '-' }}
                                            </td>
                                            <td
                                                class="border border-gray-300 px-4 py-3 text-sm text-right print:border-black">
                                                {{ getCurrency() }}{{ numberFormat(item.unit_price) }}
                                            </td>
                                            <td
                                                class="border border-gray-300 px-4 py-3 text-sm text-right print:border-black">
                                                {{ numberFormat(item.quantity) }} {{ item.product?.unit_type?.symbol ||
                                                    item.product?.unitType?.symbol || '' }}
                                            </td>
                                            <td
                                                class="border border-gray-300 px-4 py-3 text-sm text-right font-semibold print:border-black">
                                                {{ getCurrency() }}{{ numberFormat(item.subtotal) }}
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot class="bg-gray-50 print:bg-gray-100">
                                        <tr>
                                            <td colspan="5"
                                                class="border border-gray-300 px-4 py-3 text-right font-bold text-gray-900 print:border-black">
                                                Sub Total:</td>
                                            <td
                                                class="border border-gray-300 px-4 py-3 text-right font-bold text-lg text-gray-900 print:border-black">
                                                {{ getCurrency() }}{{ numberFormat(order.sub_total) }}
                                            </td>
                                        </tr>
                                        <tr v-if="order.tax_total > 0">
                                            <td colspan="5"
                                                class="border border-gray-300 px-4 py-2 text-right text-gray-700 print:border-black">
                                                Tax:</td>
                                            <td
                                                class="border border-gray-300 px-4 py-2 text-right text-gray-900 print:border-black">
                                                {{ getCurrency() }}{{ numberFormat(order.tax_total) }}
                                            </td>
                                        </tr>
                                        <tr v-if="order.discount_total > 0">
                                            <td colspan="5"
                                                class="border border-gray-300 px-4 py-2 text-right text-gray-700 print:border-black">
                                                Discount:</td>
                                            <td
                                                class="border border-gray-300 px-4 py-2 text-right text-red-600 print:border-black">
                                                -{{ getCurrency() }}{{ numberFormat(order.discount_total) }}
                                            </td>
                                        </tr>
                                        <tr class="bg-blue-50 print:bg-gray-200">
                                            <td colspan="5"
                                                class="border border-gray-300 px-4 py-4 text-right font-bold text-lg text-gray-900 print:border-black">
                                                Grand Total:</td>
                                            <td
                                                class="border border-gray-300 px-4 py-4 text-right font-bold text-xl text-blue-600 print:text-black print:border-black">
                                                {{ getCurrency() }}{{ numberFormat(order.total) }}
                                            </td>
                                        </tr>
                                        <tr class="bg-emerald-50 print:bg-gray-100">
                                            <td colspan="5"
                                                class="border border-gray-300 px-4 py-3 text-right font-semibold text-gray-900 print:border-black">
                                                Paid:</td>
                                            <td
                                                class="border border-gray-300 px-4 py-3 text-right font-semibold text-emerald-600 print:text-black print:border-black">
                                                {{ getCurrency() }}{{ numberFormat(order.paid) }}
                                            </td>
                                        </tr>
                                        <tr v-if="order.due > 0" class="bg-red-50 print:bg-gray-100">
                                            <td colspan="5"
                                                class="border border-gray-300 px-4 py-3 text-right font-semibold text-gray-900 print:border-black">
                                                Due:</td>
                                            <td
                                                class="border border-gray-300 px-4 py-3 text-right font-semibold text-red-600 print:text-black print:border-black">
                                                {{ getCurrency() }}{{ numberFormat(order.due) }}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <!-- Payment Information -->
                        <div v-if="order.transactions && order.transactions.length > 0"
                            class="bg-green-50 p-4 rounded-lg border border-green-200 mb-6 print:border-gray-300">
                            <h4 class="font-bold text-gray-900 mb-3 flex items-center">
                                <i class="fas fa-money-bill-wave mr-2 text-green-600"></i>
                                Payment Information
                            </h4>

                            <!-- Multiple Transactions -->
                            <div v-for="(transaction, index) in order.transactions" :key="transaction.id"
                                :class="{ 'mt-4 pt-4 border-t border-green-300': index > 0 }">
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <span class="text-gray-600 font-semibold">Payment Method:</span>
                                        <span class="ml-2 text-gray-900 capitalize">{{
                                            transaction.paid_through?.replace('_', ' ') || ' - ' }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600 font-semibold">Amount Paid:</span>
                                        <span class="ml-2 text-gray-900 font-bold">{{ getCurrency() }}{{
                                            numberFormat(transaction.amount) }}</span>
                                    </div>
                                    <div v-if="transaction.created_at">
                                        <span class="text-gray-600 font-semibold">Payment Date:</span>
                                        <span class="ml-2 text-gray-900">{{ formatDateTime(transaction.created_at)
                                            }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Total Paid Summary -->
                            <div v-if="order.transactions.length > 1" class="mt-4 pt-4 border-t border-green-400">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-700 font-bold">Total Payments:</span>
                                    <span class="text-gray-900 font-bold text-lg">{{ getCurrency() }}{{
                                        numberFormat(order.paid)
                                        }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Notes -->
                        <div v-if="order.catatan"
                            class="bg-yellow-50 p-4 rounded-lg border border-yellow-200 mb-6 print:border-gray-300">
                            <h4 class="font-bold text-gray-900 mb-2 flex items-center">
                                <i class="fas fa-sticky-note mr-2 text-yellow-600"></i>
                                Additional Notes
                            </h4>
                            <p class="text-gray-900 text-sm whitespace-pre-wrap">{{ order.catatan }}</p>
                        </div>

                        <!-- Order Timeline & History -->
                        <div class="mt-6 print:hidden">
                            <OrderTimeline :activities="order.activities || []" />
                        </div>

                        <!-- Footer Info -->
                        <div class="border-t-2 border-gray-200 pt-6 mt-6 print:border-black">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                                <div>
                                    <p class="font-semibold text-gray-900 mb-2">Order Created By:</p>
                                    <p>{{ order.createdBy?.name || 'System' }}</p>
                                    <p class="text-xs">{{ formatDateTime(order.created_at) }}</p>
                                </div>
                                <div class="text-right print:text-left">
                                    <p class="font-semibold text-gray-900 mb-2">Last Updated:</p>
                                    <p>{{ formatDateTime(order.updated_at) }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Print Footer (Only visible when printing) -->
                        <div
                            class="hidden print:block mt-8 pt-6 border-t border-gray-300 text-center text-sm text-gray-600">
                            <p>Thank you for your business!</p>
                            <p class="mt-2">This is a computer-generated invoice and does not require a signature.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
@media print {
    @page {
        size: A4;
        margin: 1cm;
    }

    body {
        print-color-adjust: exact;
        -webkit-print-color-adjust: exact;
    }
}
</style>
