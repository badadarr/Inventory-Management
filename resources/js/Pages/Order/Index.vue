<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import CardTable from "@/Components/Cards/CardTable.vue";
import TableData from "@/Components/TableData.vue";
import Button from "@/Components/Button.vue";
import InputError from "@/Components/InputError.vue";
import Modal from "@/Components/Modal.vue";
import {useForm} from '@inertiajs/vue3';
import {ref, onMounted} from 'vue';
import {formatDatetime, getCurrency, numberFormat, showToast, truncateString} from "@/Utils/Helper.js";
import TableHead from "@/Components/TableHead.vue";
import {usePage} from "@inertiajs/vue3";

// Show toast on page load if there's a flash message
onMounted(() => {
    const flash = usePage().props.flash;
    if (flash && flash.message) {
        const type = flash.isSuccess === false ? 'error' : 'success';
        showToast(flash.message, type);
    }
});

defineProps({
    filters: {
        type: Object
    },
    orders: {
        type: Object
    },
    orderPaidByTypes: {
        type: Object
    },
});

const selectedOrder = ref(null);
const showOrderItemsModal = ref(false);
const showPaymentModal = ref(false);
const showSettleModal = ref(false);
const tableHeads = ref(["Order Number", "Customer", "Sales Person", "Tanggal PO", "Summary(" + getCurrency() + ")", "Paid", "Due", "Status", "Date", "Action"]);

const form = useForm({
    amount: null,
    paid_through: 'cash',
});

const viewOrderItemsModal = (order) => {
    console.log('Order data:', order);
    console.log('Order items:', order?.orderItems || order?.order_items);
    selectedOrder.value = order;
    showOrderItemsModal.value = true;
};

const payDueOrderModal = (order) => {
    selectedOrder.value = order;
    form.amount = order.due;
    showPaymentModal.value = true;
};
const payOrderDue = () => {
    form.put(route('orders.pay', selectedOrder.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
            showToast();
        },
    });
};

const settleOrderModal = (order) => {
    selectedOrder.value = order;
    showSettleModal.value = true;
};
const settleDuePayment = () => {
    form.put(route('orders.settle', selectedOrder.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
            showToast();
        },
    });
};

const closeModal = () => {
    showOrderItemsModal.value = false;
    showPaymentModal.value = false;
    showSettleModal.value = false;
};
</script>

