<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import CardTable from "@/Components/Cards/CardTable.vue";
import TableData from "@/Components/TableData.vue";
import Button from "@/Components/Button.vue";
import Modal from "@/Components/Modal.vue";
import { ref, computed } from 'vue';
import { formatDate, getCurrency, numberFormat, showToast } from "@/Utils/Helper.js";

const props = defineProps({
    purchaseOrders: {
        type: Object,
        required: true
    },
    filters: {
        type: Object,
        default: () => ({})
    }
});

const tableHeads = ref([
    "PO Number", 
    "Supplier", 
    "Order Date", 
    "Expected Date",
    "Total Amount (" + getCurrency() + ")", 
    "Paid", 
    "Status", 
    "Actions"
]);

const selectedPO = ref(null);
const showReceiveModal = ref(false);
const showDetailsModal = ref(false);

// Status badge colors
const getStatusClass = (status) => {
    const classes = {
        'pending': 'bg-yellow-100 text-yellow-800',
        'received': 'bg-green-100 text-green-800',
        'cancelled': 'bg-red-100 text-red-800'
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

// Payment status
const getPaymentStatus = (po) => {
    if (po.paid_amount >= po.total_amount) {
        return { text: 'Paid', class: 'text-green-600' };
    } else if (po.paid_amount > 0) {
        return { text: 'Partial', class: 'text-yellow-600' };
    }
    return { text: 'Unpaid', class: 'text-red-600' };
};

const viewDetails = (po) => {
    selectedPO.value = po;
    showDetailsModal.value = true;
};

const openReceiveModal = (po) => {
    if (po.status !== 'pending') {
        showToast('Only pending purchase orders can be received', 'warning');
        return;
    }
    selectedPO.value = po;
    showReceiveModal.value = true;
};

const receivePO = () => {
    router.post(route('purchase-orders.receive', selectedPO.value.id), {
        items: [] // Items should be fetched/provided from PO details
    }, {
        onSuccess: () => {
            showToast('Purchase order received successfully', 'success');
            showReceiveModal.value = false;
        },
        onError: (errors) => {
            showToast(errors.message || 'Failed to receive purchase order', 'error');
        }
    });
};

const deletePO = (id) => {
    if (!confirm('Are you sure you want to delete this purchase order?')) {
        return;
    }
    
    router.delete(route('purchase-orders.destroy', id), {
        onSuccess: () => {
            showToast('Purchase order deleted successfully', 'success');
        },
        onError: (errors) => {
            showToast(errors.message || 'Failed to delete purchase order', 'error');
        }
    });
};

const getDueAmount = (po) => {
    return po.total_amount - (po.paid_amount || 0);
};
</script>

<template>
    <Head title="Purchase Orders" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Purchase Orders</h2>
                <Button 
                    :href="route('purchase-orders.create')"
                    class="bg-blue-600 hover:bg-blue-700"
                >
                    <i class="fas fa-plus mr-2"></i>
                    Create New PO
                </Button>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <CardTable
                    :data="purchaseOrders"
                    :resource="'purchase-orders'"
                    :filters="filters"
                >
                    <template #table>
                        <table class="w-full">
                            <TableData :heads="tableHeads">
                                <tr 
                                    v-for="po in purchaseOrders.data" 
                                    :key="po.id"
                                    class="border-b hover:bg-gray-50"
                                >
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-gray-900">{{ po.order_number }}</div>
                                        <div class="text-xs text-gray-500" v-if="po.notes">
                                            {{ po.notes.substring(0, 50) }}{{ po.notes.length > 50 ? '...' : '' }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-gray-900">{{ po.supplier?.name || 'N/A' }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">
                                        {{ formatDate(po.order_date) }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">
                                        {{ formatDate(po.expected_date) }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-medium">
                                        {{ numberFormat(po.total_amount) }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-right">
                                            <div :class="getPaymentStatus(po).class" class="font-medium">
                                                {{ getPaymentStatus(po).text }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ numberFormat(po.paid_amount || 0) }}
                                            </div>
                                            <div class="text-xs text-gray-500" v-if="getDueAmount(po) > 0">
                                                Due: {{ numberFormat(getDueAmount(po)) }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span 
                                            :class="getStatusClass(po.status)"
                                            class="px-2 py-1 text-xs rounded-full font-medium uppercase"
                                        >
                                            {{ po.status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex gap-2">
                                            <Button
                                                @click="viewDetails(po)"
                                                size="sm"
                                                variant="info"
                                                title="View Details"
                                            >
                                                <i class="fas fa-eye"></i>
                                            </Button>
                                            <Button
                                                v-if="po.status === 'pending'"
                                                @click="openReceiveModal(po)"
                                                size="sm"
                                                variant="success"
                                                title="Receive PO"
                                            >
                                                <i class="fas fa-check"></i>
                                            </Button>
                                            <Button
                                                :href="route('purchase-orders.edit', po.id)"
                                                size="sm"
                                                variant="warning"
                                                title="Edit"
                                                v-if="po.status === 'pending'"
                                            >
                                                <i class="fas fa-edit"></i>
                                            </Button>
                                            <Button
                                                @click="deletePO(po.id)"
                                                size="sm"
                                                variant="danger"
                                                title="Delete"
                                                v-if="po.status !== 'received'"
                                            >
                                                <i class="fas fa-trash"></i>
                                            </Button>
                                        </div>
                                    </td>
                                </tr>
                            </TableData>
                        </table>
                    </template>
                </CardTable>
            </div>
        </div>

        <!-- Details Modal -->
        <Modal :show="showDetailsModal" @close="showDetailsModal = false">
            <div class="p-6" v-if="selectedPO">
                <h3 class="text-lg font-semibold mb-4">Purchase Order Details</h3>
                
                <div class="space-y-3">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm text-gray-600">PO Number</label>
                            <div class="font-medium">{{ selectedPO.order_number }}</div>
                        </div>
                        <div>
                            <label class="text-sm text-gray-600">Status</label>
                            <div>
                                <span 
                                    :class="getStatusClass(selectedPO.status)"
                                    class="px-2 py-1 text-xs rounded-full font-medium uppercase"
                                >
                                    {{ selectedPO.status }}
                                </span>
                            </div>
                        </div>
                        <div>
                            <label class="text-sm text-gray-600">Supplier</label>
                            <div class="font-medium">{{ selectedPO.supplier?.name }}</div>
                        </div>
                        <div>
                            <label class="text-sm text-gray-600">Order Date</label>
                            <div class="font-medium">{{ formatDate(selectedPO.order_date) }}</div>
                        </div>
                        <div>
                            <label class="text-sm text-gray-600">Expected Date</label>
                            <div class="font-medium">{{ formatDate(selectedPO.expected_date) }}</div>
                        </div>
                        <div v-if="selectedPO.received_date">
                            <label class="text-sm text-gray-600">Received Date</label>
                            <div class="font-medium">{{ formatDate(selectedPO.received_date) }}</div>
                        </div>
                        <div>
                            <label class="text-sm text-gray-600">Total Amount</label>
                            <div class="font-medium">{{ getCurrency() }} {{ numberFormat(selectedPO.total_amount) }}</div>
                        </div>
                        <div>
                            <label class="text-sm text-gray-600">Paid Amount</label>
                            <div class="font-medium">{{ getCurrency() }} {{ numberFormat(selectedPO.paid_amount || 0) }}</div>
                        </div>
                        <div v-if="getDueAmount(selectedPO) > 0">
                            <label class="text-sm text-gray-600">Due Amount</label>
                            <div class="font-medium text-red-600">{{ getCurrency() }} {{ numberFormat(getDueAmount(selectedPO)) }}</div>
                        </div>
                    </div>
                    
                    <div v-if="selectedPO.notes">
                        <label class="text-sm text-gray-600">Notes</label>
                        <div class="mt-1 p-3 bg-gray-50 rounded">{{ selectedPO.notes }}</div>
                    </div>
                    
                    <div v-if="selectedPO.created_by_user">
                        <label class="text-sm text-gray-600">Created By</label>
                        <div class="font-medium">{{ selectedPO.created_by_user.name }}</div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <Button @click="showDetailsModal = false" variant="secondary">
                        Close
                    </Button>
                </div>
            </div>
        </Modal>

        <!-- Receive Modal -->
        <Modal :show="showReceiveModal" @close="showReceiveModal = false">
            <div class="p-6" v-if="selectedPO">
                <h3 class="text-lg font-semibold mb-4">Receive Purchase Order</h3>
                
                <div class="mb-4">
                    <p class="text-gray-700">
                        Are you sure you want to receive PO <strong>{{ selectedPO.order_number }}</strong>?
                    </p>
                    <p class="text-sm text-gray-600 mt-2">
                        This will update the stock quantities for all items in this purchase order.
                    </p>
                </div>

                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                This action cannot be undone. Stock quantities will be automatically updated.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-2">
                    <Button @click="showReceiveModal = false" variant="secondary">
                        Cancel
                    </Button>
                    <Button @click="receivePO" variant="success">
                        <i class="fas fa-check mr-2"></i>
                        Confirm Receive
                    </Button>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
