<script setup>
import { ref, computed, watch } from 'vue';
import { StarIcon } from '@heroicons/vue/24/solid';
import { PlusIcon, TrashIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    modelValue: {
        type: Array,
        default: () => []
    },
    errors: {
        type: Object,
        default: () => ({})
    }
});

const emit = defineEmits(['update:modelValue']);

// Local state
const sizes = ref(props.modelValue.length > 0 ? [...props.modelValue] : [createEmptySize(0)]);

// Create empty size template
function createEmptySize(index) {
    return {
        size_name: '',
        ukuran_potongan: '',
        ukuran_plano: '',
        width: null,
        height: null,
        plano_width: null,
        plano_height: null,
        notes: '',
        is_default: index === 0, // First size is default
        sort_order: index
    };
}

// Add new size
function addSize() {
    const newSize = createEmptySize(sizes.value.length);
    sizes.value.push(newSize);
    emitUpdate();
}

// Remove size
function removeSize(index) {
    if (sizes.value.length === 1) {
        alert('Minimal harus ada 1 ukuran produk.');
        return;
    }
    
    // If removing default size, make first size default
    const wasDefault = sizes.value[index].is_default;
    sizes.value.splice(index, 1);
    
    // Update sort orders
    sizes.value.forEach((size, idx) => {
        size.sort_order = idx;
    });
    
    // If removed was default, set first as default
    if (wasDefault && sizes.value.length > 0) {
        sizes.value[0].is_default = true;
    }
    
    emitUpdate();
}

// Set default size
function setDefault(index) {
    sizes.value.forEach((size, idx) => {
        size.is_default = idx === index;
    });
    emitUpdate();
}

// Calculate quantity per plano
function calculateQuantityPerPlano(size) {
    if (!size.width || !size.height || !size.plano_width || !size.plano_height) {
        return null;
    }
    
    const piecesWidth = Math.floor(size.plano_width / size.width);
    const piecesHeight = Math.floor(size.plano_height / size.height);
    
    return piecesWidth * piecesHeight;
}

// Calculate efficiency
function calculateEfficiency(size) {
    if (!size.width || !size.height || !size.plano_width || !size.plano_height) {
        return null;
    }
    
    const qtyPerPlano = calculateQuantityPerPlano(size);
    if (!qtyPerPlano) return null;
    
    const usedArea = (size.width * size.height) * qtyPerPlano;
    const planoArea = size.plano_width * size.plano_height;
    
    return ((usedArea / planoArea) * 100).toFixed(2);
}

// Get calculated values for display
const calculatedValues = computed(() => {
    return sizes.value.map(size => ({
        quantity_per_plano: calculateQuantityPerPlano(size),
        efficiency: calculateEfficiency(size),
        waste: calculateEfficiency(size) ? (100 - parseFloat(calculateEfficiency(size))).toFixed(2) : null
    }));
});

// Emit update to parent
function emitUpdate() {
    emit('update:modelValue', sizes.value);
}

// Watch for changes in sizes
watch(sizes, () => {
    emitUpdate();
}, { deep: true });

// Watch for external changes (e.g., from parent reset)
watch(() => props.modelValue, (newVal) => {
    if (JSON.stringify(newVal) !== JSON.stringify(sizes.value)) {
        sizes.value = newVal.length > 0 ? [...newVal] : [createEmptySize(0)];
    }
}, { deep: true });

// Get error for specific field
function getError(index, field) {
    const errorKey = `sizes.${index}.${field}`;
    return props.errors[errorKey] ? props.errors[errorKey][0] : null;
}

// Check if any field has error for this size
function hasError(index) {
    return Object.keys(props.errors).some(key => key.startsWith(`sizes.${index}.`));
}
</script>

