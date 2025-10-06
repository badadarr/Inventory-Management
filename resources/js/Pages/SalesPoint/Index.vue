<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import CardTable from "@/Components/Cards/CardTable.vue";
import TableData from "@/Components/TableData.vue";
import Pagination from "@/Components/Pagination.vue";

defineProps({
    recap: {
        type: Object,
        default: () => ({})
    },
});

const tableHeads = ['#', "Sales Name", "Product Type", "Total Cetak", "Total Points"];

const formatNumber = (num) => {
    return new Intl.NumberFormat('id-ID').format(num);
};
</script>

<template>
    <Head title="Sales Points"/>

    <AuthenticatedLayout>
        <template #breadcrumb>Sales Points Recap</template>

        <div class="flex flex-wrap">
            <div class="w-full px-4">
                <CardTable :tableHeads="tableHeads">
                    <template #cardHeader>
                        <h4 class="text-2xl">Rekap Point Penjualan</h4>
                    </template>

                    <tr v-for="(item, index) in recap.data" :key="index">
                        <TableData>{{ recap.from + index }}</TableData>
                        <TableData class="text-left font-bold text-blueGray-600">{{ item.sales?.name }}</TableData>
                        <TableData>
                            <span :class="item.product_type === 'box' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800'" class="px-2 py-1 rounded text-xs">
                                {{ item.product_type === 'box' ? 'Box' : 'Kertas Nasi Padang' }}
                            </span>
                        </TableData>
                        <TableData>{{ formatNumber(item.total_cetak) }}</TableData>
                        <TableData class="font-bold text-green-600">{{ formatNumber(item.total_points) }}</TableData>
                    </tr>
                </CardTable>
                <Pagination :links="recap.links" class="mt-4"/>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
