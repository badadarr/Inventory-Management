<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, router} from '@inertiajs/vue3';
import CardTable from "@/Components/Cards/CardTable.vue";
import TableData from "@/Components/TableData.vue";
import Pagination from "@/Components/Pagination.vue";
import {ref} from 'vue';

defineProps({
    orders: {
        type: Object,
        default: () => ({})
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

        <div class="flex flex-wrap mt-4">
            <div class="w-full mb-12 px-4">
                <div class="relative flex flex-col min-w-0 break-words w-full mb-6 shadow-lg rounded bg-white">
                    <div class="rounded-t mb-0 px-4 py-3 border-0">
                        <div class="flex flex-wrap items-center">
                            <div class="relative w-full px-4 max-w-full flex-grow flex-1">
                                <h3 class="font-semibold text-lg text-blueGray-700">Outstanding Orders ({{ orders.total }})</h3>
                            </div>
                        </div>
                    </div>
                    
                    <div class="px-4 py-3 border-b">
                        <div class="grid gap-4 sm:grid-cols-1 md:grid-cols-3">
                            <div>
                                <label class="block uppercase text-blueGray-600 text-xs font-bold mb-2">Start Date</label>
                                <input v-model="filters.start_date" type="date" class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 bg-white rounded text-sm shadow focus:outline-none focus:ring w-full ease-linear transition-all duration-150"/>
                            </div>
                            <div>
                                <label class="block uppercase text-blueGray-600 text-xs font-bold mb-2">End Date</label>
                                <input v-model="filters.end_date" type="date" class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 bg-white rounded text-sm shadow focus:outline-none focus:ring w-full ease-linear transition-all duration-150"/>
                            </div>
                            <div class="flex items-end">
                                <button @click="applyFilter" class="bg-emerald-500 text-white active:bg-emerald-600 font-bold uppercase text-xs px-4 py-3 rounded shadow hover:shadow-md outline-none focus:outline-none ease-linear transition-all duration-150 w-full">
                                    Apply Filter
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="block w-full overflow-x-auto">
                        <table class="items-center w-full bg-transparent border-collapse">
                            <thead>
                                <tr>
                                    <th v-for="(head, idx) in tableHeads" :key="idx" class="px-6 align-middle border border-solid py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left bg-blueGray-50 text-blueGray-500 border-blueGray-100">
                                        {{ head }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(order, index) in orders.data" :key="order.id">
                                    <TableData>{{ orders.from + index }}</TableData>
                                    <TableData class="font-bold">{{ order.customer?.name }}</TableData>
                                    <TableData>{{ formatDate(order.tanggal_po) }}</TableData>
                                    <TableData>{{ formatDate(order.tanggal_kirim) }}</TableData>
                                    <TableData>{{ order.jenis_bahan || '-' }}</TableData>
                                    <TableData>{{ order.gramasi || '-' }}</TableData>
                                    <TableData>{{ order.volume || '-' }}</TableData>
                                    <TableData>{{ formatCurrency(order.harga_jual_pcs || 0) }}</TableData>
                                    <TableData>{{ order.jumlah_cetak || '-' }}</TableData>
                                    <TableData>{{ formatCurrency(order.total) }}</TableData>
                                    <TableData>{{ formatCurrency(order.charge || 0) }}</TableData>
                                    <TableData>{{ formatCurrency(order.paid) }}</TableData>
                                    <TableData class="font-bold text-red-600">{{ formatCurrency(order.due) }}</TableData>
                                </tr>
                                <tr v-if="orders.data?.length === 0">
                                    <td :colspan="tableHeads.length" class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4 text-center text-blueGray-500">
                                        No outstanding orders found
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="px-4 py-3">
                        <Pagination :links="orders.links"/>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
