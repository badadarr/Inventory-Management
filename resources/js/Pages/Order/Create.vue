<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import InputError from "@/Components/InputError.vue";
import {useForm} from '@inertiajs/vue3';
import {ref, computed, watch} from 'vue';
import Button from "@/Components/Button.vue";
import SubmitButton from "@/Components/SubmitButton.vue";
import {showToast, getCurrency, numberFormat} from "@/Utils/Helper.js";

const props = defineProps({
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

const form = useForm({
    // Order Information
    customer_id: null,
    sales_id: null,
    tanggal_po: null,
    tanggal_kirim: null,
    
    // Material Details
    jenis_bahan: null,
    gramasi: null,
    volume: null,
    
    // Pricing
    harga_jual_pcs: null,
    jumlah_cetak: null,
    
    // Order Items
    order_items: [],
    
    // Payment
    paid: 0,
    paid_through: 'cash',
    custom_discount: null,
    
    // Additional Info
    catatan: null,
});

// Order Items Management
const selectedProduct = ref(null);
const itemQuantity = ref(1);
const itemPrice = ref(0);

const addOrderItem = () => {
    if (!selectedProduct.value) {
        showToast('Please select a product', 'warning');
        return;
    }
    
    const product = props.products.find(p => p.id === parseInt(selectedProduct.value));
    if (!product) return;
    
    // Check if product already in list
    const existingIndex = form.order_items.findIndex(item => item.product_id === product.id);
    
    if (existingIndex >= 0) {
        // Update quantity
        form.order_items[existingIndex].quantity += itemQuantity.value;
        form.order_items[existingIndex].subtotal = form.order_items[existingIndex].quantity * form.order_items[existingIndex].price;
    } else {
        // Add new item
        form.order_items.push({
            product_id: product.id,
            product_name: product.name,
            product_code: product.product_code,
            price: itemPrice.value || product.selling_price,
            quantity: itemQuantity.value,
            subtotal: (itemPrice.value || product.selling_price) * itemQuantity.value,
            unit_symbol: product.unit_type?.symbol || '',
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

// Price calculation when product selected
watch(selectedProduct, (newVal) => {
    if (newVal) {
        const product = props.products.find(p => p.id === parseInt(newVal));
        if (product) {
            // TODO: Check for custom pricing here
            // For now, use selling price
            itemPrice.value = product.selling_price;
        }
    }
});

// Calculated totals
const subTotal = computed(() => {
    return form.order_items.reduce((sum, item) => sum + item.subtotal, 0);
});

const taxTotal = computed(() => {
    // Calculate tax if needed (0% for now)
    return 0;
});

const discountTotal = computed(() => {
    // Calculate discount if needed (0 for now)
    return 0;
});

const grandTotal = computed(() => {
    return subTotal.value + taxTotal.value - discountTotal.value;
});

const dueAmount = computed(() => {
    return Math.max(grandTotal.value - form.paid, 0);
});

const createOrder = () => {
    // Validate order items
    if (form.order_items.length === 0) {
        showToast('Please add at least one product to the order', 'warning');
        return;
    }
    
    // Submit form
    form.post(route('orders.store'), {
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
    <Head title="Create Order"/>

    <AuthenticatedLayout>
        <template #breadcrumb>
            Orders > Create
        </template>

        <div class="flex flex-wrap">
            <div class="w-full px-4">
                <div class="relative flex flex-col min-w-0 break-words w-full mb-6 shadow-lg rounded bg-white">
                    <div class="rounded-t mb-3 px-4 py-3 border-0">
                        <div class="flex flex-wrap items-center">
                            <div class="relative w-full px-4 max-w-full flex-grow flex-1">
                                <div class="flex justify-between items-center">
                                    <h4 class="text-2xl font-semibold">Create Order</h4>
                                    <Button
                                        :href="route('orders.index')"
                                        buttonType="link"
                                    >
                                        Go Back
                                    </Button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="block w-full overflow-x-auto px-8 py-4">
                        <form @submit.prevent="createOrder">
                            
                            <!-- Section 1: Order Information -->
                            <div class="mb-8">
                                <h5 class="text-lg font-semibold text-gray-700 mb-4 pb-2 border-b-2 border-emerald-500">
                                    <i class="fa fa-file-alt mr-2"></i>Order Information
                                </h5>
                                <div class="grid gap-4 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
                                    <div class="flex flex-col">
                                        <label for="customer_id" class="text-stone-600 text-sm font-medium">
                                            Customer
                                        </label>
                                        <select
                                            id="customer_id"
                                            v-model="form.customer_id"
                                            class="mt-2 block w-full rounded-md border border-gray-200 px-3 py-2 shadow-sm outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"
                                        >
                                            <option :value="null">Select Customer (Walk-in)</option>
                                            <option v-for="customer in customers" :key="customer.id" :value="customer.id">
                                                {{ customer.name }}
                                            </option>
                                        </select>
                                        <InputError :message="form.errors.customer_id"/>
                                    </div>

                                    <div class="flex flex-col">
                                        <label for="sales_id" class="text-stone-600 text-sm font-medium">
                                            Sales Person
                                        </label>
                                        <select
                                            id="sales_id"
                                            v-model="form.sales_id"
                                            class="mt-2 block w-full rounded-md border border-gray-200 px-3 py-2 shadow-sm outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"
                                        >
                                            <option :value="null">Select Sales Person</option>
                                            <option v-for="sales in salesList" :key="sales.id" :value="sales.id">
                                                {{ sales.name }}
                                            </option>
                                        </select>
                                        <InputError :message="form.errors.sales_id"/>
                                    </div>

                                    <div class="flex flex-col">
                                        <label for="tanggal_po" class="text-stone-600 text-sm font-medium">
                                            Tanggal PO
                                        </label>
                                        <input
                                            id="tanggal_po"
                                            v-model="form.tanggal_po"
                                            type="date"
                                            class="mt-2 block w-full rounded-md border border-gray-200 px-3 py-2 shadow-sm outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"
                                        />
                                        <InputError :message="form.errors.tanggal_po"/>
                                    </div>

                                    <div class="flex flex-col">
                                        <label for="tanggal_kirim" class="text-stone-600 text-sm font-medium">
                                            Tanggal Kirim
                                        </label>
                                        <input
                                            id="tanggal_kirim"
                                            v-model="form.tanggal_kirim"
                                            type="date"
                                            class="mt-2 block w-full rounded-md border border-gray-200 px-3 py-2 shadow-sm outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"
                                        />
                                        <InputError :message="form.errors.tanggal_kirim"/>
                                    </div>
                                </div>
                            </div>

                            <!-- Divider -->
                            <hr class="my-8 border-gray-200"/>

                            <!-- Section 2: Material Details -->
                            <div class="mb-8">
                                <h5 class="text-lg font-semibold text-gray-700 mb-4 pb-2 border-b-2 border-blue-500">
                                    <i class="fa fa-box mr-2"></i>Material Details
                                </h5>
                                <div class="grid gap-4 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
                                    <div class="flex flex-col">
                                        <label for="jenis_bahan" class="text-stone-600 text-sm font-medium">
                                            Jenis Bahan
                                        </label>
                                        <input
                                            id="jenis_bahan"
                                            v-model="form.jenis_bahan"
                                            type="text"
                                            placeholder="e.g., Art Paper, HVS"
                                            class="mt-2 block w-full rounded-md border border-gray-200 px-3 py-2 shadow-sm outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"
                                        />
                                        <InputError :message="form.errors.jenis_bahan"/>
                                    </div>

                                    <div class="flex flex-col">
                                        <label for="gramasi" class="text-stone-600 text-sm font-medium">
                                            Gramasi
                                        </label>
                                        <input
                                            id="gramasi"
                                            v-model="form.gramasi"
                                            type="text"
                                            placeholder="e.g., 150 gsm"
                                            class="mt-2 block w-full rounded-md border border-gray-200 px-3 py-2 shadow-sm outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"
                                        />
                                        <InputError :message="form.errors.gramasi"/>
                                    </div>

                                    <div class="flex flex-col">
                                        <label for="volume" class="text-stone-600 text-sm font-medium">
                                            Volume (Quantity)
                                        </label>
                                        <input
                                            id="volume"
                                            v-model.number="form.volume"
                                            type="number"
                                            placeholder="Enter volume"
                                            class="mt-2 block w-full rounded-md border border-gray-200 px-3 py-2 shadow-sm outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"
                                        />
                                        <InputError :message="form.errors.volume"/>
                                    </div>
                                </div>
                            </div>

                            <!-- Divider -->
                            <hr class="my-8 border-gray-200"/>

                            <!-- Section 3: Pricing Information -->
                            <div class="mb-8">
                                <h5 class="text-lg font-semibold text-gray-700 mb-4 pb-2 border-b-2 border-purple-500">
                                    <i class="fa fa-dollar-sign mr-2"></i>Pricing Information
                                </h5>
                                <div class="grid gap-4 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
                                    <div class="flex flex-col">
                                        <label for="harga_jual_pcs" class="text-stone-600 text-sm font-medium">
                                            Harga Jual per Pcs
                                        </label>
                                        <input
                                            id="harga_jual_pcs"
                                            v-model.number="form.harga_jual_pcs"
                                            type="number"
                                            step="0.01"
                                            placeholder="Enter price per piece"
                                            class="mt-2 block w-full rounded-md border border-gray-200 px-3 py-2 shadow-sm outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"
                                        />
                                        <InputError :message="form.errors.harga_jual_pcs"/>
                                    </div>

                                    <div class="flex flex-col">
                                        <label for="jumlah_cetak" class="text-stone-600 text-sm font-medium">
                                            Jumlah Cetak
                                        </label>
                                        <input
                                            id="jumlah_cetak"
                                            v-model.number="form.jumlah_cetak"
                                            type="number"
                                            placeholder="Enter print quantity"
                                            class="mt-2 block w-full rounded-md border border-gray-200 px-3 py-2 shadow-sm outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"
                                        />
                                        <InputError :message="form.errors.jumlah_cetak"/>
                                    </div>
                                </div>
                            </div>

                            <!-- Divider -->
                            <hr class="my-8 border-gray-200"/>

                            <!-- Section 4: Order Items -->
                            <div class="mb-8">
                                <h5 class="text-lg font-semibold text-gray-700 mb-4 pb-2 border-b-2 border-orange-500">
                                    <i class="fa fa-shopping-cart mr-2"></i>Order Items
                                </h5>
                                
                                <!-- Add Item Form -->
                                <div class="bg-gray-50 p-4 rounded-lg mb-4">
                                    <div class="grid gap-4 sm:grid-cols-1 md:grid-cols-4">
                                        <div class="flex flex-col md:col-span-2">
                                            <label for="product" class="text-stone-600 text-sm font-medium mb-2">
                                                Select Product
                                            </label>
                                            <select
                                                id="product"
                                                v-model="selectedProduct"
                                                class="block w-full rounded-md border border-gray-200 px-3 py-2 shadow-sm outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"
                                            >
                                                <option :value="null">Choose a product</option>
                                                <option v-for="product in products" :key="product.id" :value="product.id">
                                                    {{ product.name }} ({{ product.product_code }})
                                                </option>
                                            </select>
                                        </div>

                                        <div class="flex flex-col">
                                            <label for="quantity" class="text-stone-600 text-sm font-medium mb-2">
                                                Quantity
                                            </label>
                                            <input
                                                id="quantity"
                                                v-model.number="itemQuantity"
                                                type="number"
                                                min="1"
                                                class="block w-full rounded-md border border-gray-200 px-3 py-2 shadow-sm outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"
                                            />
                                        </div>

                                        <div class="flex flex-col">
                                            <label for="price" class="text-stone-600 text-sm font-medium mb-2">
                                                Price
                                            </label>
                                            <input
                                                id="price"
                                                v-model.number="itemPrice"
                                                type="number"
                                                step="0.01"
                                                placeholder="Auto-filled"
                                                class="block w-full rounded-md border border-gray-200 px-3 py-2 shadow-sm outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"
                                            />
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <Button
                                            @click.prevent="addOrderItem"
                                            type="button"
                                            class="bg-emerald-500 hover:bg-emerald-600"
                                        >
                                            <i class="fa fa-plus mr-2"></i>Add Item
                                        </Button>
                                    </div>
                                </div>

                                <!-- Items List -->
                                <div v-if="form.order_items.length > 0" class="overflow-x-auto">
                                    <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                                        <thead class="bg-gray-100">
                                            <tr>
                                                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Product</th>
                                                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Code</th>
                                                <th class="px-4 py-2 text-right text-sm font-semibold text-gray-700">Price</th>
                                                <th class="px-4 py-2 text-right text-sm font-semibold text-gray-700">Quantity</th>
                                                <th class="px-4 py-2 text-right text-sm font-semibold text-gray-700">Subtotal</th>
                                                <th class="px-4 py-2 text-center text-sm font-semibold text-gray-700">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(item, index) in form.order_items" :key="index" class="border-t">
                                                <td class="px-4 py-3 text-sm">{{ item.product_name }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-600">{{ item.product_code }}</td>
                                                <td class="px-4 py-3 text-sm text-right">{{ getCurrency() }}{{ numberFormat(item.price) }}</td>
                                                <td class="px-4 py-3 text-sm text-right">{{ numberFormat(item.quantity) }} {{ item.unit_symbol }}</td>
                                                <td class="px-4 py-3 text-sm text-right font-semibold">{{ getCurrency() }}{{ numberFormat(item.subtotal) }}</td>
                                                <td class="px-4 py-3 text-center">
                                                    <button
                                                        @click.prevent="removeOrderItem(index)"
                                                        type="button"
                                                        class="text-red-600 hover:text-red-800"
                                                        title="Remove item"
                                                    >
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                        <tfoot class="bg-gray-50 font-semibold">
                                            <tr>
                                                <td colspan="4" class="px-4 py-3 text-right text-gray-700">Sub Total:</td>
                                                <td class="px-4 py-3 text-right text-lg">{{ getCurrency() }}{{ numberFormat(subTotal) }}</td>
                                                <td></td>
                                            </tr>
                                            <tr v-if="taxTotal > 0">
                                                <td colspan="4" class="px-4 py-2 text-right text-gray-700">Tax:</td>
                                                <td class="px-4 py-2 text-right">{{ getCurrency() }}{{ numberFormat(taxTotal) }}</td>
                                                <td></td>
                                            </tr>
                                            <tr v-if="discountTotal > 0">
                                                <td colspan="4" class="px-4 py-2 text-right text-gray-700">Discount:</td>
                                                <td class="px-4 py-2 text-right text-red-600">-{{ getCurrency() }}{{ numberFormat(discountTotal) }}</td>
                                                <td></td>
                                            </tr>
                                            <tr class="border-t-2">
                                                <td colspan="4" class="px-4 py-3 text-right text-gray-900 text-lg">Grand Total:</td>
                                                <td class="px-4 py-3 text-right text-emerald-600 text-xl font-bold">{{ getCurrency() }}{{ numberFormat(grandTotal) }}</td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <div v-else class="text-center py-8 text-gray-500">
                                    <i class="fa fa-shopping-cart text-4xl mb-3"></i>
                                    <p>No items added yet. Add products above to create order.</p>
                                </div>
                            </div>

                            <!-- Divider -->
                            <hr class="my-8 border-gray-200"/>

                            <!-- Section 5: Payment & Notes -->
                            <div class="mb-8">
                                <h5 class="text-lg font-semibold text-gray-700 mb-4 pb-2 border-b-2 border-pink-500">
                                    <i class="fa fa-credit-card mr-2"></i>Payment & Additional Info
                                </h5>
                                <div class="grid gap-4 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
                                    <div class="flex flex-col">
                                        <label for="paid" class="text-stone-600 text-sm font-medium">
                                            Paid Amount
                                        </label>
                                        <input
                                            id="paid"
                                            v-model.number="form.paid"
                                            type="number"
                                            step="0.01"
                                            placeholder="Enter paid amount"
                                            class="mt-2 block w-full rounded-md border border-gray-200 px-3 py-2 shadow-sm outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"
                                        />
                                        <InputError :message="form.errors.paid"/>
                                    </div>

                                    <div class="flex flex-col">
                                        <label for="paid_through" class="text-stone-600 text-sm font-medium">
                                            Payment Method
                                        </label>
                                        <select
                                            id="paid_through"
                                            v-model="form.paid_through"
                                            class="mt-2 block w-full rounded-md border border-gray-200 px-3 py-2 shadow-sm outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"
                                        >
                                            <option value="cash">Cash</option>
                                            <option value="bank_transfer">Bank Transfer</option>
                                            <option value="credit_card">Credit Card</option>
                                            <option value="debit_card">Debit Card</option>
                                            <option value="e_wallet">E-Wallet (OVO, GoPay, Dana, dll)</option>
                                            <option value="qris">QRIS</option>
                                            <option value="gift_card">Gift Card</option>
                                        </select>
                                        <InputError :message="form.errors.paid_through"/>
                                    </div>

                                    <div class="flex flex-col">
                                        <label class="text-stone-600 text-sm font-medium mb-2">
                                            Due Amount
                                        </label>
                                        <div class="mt-2 px-4 py-2 bg-red-50 border border-red-200 rounded-md">
                                            <span class="text-red-600 font-bold text-lg">{{ getCurrency() }}{{ numberFormat(dueAmount) }}</span>
                                        </div>
                                    </div>

                                    <div class="flex flex-col md:col-span-3">
                                        <label for="catatan" class="text-stone-600 text-sm font-medium">
                                            Catatan / Notes
                                        </label>
                                        <textarea
                                            id="catatan"
                                            v-model="form.catatan"
                                            rows="3"
                                            placeholder="Enter any additional notes or special instructions"
                                            class="mt-2 block w-full rounded-md border border-gray-200 px-3 py-2 shadow-sm outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"
                                        ></textarea>
                                        <InputError :message="form.errors.catatan"/>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex justify-end gap-3 mt-8">
                                <Button
                                    :href="route('orders.index')"
                                    buttonType="link"
                                    type="button"
                                    class="bg-gray-500 hover:bg-gray-600"
                                >
                                    Cancel
                                </Button>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    :class="{ 'opacity-50': form.processing }"
                                    class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 active:bg-emerald-900 focus:outline-none focus:border-emerald-900 focus:ring focus:ring-emerald-300 disabled:opacity-25 transition"
                                >
                                    <i class="fa fa-save mr-2"></i>
                                    {{ form.processing ? 'Creating...' : 'Create Order' }}
                                    <i v-if="form.processing" class="fas fa-spinner fa-spin ml-2"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
