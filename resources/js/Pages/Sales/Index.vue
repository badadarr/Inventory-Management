<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, useForm} from '@inertiajs/vue3';
import CardTable from "@/Components/Cards/CardTable.vue";
import TableData from "@/Components/TableData.vue";
import Button from "@/Components/Button.vue";
import Modal from "@/Components/Modal.vue";
import {nextTick, ref} from 'vue';
import DashboardInputGroup from "@/Components/DashboardInputGroup.vue";
import InputError from "@/Components/InputError.vue";
import {showToast} from "@/Utils/Helper.js";

defineProps({
    sales: {
        type: Array,
        default: () => []
    },
});

const selectedSales = ref(null);
const showCreateModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const nameInput = ref(null);
const tableHeads = ref(['#', "Name", "Phone", "Email", "Status", "Action"]);

const form = useForm({
    name: '',
    phone: '',
    email: '',
    address: '',
    photo: null,
    status: 'active',
});

const createModal = () => {
    showCreateModal.value = true;
};

const editModal = (sales) => {
    selectedSales.value = sales;
    form.name = sales.name || '';
    form.phone = sales.phone || '';
    form.email = sales.email || '';
    form.address = sales.address || '';
    form.status = sales.status;
    form.photo = null;
    showEditModal.value = true;
};

const deleteModal = (sales) => {
    selectedSales.value = sales;
    showDeleteModal.value = true;
};

const create = () => {
    form.post(route('sales.store'), {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
            showToast();
        },
    });
};

const update = () => {
    form.transform((data) => ({...data, _method: "put"}))
        .post(route('sales.update', selectedSales.value.id), {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
                showToast();
            },
        });
};

const deleteSales = () => {
    form.delete(route('sales.destroy', selectedSales.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
            showToast();
        },
    });
};

const closeModal = () => {
    showCreateModal.value = false;
    showEditModal.value = false;
    showDeleteModal.value = false;
    form.reset();
};
</script>

