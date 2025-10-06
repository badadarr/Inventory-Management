<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, useForm, router, usePage} from '@inertiajs/vue3';
import CardTable from "@/Components/Cards/CardTable.vue";
import TableData from "@/Components/TableData.vue";
import {ref, watch} from 'vue';
import {truncateString} from "@/Utils/Helper.js";
import {push} from 'notivue';

const props = defineProps({
    filters: {
        type: Object
    },
    users: {
        type: Object
    },
    roles: {
        type: Object
    },
});

const tableHeads = ref(['#', "Name", "Email", "Role", "Company", "Actions"]);
const showModal = ref(false);
const isEdit = ref(false);

const form = useForm({
    id: null,
    name: '',
    email: '',
    password: '',
    role: 'admin',
    company_name: '',
    company_id: null,
});

const openCreateModal = () => {
    form.reset();
    form.clearErrors();
    isEdit.value = false;
    showModal.value = true;
};

const openEditModal = (user) => {
    form.id = user.id;
    form.name = user.name;
    form.email = user.email;
    form.password = '';
    form.role = user.role;
    form.company_name = user.company_name;
    form.company_id = user.company_id;
    isEdit.value = true;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    form.reset();
    form.clearErrors();
};

const submit = () => {
    if (isEdit.value) {
        form.put(route('users.update', form.id), {
            onSuccess: () => closeModal()
        });
    } else {
        form.post(route('users.store'), {
            onSuccess: () => closeModal()
        });
    }
};

const deleteUser = (userId) => {
    if (confirm('Are you sure you want to delete this user?')) {
        router.delete(route('users.destroy', userId));
    }
};

// Watch for flash messages
const page = usePage();
watch(() => page.props.flash, (flash) => {
    if (flash.message) {
        push[flash.isSuccess ? 'success' : 'error']({
            message: flash.message,
            duration: 3000
        });
    }
}, { deep: true });
</script>

<template>
    <Head title="User"/>

    <AuthenticatedLayout>
        <template #breadcrumb>
            Users
        </template>

        <div class="flex flex-wrap">
            <div class="w-full px-4">
                <CardTable
                    indexRoute="users.index"
                    :paginatedData="users"
                    :filters="filters"
                    :tableHeads="tableHeads"
                >
                    <template #cardHeader>
                        <div class="flex justify-between items-center">
                            <h4 class="text-2xl">Users ({{users.total}})</h4>
                            <button
                                @click="openCreateModal"
                                class="bg-emerald-500 text-white active:bg-emerald-600 font-bold uppercase text-xs px-4 py-2 rounded shadow hover:shadow-md outline-none focus:outline-none ease-linear transition-all duration-150"
                                type="button"
                            >
                                <i class="fas fa-plus mr-2"></i> Add User
                            </button>
                        </div>
                    </template>

                    <tr v-for="(user, index) in users.data" :key="user.id">
                        <TableData>
                            {{ (users.current_page * users.per_page) - (users.per_page - (index + 1)) }}
                        </TableData>
                        <TableData class="text-left flex items-center" :title="user.name">
                            <img
                                :src="user.photo"
                                class="h-12 w-12 bg-white rounded-full border"
                                alt="Inventory management system"
                            />
                            <span class="ml-3 font-bold text-blueGray-600">{{ truncateString(user.name, 20) }}</span>
                        </TableData>
                        <TableData>{{ user.email }}</TableData>
                        <TableData>
                            <span class="text-xs font-semibold inline-block py-1 px-2 rounded text-blue-600 bg-blue-200">
                                {{ user.role_label }}
                            </span>
                        </TableData>
                        <TableData>{{ user.company_name || '-' }}</TableData>
                        <TableData>
                            <span v-if="user.id === $page.props.auth.user.id" class="text-xs font-semibold inline-block py-1 px-2 rounded text-green-600 bg-green-200">
                                Current Login
                            </span>
                            <template v-else>
                                <button
                                    @click="openEditModal(user)"
                                    class="text-blue-500 hover:text-blue-700 mr-3"
                                    title="Edit"
                                >
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button
                                    @click="deleteUser(user.id)"
                                    class="text-red-500 hover:text-red-700"
                                    title="Delete"
                                >
                                    <i class="fas fa-trash"></i>
                                </button>
                            </template>
                        </TableData>
                    </tr>
                </CardTable>
            </div>
        </div>

        <!-- Modal -->
        <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form @submit.prevent="submit">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ isEdit ? 'Edit User' : 'Create User' }}</h3>
                            
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Name</label>
                                <input v-model="form.name" type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                                <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                                <input v-model="form.email" type="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                                <div v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</div>
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Password {{ isEdit ? '(leave blank to keep current)' : '' }}</label>
                                <input v-model="form.password" type="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" :required="!isEdit">
                                <div v-if="form.errors.password" class="text-red-500 text-xs mt-1">{{ form.errors.password }}</div>
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Role</label>
                                <select v-model="form.role" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                                    <option v-for="(label, value) in roles" :key="value" :value="value">{{ label }}</option>
                                </select>
                                <div v-if="form.errors.role" class="text-red-500 text-xs mt-1">{{ form.errors.role }}</div>
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Company Name</label>
                                <input v-model="form.company_name" type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <div v-if="form.errors.company_name" class="text-red-500 text-xs mt-1">{{ form.errors.company_name }}</div>
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Company ID</label>
                                <input v-model="form.company_id" type="number" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <div v-if="form.errors.company_id" class="text-red-500 text-xs mt-1">{{ form.errors.company_id }}</div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" :disabled="form.processing" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-emerald-600 text-base font-medium text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 sm:ml-3 sm:w-auto sm:text-sm">
                                {{ isEdit ? 'Update' : 'Create' }}
                            </button>
                            <button type="button" @click="closeModal" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
