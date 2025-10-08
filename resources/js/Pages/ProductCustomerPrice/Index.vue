<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import Button from "@/Components/Button.vue";
import Modal from "@/Components/Modal.vue";
import { showToast } from "@/Utils/Helper.js";
import axios from 'axios';

const props = defineProps({
    product: {
        type: Object,
        required: true
    },
    customPrices: {
        type: Array,
        default: () => []
    },
    customers: {
        type: Array,
        default: () => []
    }
});

const showModal = ref(false);
const editingPrice = ref(null);

const form = useForm({
    product_id: props.product.id,
    customer_id: null,
    custom_price: null,
    notes: null
});

const openCreateModal = () => {
    editingPrice.value = null;
    form.reset();
    form.product_id = props.product.id;
    showModal.value = true;
};

const openEditModal = (customPrice) => {
    editingPrice.value = customPrice;
    form.product_id = customPrice.product_id;
    form.customer_id = customPrice.customer_id;
    form.custom_price = customPrice.custom_price;
    form.notes = customPrice.notes;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    editingPrice.value = null;
    form.reset();
};

const saveCustomPrice = async () => {
    console.log('saveCustomPrice called');
    console.log('Form data:', form.data());
    
    // Validate required fields
    if (!form.customer_id) {
        showToast('Please select a customer', 'error');
        return;
    }
    
    if (!form.custom_price) {
        showToast('Please enter custom price', 'error');
        return;
    }
    
    try {
        form.processing = true;
        console.log('Sending request to:', route('product-customer-prices.upsert'));
        
        const response = await axios.post(route('product-customer-prices.upsert'), form.data());
        
        console.log('Response:', response.data);
        showToast(response.data.message || 'Custom price saved successfully', 'success');
        closeModal();
        
        // Reload page to refresh data
        setTimeout(() => {
            window.location.reload();
        }, 500);
    } catch (error) {
        console.error('Save error:', error);
        console.error('Error response:', error.response);
        
        const errorMessage = error.response?.data?.message || 'Failed to save custom price';
        showToast(errorMessage, 'error');
    } finally {
        form.processing = false;
    }
};

const deleteCustomPrice = async (productId, customerId) => {
    if (!confirm('Are you sure you want to delete this custom price?')) {
        return;
    }
    
    try {
        const response = await axios.delete(route('product-customer-prices.destroy', { 
            productId, 
            customerId 
        }));
        showToast(response.data.message || 'Custom price deleted successfully', 'success');
        // Reload page to refresh data
        window.location.reload();
    } catch (error) {
        const errorMessage = error.response?.data?.message || 'Failed to delete custom price';
        showToast(errorMessage, 'error');
    }
};

const formatPrice = (price) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(price);
};
</script>

