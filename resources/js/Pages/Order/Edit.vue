<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import InputError from "@/Components/InputError.vue";
import { useForm } from '@inertiajs/vue3';
import { ref, computed, watch, onMounted } from 'vue';
import Button from "@/Components/Button.vue";
import SubmitButton from "@/Components/SubmitButton.vue";
import { showToast, getCurrency, numberFormat } from "@/Utils/Helper.js";

const props = defineProps({
    order: {
        type: Object,
        required: true
    },
    customers: {
        type: Array,
        default: () => []
    },
    salesList: {
        type: Array,
        default: () => []
    },
    products: {
        type: Array,
        default: () => []
    },
});

// Helper function to format date from timestamp to yyyy-MM-dd
const formatDateForInput = (timestamp) => {
    if (!timestamp) return null;
    // Convert "2025-10-11T00:00:00.000000Z" to "2025-10-11"
    return timestamp.split('T')[0];
};

const form = useForm({
    // Order Information
    customer_id: props.order.customer_id,
    sales_id: props.order.sales_id,
    tanggal_po: formatDateForInput(props.order.tanggal_po),
    tanggal_kirim: formatDateForInput(props.order.tanggal_kirim),

    // Material Details
    jenis_bahan: props.order.jenis_bahan,
    gramasi: props.order.gramasi,
    volume: props.order.volume,

    // Pricing
    harga_jual_pcs: props.order.harga_jual_pcs,
    jumlah_cetak: props.order.jumlah_cetak,

    // Order Items - will be populated from existing order items
    order_items: [],

    // Payment
    paid: props.order.paid || 0,
    paid_through: props.order.transactions?.[0]?.paid_through || 'cash',
    custom_discount: null,

    // Additional Info
    catatan: props.order.catatan,
});

// Load existing order items on mount - moved to loadCustomPrices section below
// to ensure we load items first, then apply custom prices

// Order Items Management
const selectedProduct = ref(null);
const itemQuantity = ref(1);
const itemPrice = ref(0);
const customPrices = ref({});  // Store custom prices: { product_id: custom_price }
const loadingCustomPrices = ref(false);

const addOrderItem = () => {
    if (!selectedProduct.value) {
        showToast('Please select a product', 'warning');
        return;
    }

    // Check if product already in cart
    const existingIndex = form.order_items.findIndex(
        item => item.product_id === selectedProduct.value.id
    );

    if (existingIndex >= 0) {
        // Update quantity if exists (ensure it's a number)
        form.order_items[existingIndex].quantity = parseInt(form.order_items[existingIndex].quantity) + parseInt(itemQuantity.value);
        form.order_items[existingIndex].subtotal =
            form.order_items[existingIndex].quantity * parseFloat(form.order_items[existingIndex].price);
    } else {
        // Add new item with proper number types
        const price = parseFloat(itemPrice.value || selectedProduct.value.selling_price);
        const quantity = parseInt(itemQuantity.value);
        form.order_items.push({
            product_id: parseInt(selectedProduct.value.id),
            product_name: selectedProduct.value.name,
            product_code: selectedProduct.value.product_code,
            price: price,
            quantity: quantity,
            subtotal: price * quantity,
            unit_symbol: selectedProduct.value.unit_type?.symbol || ''
        });
    }

    // Reset
    selectedProduct.value = null;
    itemQuantity.value = 1;
    itemPrice.value = 0;
};

const removeOrderItem = (index) => {
    form.order_items.splice(index, 1);
};

const updateItemQuantity = (index, newQuantity) => {
    // Convert to number and ensure minimum value of 1
    const qty = parseInt(newQuantity) || 1;
    if (qty < 1) {
        form.order_items[index].quantity = 1;
    } else {
        form.order_items[index].quantity = qty;
    }
    form.order_items[index].subtotal = form.order_items[index].price * form.order_items[index].quantity;
};

