<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, router} from '@inertiajs/vue3';
import CardTable from "@/Components/Cards/CardTable.vue";
import TableData from "@/Components/TableData.vue";
import {ref} from 'vue';

defineProps({
    orders: {
        type: Array,
        default: () => []
    },
});

const filters = ref({
    start_date: null,
    end_date: null,
});

const tableHeads = ['#', "Customer", "Tanggal PO", "Tanggal Kirim", "Jenis Bahan", "Gramasi", "Volume", "Harga/Pcs", "Jumlah Cetak", "Total", "Charge", "Paid", "Outstanding"];

const applyFilter = () => {
    router.get(route('reports.outstanding'), filters.value, {
        preserveState: true,
        preserveScroll: true,
    });
};

const formatCurrency = (num) => {
    return new Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR'}).format(num);
};

const formatDate = (date) => {
    return date ? new Date(date).toLocaleDateString('id-ID') : '-';
};
</script>

<template>
    <Head title="Outstanding Report"/>

    <AuthenticatedLayout>
        <template #breadcrumb>Laporan Outstanding</template>

        <div class="flex flex-wrap">
            <div class="w-full px-4">
                <div class="bg-white rounded-lg shadow-lg p-4 mb-4">
                    <div class="grid gap-4 sm:grid-cols-1 md:grid-cols-3">
                        <div>
                            <label class="text-stone-600 text-sm font-medium">Start Date</label>
                            <input v-model="filters.start_date" type="date" class="mt-2 block w-full rounded-md border border-gray-200 px-2 py-2 shadow-sm outline-none"/>
                        </div>
                        <div>
                            <label class="text-stone-600 text-sm font-medium">End Date</label>
                            <input v-model="filters.end_date" type="date" class="mt-2 block w-full rounded-md border border-gray-200 px-2 py-2 shadow-sm outline-none"/>
                        </div>
                        <div class="flex items-end">
                            <button @click="applyFilter" class="w-full bg-emerald-500 text-white px-4 py-2 rounded-md hover:bg-emerald-600">
                                Apply Filter
                            </button>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-lg overflow-x-auto">
                    <div class="px-4 py-3 border-b">
                        <h4 class="text-2xl">Outstanding Orders ({{ orders.length }})</h4>
                    </div>
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th v-for="(head, idx) in tableHeads" :key="idx" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    {{ head }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="(order, index) in orders" :key="order.id" class="hover:bg-gray-50">
                                <td class="px-4 py-3 whitespace-nowrap text-sm">{{ index + 1 }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-blueGray-600">{{ order.customer?.name }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm">{{ formatDate(order.tanggal_po) }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm">{{ formatDate(order.tanggal_kirim) }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm">{{ order.jenis_bahan || '-' }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm">{{ order.gramasi || '-' }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm">{{ order.volume || '-' }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm">{{ formatCurrency(order.harga_jual_pcs || 0) }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm">{{ order.jumlah_cetak || '-' }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm">{{ formatCurrency(order.total) }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm">{{ formatCurrency(order.charge || 0) }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm">{{ formatCurrency(order.paid) }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-red-600">{{ formatCurrency(order.due) }}</td>
                            </tr>
                            <tr v-if="orders.length === 0">
                                <td :colspan="tableHeads.length" class="px-4 py-8 text-center text-gray-500">
                                    No outstanding orders found
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
