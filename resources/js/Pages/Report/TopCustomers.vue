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

        <div class="flex flex-wrap">
            <div class="w-full px-4">
                <div class="bg-white rounded-lg shadow-lg p-4 mb-4">
                    <div class="grid gap-4 sm:grid-cols-1 md:grid-cols-4">
                        <div>
                            <label class="text-stone-600 text-sm font-medium">Start Date</label>
                            <input v-model="filters.start_date" type="date" class="mt-2 block w-full rounded-md border border-gray-200 px-2 py-2 shadow-sm outline-none"/>
                        </div>
                        <div>
                            <label class="text-stone-600 text-sm font-medium">End Date</label>
                            <input v-model="filters.end_date" type="date" class="mt-2 block w-full rounded-md border border-gray-200 px-2 py-2 shadow-sm outline-none"/>
                        </div>
                        <div>
                            <label class="text-stone-600 text-sm font-medium">Limit</label>
                            <select v-model="filters.limit" class="mt-2 block w-full rounded-md border border-gray-200 px-2 py-2 shadow-sm outline-none">
                                <option value="5">Top 5</option>
                                <option value="10">Top 10</option>
                                <option value="20">Top 20</option>
                                <option value="50">Top 50</option>
                            </select>
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
                        <h4 class="text-2xl">Top {{ customers.length }} Customers</h4>
                    </div>
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th v-for="(head, idx) in tableHeads" :key="idx" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ head }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="(customer, index) in customers" :key="index" class="hover:bg-gray-50">
                                <td class="px-4 py-3 whitespace-nowrap text-sm">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full font-bold" :class="index === 0 ? 'bg-yellow-400 text-white' : index === 1 ? 'bg-gray-300 text-white' : index === 2 ? 'bg-orange-400 text-white' : 'bg-blue-100 text-blue-800'">
                                        {{ index + 1 }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-blueGray-600">{{ customer.customer?.name }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm">{{ formatNumber(customer.total_lembar || 0) }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-green-600">{{ formatCurrency(customer.total_penjualan) }}</td>
                            </tr>
                            <tr v-if="customers.length === 0">
                                <td :colspan="tableHeads.length" class="px-4 py-8 text-center text-gray-500">
                                    No customers found
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
