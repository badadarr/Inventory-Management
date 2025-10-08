<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import InputError from "@/Components/InputError.vue";
import {useForm} from '@inertiajs/vue3';
import {ref} from 'vue';
import Button from "@/Components/Button.vue";
import SubmitButton from "@/Components/SubmitButton.vue";
import AsyncVueSelect from "@/Components/AsyncVueSelect.vue";
import ProductSizeRepeater from "@/Components/ProductSizeRepeater.vue";
import {showToast} from "@/Utils/Helper.js";
import default_image from "@/assets/img/default-image.jpg";

defineProps({
    filters: {
        type: Object
    },
});

const nameInput = ref(null);
const isHovered = ref(false);
const fileInput = ref(null);
const previewImage = ref(null);

const form = useForm({
    category_id: null,
    supplier_id: null,
    name: null,
    bahan: null,
    gramatur: null,
    alamat_pengiriman: null,
    product_code: null,
    buying_price: null,
    selling_price: null,
    unit_type_id: null,
    quantity: null,
    reorder_level: null,
    keterangan_tambahan: null,
    photo: null,
    status: 'active',
    sizes: [{
        size_name: '',
        ukuran_potongan: '',
        ukuran_plano: '',
        width: null,
        height: null,
        plano_width: null,
        plano_height: null,
        notes: '',
        is_default: true,
        sort_order: 0
    }]
});

const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        previewImage.value = URL.createObjectURL(file);
        form.photo = file;
    }
};

const createProduct = () => {
    form.post(route('products.store'), {
        preserveScroll: true,
        onSuccess: () => {
            showToast();
        },
        onError: () => nameInput.value.focus(),
    });
};
</script>

