<script setup>
import { ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import { numberFormat } from '@/Utils/Helper.js';

const lowStockProducts = ref([]);
const loading = ref(true);
const showAll = ref(false);

const fetchLowStockProducts = async () => {
    try {
        loading.value = true;
        const response = await axios.get('/products/low-stock/alert');
        lowStockProducts.value = response.data.data || [];
    } catch (error) {
        console.error('Failed to fetch low stock products:', error);
    } finally {
        loading.value = false;
    }
};

const displayedProducts = computed(() => {
    if (showAll.value) {
        return lowStockProducts.value;
    }
    return lowStockProducts.value.slice(0, 5);
});

const getStockPercentage = (product) => {
    if (!product.reorder_level || product.reorder_level === 0) return 0;
    return Math.min(100, (product.quantity / product.reorder_level) * 100);
};

const getStockStatusClass = (percentage) => {
    if (percentage <= 30) return 'bg-red-500';
    if (percentage <= 60) return 'bg-yellow-500';
    return 'bg-green-500';
};

const viewProduct = (productId) => {
    router.visit(route('products.edit', productId));
};

onMounted(() => {
    fetchLowStockProducts();
});
</script>

<template>
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-orange-500 to-red-500 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-white text-2xl mr-3"></i>
                    <div>
                        <h3 class="text-white font-bold text-lg">Low Stock Alert</h3>
                        <p class="text-orange-100 text-sm">Products needing reorder</p>
                    </div>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full px-4 py-2">
                    <span class="text-white font-bold text-xl">
                        {{ lowStockProducts.length }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="p-6">
            <!-- Loading State -->
            <div v-if="loading" class="text-center py-8">
                <i class="fas fa-spinner fa-spin text-3xl text-gray-400 mb-3"></i>
                <p class="text-gray-500">Loading low stock products...</p>
            </div>

            <!-- Empty State -->
            <div v-else-if="lowStockProducts.length === 0" class="text-center py-8">
                <i class="fas fa-check-circle text-5xl text-green-400 mb-4"></i>
                <h4 class="text-lg font-semibold text-gray-700 mb-2">All Stock Levels Good!</h4>
                <p class="text-gray-500">No products need reordering at this time.</p>
            </div>

            <!-- Products List -->
            <div v-else class="space-y-4">
                <div 
                    v-for="product in displayedProducts" 
                    :key="product.id"
                    class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow cursor-pointer"
                    @click="viewProduct(product.id)"
                >
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900 mb-1">
                                {{ product.name }}
                            </h4>
                            <div class="flex items-center gap-3 text-sm text-gray-600">
                                <span>
                                    <i class="fas fa-tag mr-1"></i>
                                    {{ product.product_code }}
                                </span>
                                <span>
                                    <i class="fas fa-folder mr-1"></i>
                                    {{ product.category?.name || 'N/A' }}
                                </span>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold text-red-600">
                                {{ numberFormat(product.quantity) }}
                            </div>
                            <div class="text-xs text-gray-500">units left</div>
                        </div>
                    </div>

                    <!-- Stock Progress Bar -->
                    <div class="mb-2">
                        <div class="flex justify-between text-xs text-gray-600 mb-1">
                            <span>Stock Level</span>
                            <span>Reorder at: {{ numberFormat(product.reorder_level) }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div 
                                :class="getStockStatusClass(getStockPercentage(product))"
                                class="h-2 rounded-full transition-all"
                                :style="{ width: getStockPercentage(product) + '%' }"
                            ></div>
                        </div>
                    </div>

                    <!-- Supplier Info -->
                    <div class="flex items-center justify-between text-sm">
                        <div class="text-gray-600">
                            <i class="fas fa-truck mr-1"></i>
                            Supplier: <span class="font-medium">{{ product.supplier?.name || 'N/A' }}</span>
                        </div>
                        <button 
                            @click.stop="viewProduct(product.id)"
                            class="text-blue-600 hover:text-blue-800 font-medium"
                        >
                            View Details →
                        </button>
                    </div>
                </div>

                <!-- Show More/Less Button -->
                <button 
                    v-if="lowStockProducts.length > 5"
                    @click="showAll = !showAll"
                    class="w-full py-2 px-4 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors font-medium"
                >
                    {{ showAll ? 'Show Less' : `Show All (${lowStockProducts.length})` }}
                    <i :class="['fas', showAll ? 'fa-chevron-up' : 'fa-chevron-down', 'ml-2']"></i>
                </button>
            </div>
        </div>

        <!-- Footer -->
        <div v-if="lowStockProducts.length > 0" class="bg-gray-50 px-6 py-4 border-t">
            <button 
                @click="router.visit(route('purchase-orders.create'))"
                class="w-full py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors font-medium"
            >
                <i class="fas fa-plus mr-2"></i>
                Create Purchase Order
            </button>
        </div>
    </div>
</template>

<script>
import { computed } from 'vue';
export default {
    name: 'LowStockWidget'
}
</script>