const updateItemPrice = (index, newPrice) => {
    // Convert to number and ensure minimum value of 0
    const price = parseFloat(newPrice) || 0;
    if (price < 0) {
        form.order_items[index].price = 0;
    } else {
        form.order_items[index].price = price;
    }
    form.order_items[index].subtotal = form.order_items[index].price * form.order_items[index].quantity;
};

// Load custom prices on mount and when customer changes
const loadCustomPrices = async () => {
    if (form.customer_id) {
        loadingCustomPrices.value = true;
        try {
            const response = await axios.get(route('orders.custom-prices', form.customer_id));
            if (response.data.success) {
                customPrices.value = response.data.data;

                // Update existing order items with custom prices
                form.order_items.forEach(item => {
                    if (customPrices.value[item.product_id]) {
                        item.price = customPrices.value[item.product_id];
                        item.subtotal = item.price * item.quantity;
                    }
                });
            }
        } catch (error) {
            console.error('Failed to load custom prices:', error);
        } finally {
            loadingCustomPrices.value = false;
        }
    } else {
        customPrices.value = {};
    }
};

// Load custom prices and order items on mount
onMounted(() => {
    // First, load existing order items with proper number types
    if (props.order.orderItems || props.order.order_items) {
        const existingItems = props.order.orderItems || props.order.order_items;
        form.order_items = existingItems.map(item => ({
            product_id: parseInt(item.product_id),
            product_name: item.product?.name || 'Unknown Product',
            product_code: item.product?.product_code || '-',
            price: parseFloat(item.unit_price || item.product?.selling_price || 0),
            quantity: parseInt(item.quantity || 1),
            subtotal: parseFloat((item.unit_price || 0) * (item.quantity || 0)),
            unit_symbol: item.product?.unit_type?.symbol || item.product?.unitType?.symbol || ''
        }));
    }

    // Then load custom prices (which will update prices if custom prices exist)
    loadCustomPrices();
});

// Watch customer selection to reload custom prices
watch(() => form.customer_id, () => {
    loadCustomPrices();
});

// Watch selected product to auto-fill price
watch(selectedProduct, (product) => {
    if (product) {
        // Check for custom pricing first
        const productId = product.id;
        if (customPrices.value[productId]) {
            itemPrice.value = customPrices.value[productId];
        } else {
            itemPrice.value = product.selling_price;
        }
    }
});

// Calculations
const subTotal = computed(() => {
    return form.order_items.reduce((sum, item) => sum + item.subtotal, 0);
});

const taxAmount = computed(() => {
    // 0% tax for now, can be configured
    return 0;
});

const grandTotal = computed(() => {
    return subTotal.value + taxAmount.value;
});

const dueAmount = computed(() => {
    return Math.max(grandTotal.value - form.paid, 0);
});

const updateOrder = () => {
    // Validate order items
    if (form.order_items.length === 0) {
        showToast('Please add at least one product to the order', 'warning');
        return;
    }

    // Ensure all numeric fields are properly typed
    const payload = {
        customer_id: form.customer_id ? parseInt(form.customer_id) : null,
        sales_id: form.sales_id ? parseInt(form.sales_id) : null,
        tanggal_po: form.tanggal_po,
        tanggal_kirim: form.tanggal_kirim,
        jenis_bahan: form.jenis_bahan,
        gramasi: form.gramasi,
        volume: form.volume ? parseInt(form.volume) : null,
        harga_jual_pcs: form.harga_jual_pcs ? parseFloat(form.harga_jual_pcs) : null,
        jumlah_cetak: form.jumlah_cetak ? parseInt(form.jumlah_cetak) : null,
        catatan: form.catatan,
        paid: form.paid ? parseFloat(form.paid) : 0,
        paid_through: form.paid_through,
        custom_discount: form.custom_discount,
        order_items: form.order_items.map(item => ({
            product_id: parseInt(item.product_id),
            quantity: parseInt(item.quantity),
            price: parseFloat(item.price)
        }))
    };

    // Submit form with properly typed data
    form.transform(() => payload).put(route('orders.update', props.order.id), {
        preserveScroll: true,
        onError: (errors) => {
            // Show validation errors if any
            if (Object.keys(errors).length > 0) {
                const errorMessages = Object.values(errors).flat();
                showToast('Validation Error: ' + errorMessages.join(', '), 'error');
            }
        },
    });
};
</script>

