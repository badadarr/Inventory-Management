<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, router} from '@inertiajs/vue3';
import CardTable from "@/Components/Cards/CardTable.vue";
import TableData from "@/Components/TableData.vue";
import {ref} from 'vue';

defineProps({
    customers: {
        type: Array,
        default: () => []
    },
});

const filters = ref({
    start_date: null,
    end_date: null,
    limit: 10,
});

const tableHeads = ['Rank', "Customer Name", "Total Lembar", "Total Penjualan"];

const applyFilter = () => {
    router.get(route('reports.top-customers'), filters.value, {
        preserveState: true,
        preserveScroll: true,
    });
};

const formatCurrency = (num) => {
    return new Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR'}).format(num);
};

const formatNumber = (num) => {
    return new Intl.NumberFormat('id-ID').format(num);
};
</script>

<template>
    <Head title="Top Customers"/>

    <AuthenticatedLayout>
        <template #breadcrumb>Laporan Customer Terbesar</template>

        <div class="flex flex-wrap mt-4">
            <div class="w-full mb-12 px-4">
                <div class="relative flex flex-col min-w-0 break-words w-full mb-6 shadow-lg rounded bg-white">
                    <div class="rounded-t mb-0 px-4 py-3 border-0">
                        <div class="flex flex-wrap items-center">
                            <div class="relative w-full px-4 max-w-full flex-grow flex-1">
                                <h3 class="font-semibold text-lg text-blueGray-700">Top {{ customers.length }} Customers</h3>
                            </div>
                        </div>
                    </div>
                    
                    <div class="px-4 py-3 border-b">
                        <div class="grid gap-4 sm:grid-cols-1 md:grid-cols-4">
                            <div>
                                <label class="block uppercase text-blueGray-600 text-xs font-bold mb-2">Start Date</label>
                                <input v-model="filters.start_date" type="date" class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 bg-white rounded text-sm shadow focus:outline-none focus:ring w-full ease-linear transition-all duration-150"/>
                            </div>
                            <div>
                                <label class="block uppercase text-blueGray-600 text-xs font-bold mb-2">End Date</label>
                                <input v-model="filters.end_date" type="date" class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 bg-white rounded text-sm shadow focus:outline-none focus:ring w-full ease-linear transition-all duration-150"/>
                            </div>
                            <div>
                                <label class="block uppercase text-blueGray-600 text-xs font-bold mb-2">Limit</label>
                                <select v-model="filters.limit" class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 bg-white rounded text-sm shadow focus:outline-none focus:ring w-full ease-linear transition-all duration-150">
                                    <option value="5">Top 5</option>
                                    <option value="10">Top 10</option>
                                    <option value="20">Top 20</option>
                                    <option value="50">Top 50</option>
                                </select>
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
                                <tr v-for="(customer, index) in customers" :key="index">
                                    <TableData>
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full font-bold" :class="index === 0 ? 'bg-yellow-400 text-white' : index === 1 ? 'bg-gray-300 text-white' : index === 2 ? 'bg-orange-400 text-white' : 'bg-blue-100 text-blue-800'">
                                            {{ index + 1 }}
                                        </span>
                                    </TableData>
                                    <TableData class="font-bold">{{ customer.customer?.name }}</TableData>
                                    <TableData>{{ formatNumber(customer.total_lembar || 0) }}</TableData>
                                    <TableData class="font-bold text-green-600">{{ formatCurrency(customer.total_penjualan) }}</TableData>
                                </tr>
                                <tr v-if="customers.length === 0">
                                    <td :colspan="tableHeads.length" class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4 text-center text-blueGray-500">
                                        No customers found
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