<template>
    <Head title="Sales"/>

    <AuthenticatedLayout>
        <template #breadcrumb>Sales</template>

        <div class="flex flex-wrap">
            <div class="w-full px-4">
                <CardTable :tableHeads="tableHeads">
                    <template #cardHeader>
                        <div class="flex justify-between items-center">
                            <h4 class="text-2xl">Sales List ({{ sales.length }})</h4>
                            <Button @click="createModal">Create Sales</Button>
                        </div>
                    </template>

                    <tr v-for="(item, index) in sales" :key="item.id">
                        <TableData>{{ index + 1 }}</TableData>
                        <TableData class="text-left flex items-center">
                            <img :src="item.photo" class="h-12 w-12 bg-white rounded-full border object-cover" alt="Sales"/>
                            <span class="ml-3 font-bold text-blueGray-600">{{ item.name }}</span>
                        </TableData>
                        <TableData>{{ item.phone }}</TableData>
                        <TableData>{{ item.email }}</TableData>
                        <TableData>
                            <span :class="item.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" class="px-2 py-1 rounded text-xs">
                                {{ item.status }}
                            </span>
                        </TableData>
                        <TableData>
                            <Button @click="editModal(item)"><i class="fa fa-edit"></i></Button>
                            <Button @click="deleteModal(item)" type="red"><i class="fa fa-trash-alt"></i></Button>
                        </TableData>
                    </tr>
                </CardTable>
            </div>
        </div>

        <Modal title="Create Sales" :show="showCreateModal" :formProcessing="form.processing" @close="closeModal" @submitAction="create">
            <div class="grid gap-4 sm:grid-cols-1 md:grid-cols-2">
                <DashboardInputGroup ref="nameInput" label="Name" name="name" v-model="form.name" placeholder="Enter name" :errorMessage="form.errors.name" @keyupEnter="create"/>
                <DashboardInputGroup label="Phone" name="phone" v-model="form.phone" placeholder="Enter phone" :errorMessage="form.errors.phone"/>
                <DashboardInputGroup label="Email" name="email" v-model="form.email" placeholder="Enter email" :errorMessage="form.errors.email" type="email"/>
                <div class="flex flex-col">
                    <label class="text-stone-600 text-sm font-medium">Status</label>
                    <select v-model="form.status" class="mt-2 block w-full rounded-md border border-gray-200 px-2 py-2 shadow-sm outline-none">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                    <InputError :message="form.errors.status"/>
                </div>
                <div class="flex flex-col md:col-span-2">
                    <label class="text-stone-600 text-sm font-medium">Address</label>
                    <textarea v-model="form.address" rows="2" placeholder="Enter address" class="mt-2 block w-full rounded-md border border-gray-200 px-2 py-2 shadow-sm outline-none"></textarea>
                    <InputError :message="form.errors.address"/>
                </div>
                <div class="flex flex-col">
                    <label class="w-full flex flex-col items-center px-4 py-6 bg-white text-blue rounded-lg shadow-lg tracking-wide uppercase border border-blue cursor-pointer hover:bg-blue hover:text-emerald-600">
                        <svg class="w-8 h-8" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M16.88 9.1A4 4 0 0 1 16 17H5a5 5 0 0 1-1-9.9V7a3 3 0 0 1 4.52-2.59A4.98 4.98 0 0 1 17 8c0 .38-.04.74-.12 1.1zM11 11h3l-4-4-4 4h3v3h2v-3z"/>
                        </svg>
                        <span v-if="form.photo" class="mt-2 text-base leading-normal">{{ form.photo.name.replace(/(^.{17}).*(\\..+$)/, "$1...$2") }}</span>
                        <span v-else class="mt-2 text-base leading-normal">Select a photo</span>
                        <input @input="form.photo = $event.target.files[0]" type='file' class="hidden" accept="image/*"/>
                    </label>
                    <InputError :message="form.errors.photo"/>
                </div>
            </div>
        </Modal>

        <Modal title="Edit Sales" :show="showEditModal" :formProcessing="form.processing" @close="closeModal" @submitAction="update">
            <div class="grid gap-4 sm:grid-cols-1 md:grid-cols-2">
                <DashboardInputGroup label="Name" name="name" v-model="form.name" placeholder="Enter name" :errorMessage="form.errors.name" @keyupEnter="update"/>
                <DashboardInputGroup label="Phone" name="phone" v-model="form.phone" placeholder="Enter phone" :errorMessage="form.errors.phone"/>
                <DashboardInputGroup label="Email" name="email" v-model="form.email" placeholder="Enter email" :errorMessage="form.errors.email" type="email"/>
                <div class="flex flex-col">
                    <label class="text-stone-600 text-sm font-medium">Status</label>
                    <select v-model="form.status" class="mt-2 block w-full rounded-md border border-gray-200 px-2 py-2 shadow-sm outline-none">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                    <InputError :message="form.errors.status"/>
                </div>
                <div class="flex flex-col md:col-span-2">
                    <label class="text-stone-600 text-sm font-medium">Address</label>
                    <textarea v-model="form.address" rows="2" placeholder="Enter address" class="mt-2 block w-full rounded-md border border-gray-200 px-2 py-2 shadow-sm outline-none"></textarea>
                    <InputError :message="form.errors.address"/>
                </div>
                <div class="flex flex-col">
                    <label class="w-full flex flex-col items-center px-4 py-6 bg-white text-blue rounded-lg shadow-lg tracking-wide uppercase border border-blue cursor-pointer hover:bg-blue hover:text-emerald-600">
                        <svg class="w-8 h-8" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M16.88 9.1A4 4 0 0 1 16 17H5a5 5 0 0 1-1-9.9V7a3 3 0 0 1 4.52-2.59A4.98 4.98 0 0 1 17 8c0 .38-.04.74-.12 1.1zM11 11h3l-4-4-4 4h3v3h2v-3z"/>
                        </svg>
                        <span v-if="form.photo" class="mt-2 text-base leading-normal">{{ form.photo.name.replace(/(^.{17}).*(\\..+$)/, "$1...$2") }}</span>
                        <span v-else class="mt-2 text-base leading-normal">Select a photo</span>
                        <input @input="form.photo = $event.target.files[0]" type='file' class="hidden" accept="image/*"/>
                    </label>
                    <InputError :message="form.errors.photo"/>
                </div>
            </div>
        </Modal>

        <Modal title="Delete" :show="showDeleteModal" :formProcessing="form.processing" @close="closeModal" @submitAction="deleteSales" maxWidth="sm" submitButtonText="Yes, delete it!">
            Are you sure you want to delete this sales?
        </Modal>
    </AuthenticatedLayout>
</template>
