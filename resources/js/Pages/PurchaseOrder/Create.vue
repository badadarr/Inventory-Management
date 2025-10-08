<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import InputError from "@/Components/InputError.vue";
import Button from "@/Components/Button.vue";
import SubmitButton from "@/Components/SubmitButton.vue";
import AsyncVueSelect from "@/Components/AsyncVueSelect.vue";
import { ref } from 'vue';
import { showToast } from "@/Utils/Helper.js";

const form = useForm({
    supplier_id: null,
    order_number: '',
    order_date: new Date().toISOString().split('T')[0],
    expected_date: '',
    total_amount: 0,
    paid_amount: 0,
    notes: '',
    status: 'pending',
});

const generateOrderNumber = () => {
    const today = new Date();
    const dateStr = today.toISOString().split('T')[0].replace(/-/g, '');
    const random = Math.floor(Math.random() * 9999).toString().padStart(4, '0');
    form.order_number = `PO-${dateStr}-${random}`;
};

// Auto-generate order number on mount
generateOrderNumber();

const createPurchaseOrder = () => {
    form.post(route('purchase-orders.store'), {
        preserveScroll: true,
        onSuccess: () => {
            showToast('Purchase order created successfully', 'success');
        },
        onError: (errors) => {
            showToast('Failed to create purchase order', 'error');
        }
    });
};
</script>

<template>
    <Head title="Create Purchase Order" />

    <AuthenticatedLayout>
        <template #breadcrumb>
            Purchase Orders > Create
        </template>

        <div class="flex flex-wrap">
            <div class="w-full px-4">
                <div class="relative flex flex-col min-w-0 break-words w-full mb-6 shadow-lg rounded bg-white">
                    <div class="rounded-t mb-3 px-4 py-3 border-0">
                        <div class="flex flex-wrap items-center">
                            <div class="relative w-full px-4 max-w-full flex-grow flex-1">
                                <div class="flex justify-between items-center">
                                    <h4 class="text-2xl font-semibold">Create Purchase Order</h4>
                                    <Button
                                        :href="route('purchase-orders.index')"
                                        buttonType="link"
                                    >
                                        <i class="fas fa-arrow-left mr-2"></i>
                                        Go Back
                                    </Button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <form @submit.prevent="createPurchaseOrder" class="px-8 py-4">
                        <div class="grid gap-6 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2">
                            <!-- Supplier -->
                            <div class="flex flex-col">
                                <label for="supplier" class="text-stone-600 text-sm font-medium mb-2">
                                    Supplier <span class="text-red-500">*</span>
                                </label>
                                <AsyncVueSelect
                                    v-model="form.supplier_id"
                                    resource="suppliers.index"
                                    placeholder="Select supplier"
                                    label="name"
                                    :reduce="supplier => supplier.id"
                                />
                                <InputError class="mt-2" :message="form.errors.supplier_id"/>
                            </div>

                            <!-- Order Number -->
                            <div class="flex flex-col">
                                <label for="order_number" class="text-stone-600 text-sm font-medium mb-2">
                                    Order Number <span class="text-red-500">*</span>
                                </label>
                                <div class="flex gap-2">
                                    <input
                                        id="order_number"
                                        v-model="form.order_number"
                                        type="text"
                                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm flex-1"
                                        required
                                    />
                                    <Button 
                                        @click.prevent="generateOrderNumber"
                                        type="button"
                                        variant="secondary"
                                        size="sm"
                                        title="Generate new number"
                                    >
                                        <i class="fas fa-sync"></i>
                                    </Button>
                                </div>
                                <InputError class="mt-2" :message="form.errors.order_number"/>
                            </div>

                            <!-- Order Date -->
                            <div class="flex flex-col">
                                <label for="order_date" class="text-stone-600 text-sm font-medium mb-2">
                                    Order Date <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="order_date"
                                    v-model="form.order_date"
                                    type="date"
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.order_date"/>
                            </div>

                            <!-- Expected Date -->
                            <div class="flex flex-col">
                                <label for="expected_date" class="text-stone-600 text-sm font-medium mb-2">
                                    Expected Delivery Date
                                </label>
                                <input
                                    id="expected_date"
                                    v-model="form.expected_date"
                                    type="date"
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    :min="form.order_date"
                                />
                                <InputError class="mt-2" :message="form.errors.expected_date"/>
                            </div>

                            <!-- Total Amount -->
                            <div class="flex flex-col">
                                <label for="total_amount" class="text-stone-600 text-sm font-medium mb-2">
                                    Total Amount <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="total_amount"
                                    v-model="form.total_amount"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.total_amount"/>
                            </div>

                            <!-- Paid Amount -->
                            <div class="flex flex-col">
                                <label for="paid_amount" class="text-stone-600 text-sm font-medium mb-2">
                                    Paid Amount
                                </label>
                                <input
                                    id="paid_amount"
                                    v-model="form.paid_amount"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    :max="form.total_amount"
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                />
                                <InputError class="mt-2" :message="form.errors.paid_amount"/>
                                <p class="text-xs text-gray-500 mt-1">
                                    Due: {{ (form.total_amount - form.paid_amount).toFixed(2) }}
                                </p>
                            </div>

                            <!-- Status -->
                            <div class="flex flex-col">
                                <label for="status" class="text-stone-600 text-sm font-medium mb-2">
                                    Status
                                </label>
                                <select
                                    id="status"
                                    v-model="form.status"
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                >
                                    <option value="pending">Pending</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.status"/>
                            </div>

                            <!-- Notes -->
                            <div class="flex flex-col md:col-span-2">
                                <label for="notes" class="text-stone-600 text-sm font-medium mb-2">
                                    Notes
                                </label>
                                <textarea
                                    id="notes"
                                    v-model="form.notes"
                                    rows="4"
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    placeholder="Enter any additional notes or instructions..."
                                ></textarea>
                                <InputError class="mt-2" :message="form.errors.notes"/>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex items-center justify-end mt-6 gap-3">
                            <Button
                                :href="route('purchase-orders.index')"
                                buttonType="link"
                                variant="secondary"
                            >
                                Cancel
                            </Button>
                            <SubmitButton :disabled="form.processing">
                                <i class="fas fa-save mr-2"></i>
                                Create Purchase Order
                            </SubmitButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
