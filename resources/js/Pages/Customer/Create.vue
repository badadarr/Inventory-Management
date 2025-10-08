<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import InputError from "@/Components/InputError.vue";
import {useForm} from '@inertiajs/vue3';
import {ref, computed} from 'vue';
import Button from "@/Components/Button.vue";
import SubmitButton from "@/Components/SubmitButton.vue";
import {showToast} from "@/Utils/Helper.js";
import default_image from "@/assets/img/default-image.jpg";

const props = defineProps({
    salesList: {
        type: Array,
        default: () => []
    },
});

const nameInput = ref(null);
const isHovered = ref(false);
const fileInput = ref(null);
const previewImage = ref(null);

const form = useForm({
    name: null,
    email: null,
    phone: null,
    nama_box: null,
    nama_owner: null,
    sales_id: null,
    tanggal_join: null,
    status_customer: 'baru',
    status_komisi: null,
    harga_komisi_standar: null,
    harga_komisi_extra: null,
    address: null,
    photo: null,
});

// Format currency for display
const formatCurrency = (value) => {
    if (!value) return '';
    return new Intl.NumberFormat('id-ID').format(value);
};

// Parse currency input
const parseCurrency = (value) => {
    if (!value) return null;
    return parseFloat(value.toString().replace(/\D/g, ''));
};

const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        previewImage.value = URL.createObjectURL(file);
        form.photo = file;
    }
};

const createCustomer = () => {
    form.post(route('customers.store'), {
        preserveScroll: true,
        onSuccess: () => {
            showToast();
        },
        onError: () => nameInput.value.focus(),
    });
};
</script>

