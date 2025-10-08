<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import CardTable from "@/Components/Cards/CardTable.vue";
import TableData from "@/Components/TableData.vue";
import { ref, computed } from 'vue';
import { formatDatetime, numberFormat } from "@/Utils/Helper.js";

const props = defineProps({
    movements: {
        type: Object,
        required: true
    },
    filters: {
        type: Object,
        default: () => ({})
    }
});

const tableHeads = ref([
    "Date/Time",
    "Product", 
    "Reference",
    "Type",
    "Quantity",
    "Balance After",
    "Created By",
    "Notes"
]);

// Movement type badge
const getMovementTypeClass = (type) => {
    return type === 'in' 
        ? 'bg-green-100 text-green-800' 
        : 'bg-red-100 text-red-800';
};

const getMovementTypeIcon = (type) => {
    return type === 'in' 
        ? 'fa-arrow-down' 
        : 'fa-arrow-up';
};

// Reference type formatting
const formatReferenceType = (type) => {
    const types = {
        'purchase_order': 'Purchase Order',
        'sales_order': 'Sales Order',
        'adjustment': 'Adjustment'
    };
    return types[type] || type;
};

const getReferenceTypeClass = (type) => {
    const classes = {
        'purchase_order': 'bg-blue-100 text-blue-800',
        'sales_order': 'bg-purple-100 text-purple-800',
        'adjustment': 'bg-yellow-100 text-yellow-800'
    };
    return classes[type] || 'bg-gray-100 text-gray-800';
};

// Calculate movement impact
const getMovementImpact = (movement) => {
    if (movement.movement_type === 'in') {
        return '+' + numberFormat(movement.quantity);
    }
    return '-' + numberFormat(movement.quantity);
};
</script>

<template>
    <Head title="Stock Movements" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Stock Movements</h2>
                <div class="text-sm text-gray-600">
                    <i class="fas fa-info-circle mr-1"></i>
                    Automatic tracking of all inventory changes
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Info Alert -->
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                Stock movements are automatically recorded when:
                                <span class="font-medium">Purchase Orders are received</span>, 
                                <span class="font-medium">Sales Orders are completed</span>, or
                                <span class="font-medium">Manual adjustments</span> are made.
                            </p>
                        </div>
                    </div>
                </div>

                <CardTable
                    :data="movements"
                    :resource="'stock-movements'"
                    :filters="filters"
                >
                    <template #table>
                        <table class="w-full">
                            <TableData :heads="tableHeads">
                                <tr 
                                    v-for="movement in movements.data" 
                                    :key="movement.id"
                                    class="border-b hover:bg-gray-50"
                                >
                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        {{ formatDatetime(movement.created_at) }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-gray-900">
                                            {{ movement.product?.name || 'N/A' }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            Code: {{ movement.product?.product_code }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div>
                                            <span 
                                                :class="getReferenceTypeClass(movement.reference_type)"
                                                class="px-2 py-1 text-xs rounded-full font-medium"
                                            >
                                                {{ formatReferenceType(movement.reference_type) }}
                                            </span>
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1" v-if="movement.reference_id">
                                            ID: {{ movement.reference_id }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span 
                                            :class="getMovementTypeClass(movement.movement_type)"
                                            class="px-3 py-1 text-sm rounded-full font-medium uppercase inline-flex items-center"
                                        >
                                            <i :class="['fas', getMovementTypeIcon(movement.movement_type), 'mr-1']"></i>
                                            {{ movement.movement_type }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div 
                                            :class="movement.movement_type === 'in' ? 'text-green-600' : 'text-red-600'"
                                            class="font-medium text-lg"
                                        >
                                            {{ getMovementImpact(movement) }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="font-medium text-gray-900">
                                            {{ numberFormat(movement.balance_after) }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            units
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        {{ movement.created_by_user?.name || 'System' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-sm text-gray-600 max-w-xs truncate" :title="movement.notes">
                                            {{ movement.notes || '-' }}
                                        </div>
                                    </td>
                                </tr>
                                
                                <!-- Empty State -->
                                <tr v-if="movements.data.length === 0">
                                    <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                        <i class="fas fa-box-open text-4xl mb-3 text-gray-300"></i>
                                        <p>No stock movements found</p>
                                        <p class="text-sm mt-1">Movements will appear here when inventory changes occur</p>
                                    </td>
                                </tr>
                            </TableData>
                        </table>
                    </template>
                </CardTable>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
</style>
