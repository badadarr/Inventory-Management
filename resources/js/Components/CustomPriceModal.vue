<script setup>
import { ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import Button from '@/Components/Button.vue';
import SubmitButton from '@/Components/SubmitButton.vue';
import InputError from '@/Components/InputError.vue';
import AsyncVueSelect from '@/Components/AsyncVueSelect.vue';
import { showToast, numberFormat } from '@/Utils/Helper.js';
import axios from 'axios';

const props = defineProps({
    show: {
        type: Boolean,
        default: false
    },
    product: {
        type: Object,
        default: null
    },
    customer: {
        type: Object,
        default: null
    }
});

const emit = defineEmits(['close', 'saved']);

const form = useForm({
    product_id: props.product?.id || null,
    customer_id: props.customer?.id || null,
    custom_price: 0,
    effective_date: new Date().toISOString().split('T')[0],
    notes: ''
});

const standardPrice = ref(0);
const discountPercentage = ref(0);

// Watch for product/customer changes
watch(() => props.product, (newProduct) => {
    if (newProduct) {
        form.product_id = newProduct.id;
        standardPrice.value = newProduct.selling_price || 0;
        calculateDiscount();
    }
});

watch(() => props.customer, (newCustomer) => {
    if (newCustomer) {
        form.customer_id = newCustomer.id;
    }
});

watch(() => form.custom_price, () => {
    calculateDiscount();
});

const calculateDiscount = () => {
    if (standardPrice.value > 0 && form.custom_price > 0) {
        const discount = ((standardPrice.value - form.custom_price) / standardPrice.value) * 100;
        discountPercentage.value = Math.round(discount * 100) / 100;
    } else {
        discountPercentage.value = 0;
    }
};

const applyDiscount = (percentage) => {
    if (standardPrice.value > 0) {
        form.custom_price = standardPrice.value * (1 - percentage / 100);
        form.custom_price = Math.round(form.custom_price * 100) / 100;
    }
};

const saveCustomPrice = async () => {
    try {
        const response = await axios.post(route('product-customer-prices.upsert'), form.data());
        
        if (response.data.success) {
            showToast(response.data.message || 'Custom price saved successfully', 'success');
            emit('saved', response.data.data);
            closeModal();
        }
    } catch (error) {
        const errorMessage = error.response?.data?.message || 'Failed to save custom price';
        showToast(errorMessage, 'error');
        
        if (error.response?.data?.errors) {
            form.setError(error.response.data.errors);
        }
    }
};

const closeModal = () => {
    form.reset();
    form.clearErrors();
    emit('close');
};
</script>

<template>
    <Modal :show="show" @close="closeModal" max-width="2xl">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-semibold text-gray-900">
                    <i class="fas fa-tag mr-2 text-blue-600"></i>
                    Set Custom Price
                </h3>
                <button 
                    @click="closeModal" 
                    class="text-gray-400 hover:text-gray-600"
                >
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form @submit.prevent="saveCustomPrice">
                <div class="space-y-4">
                    <!-- Product Selection -->
                    <div v-if="!product">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Product <span class="text-red-500">*</span>
                        </label>
                        <AsyncVueSelect
                            v-model="form.product_id"
                            resource="products.index"
                            placeholder="Select product"
                            label="name"
                            :reduce="product => product.id"
                        />
                        <InputError class="mt-2" :message="form.errors.product_id"/>
                    </div>
                    <div v-else class="bg-gray-50 p-4 rounded-lg">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Product</label>
                        <div class="font-semibold text-gray-900">{{ product.name }}</div>
                        <div class="text-sm text-gray-600">Code: {{ product.product_code }}</div>
                    </div>

                    <!-- Customer Selection -->
                    <div v-if="!customer">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Customer <span class="text-red-500">*</span>
                        </label>
                        <AsyncVueSelect
                            v-model="form.customer_id"
                            resource="customers.index"
                            placeholder="Select customer"
                            label="name"
                            :reduce="customer => customer.id"
                        />
                        <InputError class="mt-2" :message="form.errors.customer_id"/>
                    </div>
                    <div v-else class="bg-gray-50 p-4 rounded-lg">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Customer</label>
                        <div class="font-semibold text-gray-900">{{ customer.name }}</div>
                    </div>

                    <!-- Standard Price Info -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <label class="text-sm font-medium text-blue-900">Standard Price</label>
                                <div class="text-2xl font-bold text-blue-700">
                                    Rp {{ numberFormat(standardPrice) }}
                                </div>
                            </div>
                            <div class="text-right" v-if="discountPercentage !== 0">
                                <label class="text-sm font-medium text-blue-900">Discount</label>
                                <div class="text-2xl font-bold" 
                                     :class="discountPercentage > 0 ? 'text-green-600' : 'text-red-600'">
                                    {{ discountPercentage > 0 ? '-' : '+' }}{{ Math.abs(discountPercentage) }}%
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Discount Buttons -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Quick Discount</label>
                        <div class="grid grid-cols-5 gap-2">
                            <button
                                v-for="discount in [5, 10, 15, 20, 25]"
                                :key="discount"
                                type="button"
                                @click="applyDiscount(discount)"
                                class="py-2 px-3 bg-gray-100 hover:bg-blue-100 hover:text-blue-700 rounded-lg text-sm font-medium transition-colors"
                            >
                                -{{ discount }}%
                            </button>
                        </div>
                    </div>

                    <!-- Custom Price Input -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Custom Price <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">
                                Rp
                            </span>
                            <input
                                v-model="form.custom_price"
                                type="number"
                                step="0.01"
                                min="0"
                                class="pl-12 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                placeholder="0.00"
                                required
                            />
                        </div>
                        <InputError class="mt-2" :message="form.errors.custom_price"/>
                    </div>

                    <!-- Effective Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Effective Date
                        </label>
                        <input
                            v-model="form.effective_date"
                            type="date"
                            class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        />
                        <InputError class="mt-2" :message="form.errors.effective_date"/>
                    </div>

                    <!-- Notes -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Notes
                        </label>
                        <textarea
                            v-model="form.notes"
                            rows="3"
                            class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            placeholder="Enter reason for custom pricing..."
                        ></textarea>
                        <InputError class="mt-2" :message="form.errors.notes"/>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="flex justify-end gap-3 mt-6 pt-6 border-t">
                    <Button
                        type="button"
                        @click="closeModal"
                        variant="secondary"
                    >
                        Cancel
                    </Button>
                    <SubmitButton :disabled="form.processing">
                        <i class="fas fa-save mr-2"></i>
                        Save Custom Price
                    </SubmitButton>
                </div>
            </form>
        </div>
    </Modal>
</template>