<template>
    <Head title="Customer"/>

    <AuthenticatedLayout>
        <template #breadcrumb>
            Customers > Create
        </template>

        <div class="flex flex-wrap">
            <div class="w-full px-4">
                <div class="relative flex flex-col min-w-0 break-words w-full mb-6 shadow-lg rounded bg-white">
                    <div class="rounded-t mb-3 px-4 py-3 border-0">
                        <div class="flex flex-wrap items-center">
                            <div class="relative w-full px-4 max-w-full flex-grow flex-1">
                                <div class="flex justify-between items-center">
                                    <h4 class="text-2xl font-semibold">Create Customer</h4>
                                    <Button
                                        :href="route('customers.index')"
                                        buttonType="link"
                                    >
                                        Go Back
                                    </Button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="block w-full overflow-x-auto px-8 py-4">
                        <form @submit.prevent="createCustomer">
                            
                            <!-- Customer Information Section -->
                            <div class="mb-8">
                                <h5 class="text-lg font-semibold text-gray-700 mb-4 pb-2 border-b-2 border-emerald-500">
                                    <i class="fa fa-user mr-2"></i>Customer Information
                                </h5>
                                <div class="grid gap-4 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
                                    <div class="flex flex-col">
                                        <label for="name" class="text-stone-600 text-sm font-medium">
                                            Customer Name <span class="text-red-500">*</span>
                                        </label>
                                        <input
                                            id="name"
                                            ref="nameInput"
                                            v-model="form.name"
                                            type="text"
                                            placeholder="Enter customer name"
                                            class="mt-2 block w-full rounded-md border border-gray-200 px-3 py-2 shadow-sm outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"
                                        />
                                        <InputError :message="form.errors.name"/>
                                    </div>

                                    <div class="flex flex-col">
                                        <label for="nama_box" class="text-stone-600 text-sm font-medium">
                                            Nama Box
                                        </label>
                                        <input
                                            id="nama_box"
                                            v-model="form.nama_box"
                                            type="text"
                                            placeholder="Enter nama box"
                                            class="mt-2 block w-full rounded-md border border-gray-200 px-3 py-2 shadow-sm outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"
                                        />
                                        <InputError :message="form.errors.nama_box"/>
                                    </div>

                                    <div class="flex flex-col">
                                        <label for="nama_owner" class="text-stone-600 text-sm font-medium">
                                            Nama Owner
                                        </label>
                                        <input
                                            id="nama_owner"
                                            v-model="form.nama_owner"
                                            type="text"
                                            placeholder="Enter nama owner"
                                            class="mt-2 block w-full rounded-md border border-gray-200 px-3 py-2 shadow-sm outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"
                                        />
                                        <InputError :message="form.errors.nama_owner"/>
                                    </div>

                                    <div class="flex flex-col">
                                        <label for="email" class="text-stone-600 text-sm font-medium">
                                            Email <span class="text-red-500">*</span>
                                        </label>
                                        <input
                                            id="email"
                                            v-model="form.email"
                                            type="email"
                                            placeholder="Enter email"
                                            class="mt-2 block w-full rounded-md border border-gray-200 px-3 py-2 shadow-sm outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"
                                        />
                                        <InputError :message="form.errors.email"/>
                                    </div>

                                    <div class="flex flex-col">
                                        <label for="phone" class="text-stone-600 text-sm font-medium">
                                            Phone <span class="text-red-500">*</span>
                                        </label>
                                        <input
                                            id="phone"
                                            v-model="form.phone"
                                            type="text"
                                            placeholder="Enter phone number"
                                            class="mt-2 block w-full rounded-md border border-gray-200 px-3 py-2 shadow-sm outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"
                                        />
                                        <InputError :message="form.errors.phone"/>
                                    </div>

                                    <div class="flex flex-col">
                                        <label for="tanggal_join" class="text-stone-600 text-sm font-medium">
                                            Tanggal Join
                                        </label>
                                        <input
                                            id="tanggal_join"
                                            v-model="form.tanggal_join"
                                            type="date"
                                            class="mt-2 block w-full rounded-md border border-gray-200 px-3 py-2 shadow-sm outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"
                                        />
                                        <InputError :message="form.errors.tanggal_join"/>
                                    </div>

                                    <div class="flex flex-col">
                                        <label for="status_customer" class="text-stone-600 text-sm font-medium">
                                            Status Customer
                                        </label>
                                        <select
                                            id="status_customer"
                                            v-model="form.status_customer"
                                            class="mt-2 block w-full rounded-md border border-gray-200 px-3 py-2 shadow-sm outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"
                                        >
                                            <option value="baru">New</option>
                                            <option value="repeat">Repeat</option>
                                        </select>
                                        <InputError :message="form.errors.status_customer"/>
                                    </div>

                                    <div class="flex flex-col md:col-span-2">
                                        <label for="photo" class="text-stone-600 text-sm font-medium mb-2">
                                            Customer Photo
                                        </label>
                                        <div class="flex items-start gap-4">
                                            <div class="relative w-32 h-32 border-2 border-dashed border-gray-300 rounded-lg overflow-hidden">
                                                <img 
                                                    :src="previewImage || default_image" 
                                                    alt="Preview" 
                                                    class="w-full h-full object-cover"
                                                />
                                            </div>
                                            <div class="flex-1">
                                                <label class="cursor-pointer inline-flex items-center px-4 py-2 bg-emerald-500 text-white rounded-md hover:bg-emerald-600 transition">
                                                    <i class="fa fa-upload mr-2"></i>
                                                    Choose Photo
                                                    <input
                                                        ref="fileInput"
                                                        type="file"
                                                        class="hidden"
                                                        accept="image/png, image/jpeg, image/jpg, image/gif, image/svg"
                                                        @change="handleFileChange"
                                                    />
                                                </label>
                                                <p class="text-xs text-gray-500 mt-2">
                                                    Max file size: 1MB. Supported formats: JPG, PNG, GIF, SVG
                                                </p>
                                                <InputError :message="form.errors.photo"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Divider -->
                            <hr class="my-8 border-gray-200"/>

                            <!-- Sales Information Section -->
                            <div class="mb-8">
                                <h5 class="text-lg font-semibold text-gray-700 mb-4 pb-2 border-b-2 border-blue-500">
                                    <i class="fa fa-handshake mr-2"></i>Sales Information
                                </h5>
                                <div class="grid gap-4 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
                                    <div class="flex flex-col">
                                        <label for="sales_id" class="text-stone-600 text-sm font-medium">
                                            Sales Person
                                        </label>
                                        <select
                                            id="sales_id"
                                            v-model="form.sales_id"
                                            class="mt-2 block w-full rounded-md border border-gray-200 px-3 py-2 shadow-sm outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"
                                        >
                                            <option :value="null">-- Select Sales Person --</option>
                                            <option v-for="sales in salesList" :key="sales.id" :value="sales.id">
                                                {{ sales.name }}
                                            </option>
                                        </select>
                                        <InputError :message="form.errors.sales_id"/>
                                    </div>
                                </div>
                            </div>

                            <!-- Divider -->
                            <hr class="my-8 border-gray-200"/>

                            <!-- Commission Information Section -->
                            <div class="mb-8">
                                <h5 class="text-lg font-semibold text-gray-700 mb-4 pb-2 border-b-2 border-amber-500">
                                    <i class="fa fa-money-bill-wave mr-2"></i>Commission Information
                                </h5>
                                <div class="grid gap-4 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
                                    <div class="flex flex-col">
                                        <label for="status_komisi" class="text-stone-600 text-sm font-medium">
                                            Status Komisi
                                        </label>
                                        <input
                                            id="status_komisi"
                                            v-model="form.status_komisi"
                                            type="text"
                                            placeholder="e.g., Active, Inactive"
                                            class="mt-2 block w-full rounded-md border border-gray-200 px-3 py-2 shadow-sm outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"
                                        />
                                        <InputError :message="form.errors.status_komisi"/>
                                    </div>

                                    <div class="flex flex-col">
                                        <label for="harga_komisi_standar" class="text-stone-600 text-sm font-medium">
                                            Komisi Standar
                                        </label>
                                        <div class="relative mt-2">
                                            <span class="absolute left-3 top-2.5 text-gray-500 font-medium">Rp</span>
                                            <input
                                                id="harga_komisi_standar"
                                                v-model="form.harga_komisi_standar"
                                                type="number"
                                                step="0.01"
                                                min="0"
                                                placeholder="0"
                                                class="block w-full rounded-md border border-gray-200 pl-10 pr-3 py-2 shadow-sm outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"
                                            />
                                        </div>
                                        <InputError :message="form.errors.harga_komisi_standar"/>
                                    </div>

                                    <div class="flex flex-col">
                                        <label for="harga_komisi_extra" class="text-stone-600 text-sm font-medium">
                                            Komisi Extra
                                        </label>
                                        <div class="relative mt-2">
                                            <span class="absolute left-3 top-2.5 text-gray-500 font-medium">Rp</span>
                                            <input
                                                id="harga_komisi_extra"
                                                v-model="form.harga_komisi_extra"
                                                type="number"
                                                step="0.01"
                                                min="0"
                                                placeholder="0"
                                                class="block w-full rounded-md border border-gray-200 pl-10 pr-3 py-2 shadow-sm outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"
                                            />
                                        </div>
                                        <InputError :message="form.errors.harga_komisi_extra"/>
                                    </div>
                                </div>
                            </div>

                            <!-- Divider -->
                            <hr class="my-8 border-gray-200"/>

                            <!-- Address Section -->
                            <div class="mb-8">
                                <h5 class="text-lg font-semibold text-gray-700 mb-4 pb-2 border-b-2 border-purple-500">
                                    <i class="fa fa-map-marker-alt mr-2"></i>Address Information
                                </h5>
                                <div class="grid gap-4 sm:grid-cols-1">
                                    <div class="flex flex-col">
                                        <label for="address" class="text-stone-600 text-sm font-medium">
                                            Address
                                        </label>
                                        <textarea
                                            id="address"
                                            v-model="form.address"
                                            rows="4"
                                            placeholder="Enter full address"
                                            class="mt-2 block w-full rounded-md border border-gray-200 px-3 py-2 shadow-sm outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"
                                        ></textarea>
                                        <InputError :message="form.errors.address"/>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-gray-200">
                                <Button
                                    :href="route('customers.index')"
                                    buttonType="link"
                                    type="secondary"
                                >
                                    Cancel
                                </Button>
                                <SubmitButton
                                    :processing="form.processing"
                                    :disabled="form.processing"
                                >
                                    <i class="fa fa-save mr-2"></i>
                                    Create Customer
                                </SubmitButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Custom styling for better visual hierarchy */
h5 {
    position: relative;
}

h5::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    width: 50px;
    height: 2px;
}
</style>