<template>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-medium text-gray-900">Ukuran Produk</h3>
                <p class="mt-1 text-sm text-gray-500">Tambahkan satu atau lebih ukuran untuk produk ini. Bintang (⭐) menandakan ukuran default.</p>
            </div>
            <button
                type="button"
                @click="addSize"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
                <PlusIcon class="h-5 w-5 mr-2" />
                Tambah Ukuran
            </button>
        </div>

        <!-- Size Items -->
        <div class="space-y-6">
            <div
                v-for="(size, index) in sizes"
                :key="index"
                class="relative border rounded-lg p-6 bg-white shadow-sm"
                :class="{ 'ring-2 ring-indigo-500': size.is_default, 'ring-2 ring-red-300': hasError(index) }"
            >
                <!-- Header with Default Badge and Delete -->
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <span class="text-sm font-medium text-gray-700">Ukuran #{{ index + 1 }}</span>
                        <button
                            type="button"
                            @click="setDefault(index)"
                            :class="[
                                'inline-flex items-center px-2 py-1 rounded text-xs font-medium',
                                size.is_default
                                    ? 'bg-yellow-100 text-yellow-800'
                                    : 'bg-gray-100 text-gray-600 hover:bg-yellow-50 hover:text-yellow-700'
                            ]"
                            :title="size.is_default ? 'Ukuran Default' : 'Set sebagai Default'"
                        >
                            <StarIcon class="h-4 w-4 mr-1" />
                            {{ size.is_default ? 'Default' : 'Set Default' }}
                        </button>
                    </div>
                    <button
                        v-if="sizes.length > 1"
                        type="button"
                        @click="removeSize(index)"
                        class="inline-flex items-center p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded"
                        title="Hapus Ukuran"
                    >
                        <TrashIcon class="h-5 w-5" />
                    </button>
                </div>

                <!-- Size Fields Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Size Name (Optional) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Nama Ukuran <span class="text-gray-400">(Opsional)</span>
                        </label>
                        <input
                            v-model="size.size_name"
                            type="text"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            placeholder="e.g., A4 Standard, Custom Box"
                        />
                        <p v-if="getError(index, 'size_name')" class="mt-1 text-sm text-red-600">
                            {{ getError(index, 'size_name') }}
                        </p>
                    </div>

                    <!-- Ukuran Potongan (Required) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Ukuran Potongan <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="size.ukuran_potongan"
                            type="text"
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-500': getError(index, 'ukuran_potongan') }"
                            placeholder="e.g., 21 x 29.7 cm, 8.5 x 11 inch"
                        />
                        <p v-if="getError(index, 'ukuran_potongan')" class="mt-1 text-sm text-red-600">
                            {{ getError(index, 'ukuran_potongan') }}
                        </p>
                    </div>

                    <!-- Ukuran Plano (Optional) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Ukuran Plano <span class="text-gray-400">(Opsional)</span>
                        </label>
                        <input
                            v-model="size.ukuran_plano"
                            type="text"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            placeholder="e.g., 65 x 100 cm, 27 x 39 inch"
                        />
                        <p v-if="getError(index, 'ukuran_plano')" class="mt-1 text-sm text-red-600">
                            {{ getError(index, 'ukuran_plano') }}
                        </p>
                    </div>

                    <!-- Notes -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Catatan <span class="text-gray-400">(Opsional)</span>
                        </label>
                        <input
                            v-model="size.notes"
                            type="text"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            placeholder="e.g., Premium quality, Standard office"
                        />
                    </div>
                </div>

                <!-- Dimension Fields Section -->
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-sm font-medium text-gray-700">
                            Dimensi untuk Auto-Kalkulasi <span class="text-gray-400">(Opsional)</span>
                        </h4>
                        <span v-if="calculatedValues[index].quantity_per_plano" class="text-sm text-green-600 font-medium">
                            ✓ Auto-kalkulasi aktif
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 mb-3">
                        Isi dimensi berikut untuk menghitung jumlah potongan per plano dan efisiensi secara otomatis.
                    </p>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <!-- Width -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Lebar (cm)</label>
                            <input
                                v-model.number="size.width"
                                type="number"
                                step="0.01"
                                min="0"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                placeholder="21"
                            />
                            <p v-if="getError(index, 'width')" class="mt-1 text-xs text-red-600">
                                {{ getError(index, 'width') }}
                            </p>
                        </div>

                        <!-- Height -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Tinggi (cm)</label>
                            <input
                                v-model.number="size.height"
                                type="number"
                                step="0.01"
                                min="0"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                placeholder="29.7"
                            />
                            <p v-if="getError(index, 'height')" class="mt-1 text-xs text-red-600">
                                {{ getError(index, 'height') }}
                            </p>
                        </div>

                        <!-- Plano Width -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Lebar Plano (cm)</label>
                            <input
                                v-model.number="size.plano_width"
                                type="number"
                                step="0.01"
                                min="0"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                placeholder="65"
                            />
                            <p v-if="getError(index, 'plano_width')" class="mt-1 text-xs text-red-600">
                                {{ getError(index, 'plano_width') }}
                            </p>
                        </div>

                        <!-- Plano Height -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Tinggi Plano (cm)</label>
                            <input
                                v-model.number="size.plano_height"
                                type="number"
                                step="0.01"
                                min="0"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                placeholder="100"
                            />
                            <p v-if="getError(index, 'plano_height')" class="mt-1 text-xs text-red-600">
                                {{ getError(index, 'plano_height') }}
                            </p>
                        </div>
                    </div>

                    <!-- Calculation Results -->
                    <div v-if="calculatedValues[index].quantity_per_plano" class="mt-4 p-3 bg-green-50 rounded-md">
                        <div class="flex items-center justify-between text-sm">
                            <div>
                                <span class="font-medium text-gray-700">Jumlah per Plano:</span>
                                <span class="ml-2 text-green-700 font-semibold">{{ calculatedValues[index].quantity_per_plano }} potong</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Efisiensi:</span>
                                <span class="ml-2 text-green-700 font-semibold">{{ calculatedValues[index].efficiency }}%</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Waste:</span>
                                <span class="ml-2 text-orange-700 font-semibold">{{ calculatedValues[index].waste }}%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Help Text -->
        <div class="text-sm text-gray-500 bg-blue-50 p-3 rounded-md">
            <p class="font-medium text-blue-900 mb-1">Tips:</p>
            <ul class="list-disc list-inside space-y-1 text-blue-800">
                <li>Klik tombol <strong>"+ Tambah Ukuran"</strong> untuk menambah variasi ukuran</li>
                <li>Klik ⭐ <strong>"Set Default"</strong> untuk menandai ukuran utama produk</li>
                <li>Isi dimensi (opsional) untuk menghitung jumlah potongan per plano secara otomatis</li>
                <li>Minimal harus ada 1 ukuran per produk</li>
            </ul>
        </div>
    </div>
</template>