<template>
    <Head title="Order"/>

    <AuthenticatedLayout>
        <template #breadcrumb>
            Orders
        </template>

        <div class="flex flex-wrap">
            <div class="w-full px-4">
                <CardTable
                    indexRoute="orders.index"
                    :paginatedData="orders"
                    :filters="filters"
                    :tableHeads="tableHeads"
                >
                    <template #cardHeader>
                        <div class="flex justify-between items-center">
                            <h4 class="text-2xl">Apply filters({{orders.total}})</h4>
                            <div class="flex gap-2">
                                <Button
                                    :href="route('orders.create')"
                                    buttonType="link"
                                    class="bg-emerald-500 hover:bg-emerald-600"
                                >
                                    <i class="fa fa-plus mr-2"></i>Create Order
                                </Button>
                                <Button
                                    :href="route('carts.index')"
                                    buttonType="link"
                                    class="bg-blue-500 hover:bg-blue-600"
                                >
                                    <i class="fa fa-shopping-cart mr-2"></i>POS / Cart
                                </Button>
                            </div>
                        </div>
                    </template>

                    <tr v-for="(order, index) in orders.data" :key="order.id">
                        <TableData>
                            <strong>#{{ order.order_number }}</strong>
                        </TableData>
                        <TableData>{{ order.customer ? order.customer.name : 'Walk-in' }}</TableData>
                        <TableData>{{ order.sales ? order.sales.name : '-' }}</TableData>
                        <TableData>{{ order.tanggal_po || '-' }}</TableData>
                        <TableData class="text-start">
                            <span>Sub Total: {{ numberFormat(order.sub_total) }}</span><br>
                            <span>Tax: {{ numberFormat(order.tax_total) }}</span><br>
                            <span>Discount: {{ numberFormat(order.discount_total) }}</span><br>
                            <span class="font-bold">Total: {{ getCurrency() }}{{ numberFormat(order.total) }}</span><br>
                        </TableData>
                        <TableData>{{ getCurrency() }}{{ numberFormat(order.paid) }}</TableData>
                        <TableData>
                            <span :class="order.due > 0 ? 'text-red-500 text-xl font-bold' : ''">{{ getCurrency() }}{{ numberFormat(order.due) }}</span>
                            <br>
                            <div class="flex gap-1" v-if="order.due > 0">
                                <Button
                                    @click="payDueOrderModal(order)"
                                    title="Pay Due"
                                    class="px-2"
                                >
                                    <i class="fa fa-money-bill-wave"></i>
                                </Button>
                                <Button
                                    @click="settleOrderModal(order)"
                                    type="red"
                                    class="px-2"
                                    title="Settle"
                                >
                                    <i class="fa fa-handshake"></i>
                                </Button>
                            </div>
                        </TableData>
                        <TableData>
                            <span v-if="order.status === 'completed'" class="text-xs font-semibold inline-block py-1 px-2 rounded text-emerald-600 bg-emerald-200 uppercase">
                                <i class="fas fa-check-circle mr-1"></i>Completed
                            </span>
                            <span v-else-if="order.status === 'pending'" class="text-xs font-semibold inline-block py-1 px-2 rounded text-amber-600 bg-amber-200 uppercase">
                                <i class="fas fa-clock mr-1"></i>Pending
                            </span>
                            <span v-else-if="order.status === 'cancelled'" class="text-xs font-semibold inline-block py-1 px-2 rounded text-red-600 bg-red-200 uppercase">
                                <i class="fas fa-times-circle mr-1"></i>Cancelled
                            </span>
                            <span v-else class="text-xs font-semibold inline-block py-1 px-2 rounded text-gray-600 bg-gray-200 uppercase">{{ order.status }}</span>
                        </TableData>
                        <TableData>{{ formatDatetime(order.created_at) }}</TableData>
                        <TableData>
                            <div class="flex gap-2">
                                <Button 
                                    @click="viewOrderItemsModal(order)" 
                                    title="View Order Items"
                                    class="bg-blue-500 hover:bg-blue-600"
                                >
                                    <i class="fa fa-list"></i>
                                </Button>
                                <Button 
                                    :href="route('orders.edit', order.id)"
                                    buttonType="link"
                                    title="Edit Order"
                                    class="bg-emerald-500 hover:bg-emerald-600"
                                >
                                    <i class="fa fa-edit"></i>
                                </Button>
                            </div>
                        </TableData>
                    </tr>
                </CardTable>
            </div>
        </div>

        <!--Show order items data-->
        <Modal
            :title="'Order Items(' + (selectedOrder?.orderItems?.length || selectedOrder?.order_items?.length || 0) + ')'"
            :show="showOrderItemsModal"
            @close="closeModal"
            maxWidth="4xl"
            :showSubmitButton="false"
        >
            <div class="relative flex flex-col min-w-0 break-words w-full mb-6 rounded bg-white">
                <div class="block w-full overflow-x-auto">
                    <!-- Projects table -->
                    <table class="items-center w-full bg-transparent border-collapse">
                        <thead>
                        <tr>
                            <TableHead>Product Name</TableHead>
                            <TableHead>Product Number</TableHead>
                            <TableHead>Product Code</TableHead>
                            <TableHead>Price</TableHead>
                            <TableHead>Quantity</TableHead>
                            <TableHead>Action</TableHead>
                        </tr>
                        </thead>
                        <tbody>

                        <template v-for="(orderItem, index) in selectedOrder?.orderItems || selectedOrder?.order_items || []" :key="orderItem?.id || index">
                            <tr v-if="orderItem">
                                <TableData class="text-left flex items-center" :title="orderItem.product?.name || 'N/A'">
                                    <img
                                        :src="orderItem.product?.photo || 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22100%22 height=%22100%22%3E%3Crect fill=%22%23ddd%22 width=%22100%22 height=%22100%22/%3E%3Ctext fill=%22%23999%22 x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22 font-size=%2218%22%3ENo Image%3C/svg%3E'"
                                        class="h-12 w-12 bg-white rounded-full border object-cover"
                                        alt="Product image"
                                        @error="$event.target.style.display='none'"
                                    />
                                    <span class="ml-3 font-bold text-blueGray-600">{{ orderItem.product?.name ? truncateString(orderItem.product.name, 15) : 'N/A' }}</span>
                                </TableData>
                                <TableData>{{ orderItem.product?.product_number || '-' }}</TableData>
                                <TableData>{{ orderItem.product?.product_code || '-' }}</TableData>
                                <TableData>
                                    Unit Price: <strong>{{ getCurrency() }}{{ numberFormat(orderItem.unit_price || 0) }}</strong>
                                    <br>
                                    Subtotal: <strong>{{getCurrency() }}{{ numberFormat(orderItem.subtotal || 0) }}</strong>
                                </TableData>
                                <TableData>
                                    <strong>{{ numberFormat(orderItem.quantity) }}{{ orderItem.product?.unit_type?.symbol || '' }}</strong>
                                </TableData>
                                <TableData>
                                    <Button
                                        v-if="orderItem.product_id"
                                        :href="route('products.edit', orderItem.product_id)"
                                        buttonType="link"
                                        preserveScroll
                                        title="View Product"
                                    >
                                        <i class="fa fa-eye"></i>
                                    </Button>
                                    <span v-else class="text-gray-400 text-sm">Product deleted</span>
                                </TableData>
                            </tr>
                        </template>

                        </tbody>
                    </table>
                </div>
            </div>
        </Modal>

        <!--Pay due-->
        <Modal
            title="Pay Due"
            :show="showPaymentModal"
            :formProcessing="form.processing"
            @close="closeModal"
            @submitAction="payOrderDue"
            maxWidth="sm"
        >
            <div>
                <div class="flex mt-1">
                    <select
                        id="paid_through"
                        v-model="form.paid_through"
                        class="w-1/2 rounded-l-md bg-gray-300 border-none px-2 py-2 outline-none focus:outline-none"
                    >
                        <option
                            v-for="(orderPaidByType, index) in orderPaidByTypes"
                            :key="index"
                            :value="orderPaidByType.value"
                        >
                            {{ orderPaidByType.label }}
                        </option>
                    </select>
                    <input
                        id="paid"
                        placeholder="Enter paid amount"
                        v-model="form.amount"
                        @keyup.enter="payOrderDue"
                        type="text"
                        class="w-full rounded-r-md border border-gray-200 px-2 py-2 shadow-sm outline-none focus:outline-none focus:shadow-outline"
                    />
                </div>
                <InputError :message="form.errors.amount"/>
            </div>
        </Modal>

        <!--Settle Order-->
        <Modal
            title="Due Settlement"
            :show="showSettleModal"
            :formProcessing="form.processing"
            @close="closeModal"
            @submitAction="settleDuePayment"
            maxWidth="md"
            submitButtonText="Yes, settle it!"
        >
            Are you sure you want to settle this due payment?
            <br>
            <br>
            <strong>Note: </strong>The due amount will be applied as discount.
        </Modal>
    </AuthenticatedLayout>
</template>