<template>
    <Head title="Product"/>

    <AuthenticatedLayout>
        <template #breadcrumb>
            Products > Create
        </template>

        <div class="flex flex-wrap">
            <div class="w-full px-4">
                <div class="relative flex flex-col min-w-0 break-words w-full mb-6 shadow-lg rounded bg-white">
                    <div class="rounded-t mb-3 px-4 py-3 border-0">
                        <div class="flex flex-wrap items-center">
                            <div class="relative w-full px-4 max-w-full flex-grow flex-1">
                                <div class="flex justify-between items-center">
                                    <h4 class="text-2xl">Create Product</h4>
                                    <Button
                                        :href="route('products.index')"
                                        buttonType="link"
                                    >
                                        Go Back
                                    </Button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="block w-full overflow-x-auto px-8 py-4">
                        <div class="grid gap-4 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3">
                            <div class="flex flex-col">
                                <label for="category" class="text-stone-600 text-sm font-medium">Select Category</label>
                                <AsyncVueSelect
                                    v-model="form.category_id"
                                    resource="categories.index"
                                    placeholder="Select category"
                                    class="mt-2"
                                />
                                <InputError :message="form.errors.category_id"/>
                            </div>
                            <div class="flex flex-col">
                                <label for="supplier" class="text-stone-600 text-sm font-medium">Select Supplier (Optional)</label>
                                <AsyncVueSelect
                                    v-model="form.supplier_id"
                                    resource="suppliers.index"
                                    placeholder="Select supplier (optional)"
                                    class="mt-2"
                                />
                                <InputError :message="form.errors.supplier_id"/>
                            </div>
                            <div class="flex flex-col">
                                <label for="name" class="text-stone-600 text-sm font-medium">Name</label>
                                <input
                                    id="name"
                                    ref="nameInput"
                                    v-model="form.name"
                                    @keyup.enter="createProduct"
                                    type="text"
                                    placeholder="Enter name"
                                    class="mt-2 block w-full rounded-md border border-gray-200 px-2 py-2 shadow-sm outline-none focus:outline-none focus:shadow-outline"
                                />
                                <InputError :message="form.errors.name"/>
                            </div>
                            <div class="flex flex-col">
                                <label for="product_code" class="text-stone-600 text-sm font-medium">Product Code (Optional)</label>
                                <input
                                    id="product_code"
                                    v-model="form.product_code"
                                    @keyup.enter="createProduct"
                                    type="text"
                                    placeholder="Enter product code (optional)"
                                    class="mt-2 block w-full rounded-md border border-gray-200 px-2 py-2 shadow-sm outline-none focus:outline-none focus:shadow-outline"
                                />
                                <InputError :message="form.errors.product_code"/>
                            </div>
                            <div class="flex flex-col">
                                <label for="buying_price" class="text-stone-600 text-sm font-medium">Buying Price</label>
                                <input
                                    id="buying_price"
                                    v-model="form.buying_price"
                                    @keyup.enter="createProduct"
                                    type="number"
                                    placeholder="Enter buying price"
                                    class="mt-2 block w-full rounded-md border border-gray-200 px-2 py-2 shadow-sm outline-none focus:outline-none focus:shadow-outline"
                                />
                                <InputError :message="form.errors.buying_price"/>
                            </div>
                            <div class="flex flex-col">
                                <label for="selling_price" class="text-stone-600 text-sm font-medium">Selling Price</label>
                                <input
                                    id="selling_price"
                                    v-model="form.selling_price"
                                    @keyup.enter="createProduct"
                                    type="number"
                                    placeholder="Enter selling price"
                                    class="mt-2 block w-full rounded-md border border-gray-200 px-2 py-2 shadow-sm outline-none focus:outline-none focus:shadow-outline"
                                />
                                <InputError :message="form.errors.selling_price"/>
                            </div>
                            <div class="flex flex-col">
                                <label for="quantity" class="text-stone-600 text-sm font-medium">Quantity</label>
                                <div class="flex mt-1">
                                    <AsyncVueSelect
                                        v-model="form.unit_type_id"
                                        resource="unit-types.index"
                                        placeholder="Select unit type"
                                        class="w-1/2 rounded-l-md bg-gray-300 border-none outline-none focus:outline-none"
                                    />
                                    <input
                                        id="quantity"
                                        v-model="form.quantity"
                                        @keyup.enter="createProduct"
                                        type="number"
                                        placeholder="Enter quantity"
                                        class="w-full rounded-r-md border border-gray-200 px-2 py-2 shadow-sm outline-none focus:outline-none focus:shadow-outline"
                                    />
                                </div>
                                <InputError :message="form.errors.unit_type_id"/>
                                <InputError :message="form.errors.quantity"/>
                            </div>
                            <div class="flex flex-col">
                                <label for="reorder_level" class="text-stone-600 text-sm font-medium">Reorder Level (Optional)</label>
                                <input
                                    id="reorder_level"
                                    v-model="form.reorder_level"
                                    @keyup.enter="createProduct"
                                    type="number"
                                    step="0.01"
                                    placeholder="e.g. 100"
                                    class="mt-2 block w-full rounded-md border border-gray-200 px-2 py-2 shadow-sm outline-none focus:outline-none focus:shadow-outline"
                                />
                                <InputError :message="form.errors.reorder_level"/>
                                <span class="text-xs text-gray-500 mt-1">⚠️ Alert when stock falls below this level</span>
                            </div>
                            <div class="flex flex-col">
                                <label for="status" class="text-stone-600 text-sm font-medium">Status</label>
                                <select
                                    id="status"
                                    v-model="form.status"
                                    class="mt-2 block w-full rounded-md border border-gray-200 px-2 py-2 shadow-sm outline-none focus:outline-none focus:shadow-outline"
                                >
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                <InputError :message="form.errors.status"/>
                            </div>
                            <div class="flex flex-col col-span-3">
                                <label for="photo" class="text-stone-600 text-sm font-medium mb-2">Product Photo</label>
                                <div class="flex gap-4 items-start">
                                    <div class="relative cursor-pointer border-2 border-dashed border-gray-300 rounded-lg hover:border-blue-500 transition" 
                                         @mouseenter="isHovered = true" 
                                         @mouseleave="isHovered = false"
                                         @click="fileInput.click()">
                                        <img
                                            :alt="form.name || 'Product photo'"
                                            :src="previewImage || default_image"
                                            class="shadow-md h-auto align-middle border-none rounded-lg object-cover"
                                            style="width: 200px; height: 150px;"
                                            title="Click to upload photo"
                                        />
                                        <div
                                            v-if="isHovered"
                                            class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center rounded-lg"
                                        >
                                            <i class="fas fa-camera text-white text-3xl"></i>
                                        </div>
                                        <input type="file" class="hidden" accept="image/*" ref="fileInput" @change="handleFileChange" />
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-xs text-gray-500 mb-1">📷 Click image to upload</p>
                                        <p class="text-xs text-gray-400">Max size: 1MB | Format: JPG, PNG, GIF, SVG</p>
                                        <InputError :message="form.errors.photo" class="mt-1"/>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-col">
                                <label for="bahan" class="text-stone-600 text-sm font-medium">Bahan</label>
                                <input
                                    id="bahan"
                                    v-model="form.bahan"
                                    @keyup.enter="createProduct"
                                    type="text"
                                    placeholder="e.g. Art Paper, HVS, Duplex"
                                    class="mt-2 block w-full rounded-md border border-gray-200 px-2 py-2 shadow-sm outline-none focus:outline-none focus:shadow-outline"
                                />
                                <InputError :message="form.errors.bahan"/>
                            </div>
                            <div class="flex flex-col">
                                <label for="gramatur" class="text-stone-600 text-sm font-medium">Gramatur</label>
                                <input
                                    id="gramatur"
                                    v-model="form.gramatur"
                                    @keyup.enter="createProduct"
                                    type="text"
                                    placeholder="e.g. 210 gsm, 150 gsm"
                                    class="mt-2 block w-full rounded-md border border-gray-200 px-2 py-2 shadow-sm outline-none focus:outline-none focus:shadow-outline"
                                />
                                <InputError :message="form.errors.gramatur"/>
                            </div>
                            
                            <!-- Dynamic Product Sizes Component -->
                            <div class="col-span-3">
                                <ProductSizeRepeater 
                                    v-model="form.sizes"
                                    :errors="form.errors"
                                />
                            </div>
                            <div class="flex flex-col">
                                <label for="alamat_pengiriman" class="text-stone-600 text-sm font-medium">Alamat Pengiriman (Optional)</label>
                                <textarea
                                    id="alamat_pengiriman"
                                    v-model="form.alamat_pengiriman"
                                    type="text"
                                    rows="3"
                                    placeholder="e.g. Jl. Sudirman No. 123, Jakarta Pusat&#10;(Opsional - jika berbeda dari alamat utama)"
                                    class="mt-2 block w-full rounded-md border border-gray-200 px-2 py-2 shadow-sm outline-none focus:outline-none focus:shadow-outline"
                                ></textarea>
                                <InputError :message="form.errors.alamat_pengiriman"/>
                            </div>
                            <div class="flex flex-col">
                                <label for="keterangan_tambahan" class="text-stone-600 text-sm font-medium">Keterangan Tambahan (Optional)</label>
                                <textarea
                                    id="keterangan_tambahan"
                                    v-model="form.keterangan_tambahan"
                                    type="text"
                                    rows="3"
                                    placeholder="e.g. Finishing: Laminating doff + spot UV&#10;Catatan: Gunakan tinta khusus untuk warna gold"
                                    class="mt-2 block w-full rounded-md border border-gray-200 px-2 py-2 shadow-sm outline-none focus:outline-none focus:shadow-outline"
                                ></textarea>
                                <InputError :message="form.errors.keterangan_tambahan"/>
                            </div>
                        </div>
                        <div class="my-6 flex justify-end">
                            <SubmitButton
                                :processing="form.processing"
                                @click="createProduct"
                                class="text-white bg-emerald-600 font-bold uppercase text-xs px-4 py-2 rounded shadow hover:shadow-lg outline-none focus:outline-none mr-1 ease-linear transition-all duration-150"
                            >
                                Submit
                            </SubmitButton>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