<template>
    <Head :title="`Custom Prices - ${product.name}`"/>

    <AuthenticatedLayout>
        <template #breadcrumb>
            <span class="text-gray-500">Products</span>
            <span class="mx-2">/</span>
            <span class="text-gray-500">{{ product.name }}</span>
            <span class="mx-2">/</span>
            <span>Custom Prices</span>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 relative z-20">
                <!-- Product Info Card -->
                <div class="bg-white shadow rounded-lg p-6 mb-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">{{ product.name }}</h2>
                            <div class="mt-2 text-sm text-gray-600">
                                <p><strong>Product Code:</strong> {{ product.product_code }}</p>
                                <p><strong>Category:</strong> {{ product.category?.name || '-' }}</p>
                                <p><strong>Base Selling Price:</strong> {{ formatPrice(product.selling_price) }}</p>
                            </div>
                        </div>
                        <div>
                            <Button
                                @click="openCreateModal"
                                type="success"
                            >
                                <i class="fa fa-plus mr-2"></i>
                                Add Custom Price
                            </Button>
                        </div>
                    </div>
                </div>

                <!-- Custom Prices List -->
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">
                            Customer-Specific Prices ({{ customPrices.length }})
                        </h3>
                    </div>

                    <div v-if="customPrices.length === 0" class="p-8 text-center text-gray-500">
                        <i class="fa fa-tags text-4xl mb-4 text-gray-300"></i>
                        <p class="text-lg">No custom prices set for this product</p>
                        <p class="text-sm mt-2">Click "Add Custom Price" to set customer-specific pricing</p>
                    </div>

                    <div v-else class="divide-y divide-gray-200">
                        <div 
                            v-for="customPrice in customPrices" 
                            :key="`${customPrice.product_id}-${customPrice.customer_id}`"
                            class="p-6 hover:bg-gray-50 transition"
                        >
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <h4 class="text-lg font-medium text-gray-900">
                                        {{ customPrice.customer?.name || 'Unknown Customer' }}
                                    </h4>
                                    <div class="mt-2 space-y-1">
                                        <div class="flex items-center text-sm">
                                            <span class="text-gray-500 w-32">Custom Price:</span>
                                            <span class="font-semibold text-green-600">
                                                {{ formatPrice(customPrice.custom_price) }}
                                            </span>
                                            <span class="ml-3 text-xs text-gray-400">
                                                (Base: {{ formatPrice(product.selling_price) }})
                                            </span>
                                        </div>
                                        <div v-if="customPrice.notes" class="flex items-start text-sm">
                                            <span class="text-gray-500 w-32">Notes:</span>
                                            <span class="text-gray-700">{{ customPrice.notes }}</span>
                                        </div>
                                        <div class="flex items-center text-sm">
                                            <span class="text-gray-500 w-32">Last Updated:</span>
                                            <span class="text-gray-700">
                                                {{ new Date(customPrice.updated_at).toLocaleDateString('id-ID') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex gap-2 ml-4">
                                    <Button
                                        @click="openEditModal(customPrice)"
                                        type="info"
                                        title="Edit Custom Price"
                                    >
                                        <i class="fa fa-edit"></i>
                                    </Button>
                                    <Button
                                        @click="deleteCustomPrice(customPrice.product_id, customPrice.customer_id)"
                                        type="red"
                                        title="Delete Custom Price"
                                    >
                                        <i class="fa fa-trash-alt"></i>
                                    </Button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create/Edit Modal -->
        <Modal
            :title="editingPrice ? 'Edit Custom Price' : 'Add Custom Price'"
            :show="showModal"
            @close="closeModal"
            maxWidth="md"
            :showFooter="false"
        >
            <form @submit.prevent="saveCustomPrice">
                <div class="space-y-4">
                    <!-- Customer Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Customer <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="form.customer_id"
                            :disabled="!!editingPrice"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            required
                        >
                            <option :value="null">Select Customer</option>
                            <option 
                                v-for="customer in customers" 
                                :key="customer.id" 
                                :value="customer.id"
                            >
                                {{ customer.name }}
                            </option>
                        </select>
                        <p v-if="editingPrice" class="mt-1 text-xs text-gray-500">
                            Customer cannot be changed when editing
                        </p>
                    </div>

                    <!-- Custom Price -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Custom Price <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.custom_price"
                            type="number"
                            step="0.01"
                            min="0"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Enter custom price"
                            required
                        />
                        <p class="mt-1 text-xs text-gray-500">
                            Base selling price: {{ formatPrice(product.selling_price) }}
                        </p>
                    </div>

                    <!-- Notes -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Notes (Optional)
                        </label>
                        <textarea
                            v-model="form.notes"
                            rows="3"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Add any notes about this custom price..."
                        ></textarea>
                    </div>
                </div>

                <!-- Modal Actions -->
                <div class="mt-6 flex justify-end gap-3">
                    <Button
                        type="button"
                        @click="closeModal"
                        buttonType="button"
                    >
                        Cancel
                    </Button>
                    <Button
                        type="submit"
                        buttonType="submit"
                        :disabled="form.processing"
                    >
                        <i class="fa fa-save mr-2"></i>
                        {{ editingPrice ? 'Update' : 'Save' }} Custom Price
                    </Button>
                </div>
            </form>
        </Modal>
    </AuthenticatedLayout>
</template>