<template>

    <Head title="Edit Order" />

    <AuthenticatedLayout>
        <template #breadcrumb>
            <a href="/orders" class="text-emerald-600 hover:text-emerald-700">Orders</a>
            <span class="mx-2">/</span>
            <span>Edit Order #{{ order.order_number }}</span>
        </template>

        <div class="flex flex-wrap">
            <div class="w-full px-4">
                <div
                    class="relative -mt-16 flex flex-col min-w-0 break-words w-full mb-6 shadow-lg rounded-lg bg-white">
                    <div class="rounded-t mb-0 px-6 py-6 border-b">
                        <div class="flex items-center justify-between">
                            <h3 class="font-bold text-xl text-blueGray-700">
                                Edit Order #{{ order.order_number }}
                            </h3>
                            <div class="text-sm">
                                <span class="text-gray-500">Status:</span>
                                <span class="ml-2 px-3 py-1 rounded-full text-xs font-semibold" :class="{
                                    'bg-emerald-100 text-emerald-600': order.status === 'paid',
                                    'bg-yellow-100 text-yellow-600': order.status === 'partial',
                                    'bg-red-100 text-red-600': order.status === 'pending'
                                }">
                                    {{ order.status?.toUpperCase() }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="flex-auto px-6 py-6">
                        <form @submit.prevent="updateOrder">

                            <!-- Section 1: Order Information -->
                            <div class="mb-8 p-6 border-2 border-emerald-200 rounded-lg bg-emerald-50">
                                <h4 class="text-lg font-semibold text-emerald-700 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                    1. Order Information
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="flex flex-col">
                                        <label for="customer_id" class="text-stone-600 text-sm font-medium">
                                            Customer <span class="text-red-500">*</span>
                                        </label>
                                        <select id="customer_id" v-model="form.customer_id"
                                            class="mt-2 block w-full rounded-md border border-gray-200 px-3 py-2 shadow-sm outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"
                                            required>
                                            <option :value="null">Select Customer</option>
                                            <option v-for="customer in customers" :key="customer.id"
                                                :value="customer.id">
                                                {{ customer.name }}
                                            </option>
                                        </select>
                                        <InputError :message="form.errors.customer_id" />
                                    </div>

                                    <div class="flex flex-col">
                                        <label for="sales_id" class="text-stone-600 text-sm font-medium">
                                            Sales Person
                                        </label>
                                        <select id="sales_id" v-model="form.sales_id"
                                            class="mt-2 block w-full rounded-md border border-gray-200 px-3 py-2 shadow-sm outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                                            <option :value="null">Select Sales Person</option>
                                            <option v-for="sales in salesList" :key="sales.id" :value="sales.id">
                                                {{ sales.name }}
                                            </option>
                                        </select>
                                        <InputError :message="form.errors.sales_id" />
                                    </div>

                                    <div class="flex flex-col">
                                        <label for="tanggal_po" class="text-stone-600 text-sm font-medium">
                                            Tanggal PO
                                        </label>
                                        <input id="tanggal_po" v-model="form.tanggal_po" type="date"
                                            class="mt-2 block w-full rounded-md border border-gray-200 px-3 py-2 shadow-sm outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500" />
                                        <InputError :message="form.errors.tanggal_po" />
                                    </div>

                                    <div class="flex flex-col">
                                        <label for="tanggal_kirim" class="text-stone-600 text-sm font-medium">
                                            Tanggal Kirim
                                        </label>
                                        <input id="tanggal_kirim" v-model="form.tanggal_kirim" type="date"
                                            class="mt-2 block w-full rounded-md border border-gray-200 px-3 py-2 shadow-sm outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500" />
                                        <InputError :message="form.errors.tanggal_kirim" />
                                    </div>
                                </div>
                            </div>

                            <!-- Section 2: Material Details -->
                            <div class="mb-8 p-6 border-2 border-blue-200 rounded-lg bg-blue-50">
                                <h4 class="text-lg font-semibold text-blue-700 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                        </path>
                                    </svg>
                                    2. Material Details
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="flex flex-col">
                                        <label for="jenis_bahan" class="text-stone-600 text-sm font-medium">
                                            Jenis Bahan
                                        </label>
                                        <input id="jenis_bahan" v-model="form.jenis_bahan" type="text"
                                            placeholder="e.g. A4, F4, Ivory"
                                            class="mt-2 block w-full rounded-md border border-gray-200 px-3 py-2 shadow-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" />
                                        <InputError :message="form.errors.jenis_bahan" />
                                    </div>

                                    <div class="flex flex-col">
                                        <label for="gramasi" class="text-stone-600 text-sm font-medium">
                                            Gramasi
                                        </label>
                                        <input id="gramasi" v-model="form.gramasi" type="text"
                                            placeholder="e.g. 80 gsm, 100 gsm"
                                            class="mt-2 block w-full rounded-md border border-gray-200 px-3 py-2 shadow-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" />
                                        <InputError :message="form.errors.gramasi" />
                                    </div>

                                    <div class="flex flex-col">
                                        <label for="volume" class="text-stone-600 text-sm font-medium">
                                            Volume (pcs)
                                        </label>
                                        <input id="volume" v-model.number="form.volume" type="number" min="0"
                                            placeholder="0"
                                            class="mt-2 block w-full rounded-md border border-gray-200 px-3 py-2 shadow-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" />
                                        <InputError :message="form.errors.volume" />
                                    </div>
                                </div>
                            </div>

                            <!-- Section 3: Pricing -->
                            <div class="mb-8 p-6 border-2 border-purple-200 rounded-lg bg-purple-50">
                                <h4 class="text-lg font-semibold text-purple-700 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                        </path>
                                    </svg>
                                    3. Pricing Information
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="flex flex-col">
                                        <label for="harga_jual_pcs" class="text-stone-600 text-sm font-medium">
                                            Harga Jual /pcs ({{ getCurrency() }})
                                        </label>
                                        <input id="harga_jual_pcs" v-model.number="form.harga_jual_pcs" type="number"
                                            min="0" step="0.01" placeholder="0"
                                            class="mt-2 block w-full rounded-md border border-gray-200 px-3 py-2 shadow-sm outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500" />
                                        <InputError :message="form.errors.harga_jual_pcs" />
                                    </div>

                                    <div class="flex flex-col">
                                        <label for="jumlah_cetak" class="text-stone-600 text-sm font-medium">
                                            Jumlah Cetak
                                        </label>
                                        <input id="jumlah_cetak" v-model.number="form.jumlah_cetak" type="number"
                                            min="0" placeholder="0"
                                            class="mt-2 block w-full rounded-md border border-gray-200 px-3 py-2 shadow-sm outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500" />
                                        <InputError :message="form.errors.jumlah_cetak" />
                                    </div>
                                </div>
                            </div>

                            <!-- Section 4: Order Items -->
                            <div class="mb-8 p-6 border-2 border-orange-200 rounded-lg bg-orange-50">
                                <h4 class="text-lg font-semibold text-orange-700 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                                        </path>
                                    </svg>
                                    4. Order Items <span class="text-red-500 ml-1">*</span>
                                </h4>

                                <!-- Add Product Form -->
                                <div class="mb-4 p-4 bg-white rounded-lg border border-orange-300">
                                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
                                        <div class="md:col-span-1 flex flex-col">
                                            <label class="text-sm font-medium text-gray-700 mb-1">Product</label>
                                            <select v-model="selectedProduct"
                                                class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-500">
                                                <option :value="null">Select Product</option>
                                                <option v-for="product in products" :key="product.id" :value="product">
                                                    {{ product.name }} ({{ product.product_code }})
                                                </option>
                                            </select>
                                        </div>

                                        <div class="flex flex-col">
                                            <label class="text-sm font-medium text-gray-700 mb-1">Quantity</label>
                                            <input v-model.number="itemQuantity" type="number" min="1"
                                                class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-500" />
                                        </div>

                                        <div class="flex flex-col">
                                            <label class="text-sm font-medium text-gray-700 mb-1">Price ({{
                                                getCurrency()
                                            }})</label>
                                            <input v-model.number="itemPrice" type="number" min="0" step="0.01"
                                                class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-500" />
                                        </div>

                                        <div>
                                            <Button type="button" @click="addOrderItem"
                                                class="w-full bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                                                + Add Item
                                            </Button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Order Items Table -->
                                <div class="overflow-x-auto">
                                    <table class="min-w-full bg-white border border-orange-300 rounded-lg">
                                        <thead class="bg-orange-100">
                                            <tr>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-semibold text-orange-800 uppercase">
                                                    #</th>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-semibold text-orange-800 uppercase">
                                                    Product</th>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-semibold text-orange-800 uppercase">
                                                    Code</th>
                                                <th
                                                    class="px-4 py-3 text-center text-xs font-semibold text-orange-800 uppercase">
                                                    Price</th>
                                                <th
                                                    class="px-4 py-3 text-center text-xs font-semibold text-orange-800 uppercase">
                                                    Quantity</th>
                                                <th
                                                    class="px-4 py-3 text-right text-xs font-semibold text-orange-800 uppercase">
                                                    Subtotal</th>
                                                <th
                                                    class="px-4 py-3 text-center text-xs font-semibold text-orange-800 uppercase">
                                                    Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-orange-200">
                                            <tr v-if="form.order_items.length === 0">
                                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                                    No items added yet. Please add at least one product.
                                                </td>
                                            </tr>
                                            <tr v-for="(item, index) in form.order_items" :key="index"
                                                class="hover:bg-orange-50">
                                                <td class="px-4 py-3 text-sm">{{ index + 1 }}</td>
                                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{
                                                    item.product_name }}
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-600">{{ item.product_code }}</td>
                                                <td class="px-4 py-3 text-center">
                                                    <div class="flex items-center justify-center gap-2">
                                                        <input type="number" :value="item.price"
                                                            @input="updateItemPrice(index, parseFloat($event.target.value))"
                                                            min="0" step="0.01"
                                                            class="w-24 px-2 py-1 text-sm text-center border border-gray-300 rounded focus:border-orange-500 focus:ring-1 focus:ring-orange-500" />
                                                        <span v-if="customPrices[item.product_id]"
                                                            class="px-2 py-1 text-xs bg-emerald-100 text-emerald-700 rounded-full whitespace-nowrap"
                                                            title="Custom price for this customer">
                                                            <i class="fa fa-star"></i> Custom
                                                        </span>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <input type="number" :value="item.quantity"
                                                        @input="updateItemQuantity(index, parseInt($event.target.value))"
                                                        min="1"
                                                        class="w-20 px-2 py-1 text-sm text-center border border-gray-300 rounded focus:border-orange-500 focus:ring-1 focus:ring-orange-500" />
                                                    <span class="ml-1 text-xs text-gray-500">{{ item.unit_symbol
                                                    }}</span>
                                                </td>
                                                <td class="px-4 py-3 text-right text-sm font-semibold text-gray-900">
                                                    {{ getCurrency() }} {{ numberFormat(item.subtotal) }}
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <button type="button" @click="removeOrderItem(index)"
                                                        class="text-red-600 hover:text-red-800 font-medium text-sm">
                                                        Remove
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <InputError :message="form.errors.order_items" />
                            </div>

                            <!-- Section 5: Payment & Summary -->
                            <div class="mb-8 p-6 border-2 border-pink-200 rounded-lg bg-pink-50">
                                <h4 class="text-lg font-semibold text-pink-700 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                                        </path>
                                    </svg>
                                    5. Payment & Notes
                                </h4>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Left: Payment Details -->
                                    <div class="space-y-4">
                                        <div class="flex flex-col">
                                            <label for="paid" class="text-stone-600 text-sm font-medium">
                                                Paid Amount ({{ getCurrency() }})
                                            </label>
                                            <input id="paid" v-model.number="form.paid" type="number" min="0"
                                                step="0.01" placeholder="0"
                                                class="mt-2 block w-full rounded-md border border-gray-200 px-3 py-2 shadow-sm outline-none focus:border-pink-500 focus:ring-1 focus:ring-pink-500" />
                                            <InputError :message="form.errors.paid" />
                                        </div>

                                        <div class="flex flex-col">
                                            <label for="paid_through" class="text-stone-600 text-sm font-medium">
                                                Payment Method
                                            </label>
                                            <select id="paid_through" v-model="form.paid_through"
                                                class="mt-2 block w-full rounded-md border border-gray-200 px-3 py-2 shadow-sm outline-none focus:border-pink-500 focus:ring-1 focus:ring-pink-500">
                                                <option value="cash">Cash</option>
                                                <option value="bank_transfer">Bank Transfer</option>
                                                <option value="credit_card">Credit Card</option>
                                                <option value="debit_card">Debit Card</option>
                                                <option value="e_wallet">E-Wallet (OVO, GoPay, Dana, dll)</option>
                                                <option value="qris">QRIS</option>
                                                <option value="gift_card">Gift Card</option>
                                            </select>
                                            <InputError :message="form.errors.paid_through" />
                                        </div>

                                        <div class="flex flex-col">
                                            <label for="catatan" class="text-stone-600 text-sm font-medium">
                                                Catatan / Notes
                                            </label>
                                            <textarea id="catatan" v-model="form.catatan" rows="4"
                                                placeholder="Additional notes..."
                                                class="mt-2 block w-full rounded-md border border-gray-200 px-3 py-2 shadow-sm outline-none focus:border-pink-500 focus:ring-1 focus:ring-pink-500"></textarea>
                                            <InputError :message="form.errors.catatan" />
                                        </div>
                                    </div>

                                    <!-- Right: Order Summary -->
                                    <div class="bg-white p-6 rounded-lg border-2 border-pink-300 shadow-sm">
                                        <h5 class="text-lg font-semibold text-gray-800 mb-4">Order Summary</h5>
                                        <div class="space-y-3">
                                            <div class="flex justify-between items-center text-sm">
                                                <span class="text-gray-600">Subtotal:</span>
                                                <span class="font-semibold text-gray-900">{{ getCurrency() }} {{
                                                    numberFormat(subTotal) }}</span>
                                            </div>
                                            <div class="flex justify-between items-center text-sm">
                                                <span class="text-gray-600">Tax:</span>
                                                <span class="font-semibold text-gray-900">{{ getCurrency() }} {{
                                                    numberFormat(taxAmount) }}</span>
                                            </div>
                                            <div
                                                class="border-t border-gray-300 pt-3 flex justify-between items-center">
                                                <span class="text-lg font-bold text-gray-900">Grand Total:</span>
                                                <span class="text-xl font-bold text-pink-600">{{ getCurrency() }} {{
                                                    numberFormat(grandTotal) }}</span>
                                            </div>
                                            <div
                                                class="flex justify-between items-center text-sm pt-2 border-t border-gray-200">
                                                <span class="text-gray-600">Paid:</span>
                                                <span class="font-semibold text-emerald-600">{{ getCurrency() }} {{
                                                    numberFormat(form.paid) }}</span>
                                            </div>
                                            <div class="flex justify-between items-center text-sm">
                                                <span class="text-gray-600">Due:</span>
                                                <span class="font-semibold"
                                                    :class="dueAmount > 0 ? 'text-red-600' : 'text-emerald-600'">
                                                    {{ getCurrency() }} {{ numberFormat(dueAmount) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="flex items-center justify-end gap-3 pt-4 border-t">
                                <a href="/orders"
                                    class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition duration-150">
                                    Cancel
                                </a>
                                <SubmitButton :processing="form.processing"
                                    class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg shadow-md transition duration-150 disabled:opacity-50">
                                    <svg class="w-5 h-5 mr-2 inline-block" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Update Order
                                </SubmitButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
