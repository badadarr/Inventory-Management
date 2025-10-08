<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import CardTable from "@/Components/Cards/CardTable.vue";
import TableData from "@/Components/TableData.vue";
import Button from "@/Components/Button.vue";
import Modal from "@/Components/Modal.vue";
import {useForm} from '@inertiajs/vue3';
import {ref} from 'vue';
import {showToast} from "@/Utils/Helper.js";

defineProps({
    filters: {
        type: Object
    },
    customers: {
        type: Object
    },
});

const selectedCustomer = ref(null);
const showDeleteModal = ref(false);
const tableHeads = ref(['#', "Customer", "Contact", "Sales Person", "Commission", "Orders", "Status", "Action"]);

const form = useForm({});

const deleteCustomerModal = (customer) => {
    selectedCustomer.value = customer;
    showDeleteModal.value = true;
};

const deleteCustomer = () => {
    form.delete(route('customers.destroy', selectedCustomer.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
            showToast();
        },
    });
};

const closeModal = () => {
    showDeleteModal.value = false;
    form.reset();
};
</script>

<template>
    <Head title="Customer"/>

    <AuthenticatedLayout>
        <template #breadcrumb>
            Customers
        </template>

        <div class="flex flex-wrap">
            <div class="w-full px-4">
                <CardTable
                    indexRoute="customers.index"
                    :paginatedData="customers"
                    :filters="filters"
                    :tableHeads="tableHeads"
                >
                    <template #cardHeader>
                        <div class="flex justify-between items-center">
                            <h4 class="text-2xl">Apply filters({{customers.total}})</h4>
                            <Button :href="route('customers.create')" buttonType="link">
                                <i class="fa fa-plus mr-2"></i>Create Customer
                            </Button>
                        </div>
                    </template>

                    <tr v-for="(customer, index) in customers.data" :key="customer.id">
                        <TableData>
                            {{ (customers.current_page * customers.per_page) - (customers.per_page - (index + 1)) }}
                        </TableData>
                        <!-- Customer Info -->
                        <TableData class="text-left">
                            <div class="flex items-center">
                                <img
                                    :src="customer.photo"
                                    class="h-12 w-12 bg-white rounded-full border object-cover"
                                    alt="Customer photo"
                                />
                                <div class="ml-3">
                                    <p class="font-bold text-blueGray-600">{{ customer.name }}</p>
                                    <p v-if="customer.nama_box" class="text-xs text-gray-500">
                                        Box: {{ customer.nama_box }}
                                    </p>
                                </div>
                            </div>
                        </TableData>

                        <!-- Contact -->
                        <TableData class="text-left">
                            <div class="text-sm">
                                <p class="text-gray-700">
                                    <i class="fa fa-envelope text-gray-400 mr-1"></i>
                                    {{ customer.email || '-' }}
                                </p>
                                <p class="text-gray-700 mt-1">
                                    <i class="fa fa-phone text-gray-400 mr-1"></i>
                                    {{ customer.phone || '-' }}
                                </p>
                            </div>
                        </TableData>

                        <!-- Sales Person -->
                        <TableData class="text-left">
                            <span v-if="customer.sales" class="text-sm text-gray-700">
                                {{ customer.sales.name }}
                            </span>
                            <span v-else class="text-xs text-gray-400 italic">No sales assigned</span>
                        </TableData>

                        <!-- Commission -->
                        <TableData class="text-left">
                            <div v-if="customer.harga_komisi_standar || customer.harga_komisi_extra" class="text-sm">
                                <p v-if="customer.harga_komisi_standar" class="text-gray-700">
                                    <span class="text-xs text-gray-500">Std:</span>
                                    Rp {{ customer.harga_komisi_standar.toLocaleString('id-ID') }}
                                </p>
                                <p v-if="customer.harga_komisi_extra" class="text-gray-700">
                                    <span class="text-xs text-gray-500">Extra:</span>
                                    Rp {{ customer.harga_komisi_extra.toLocaleString('id-ID') }}
                                </p>
                            </div>
                            <span v-else class="text-xs text-gray-400 italic">No commission</span>
                        </TableData>

                        <!-- Orders Count -->
                        <TableData class="text-center">
                            <div class="flex flex-col items-center">
                                <span class="text-lg font-bold text-emerald-600">
                                    {{ customer.repeat_order_count || 0 }}
                                </span>
                                <span class="text-xs text-gray-500">orders</span>
                            </div>
                        </TableData>

                        <!-- Status -->
                        <TableData class="text-center">
                            <span 
                                v-if="customer.status_customer === 'repeat'"
                                class="px-2 py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded-full"
                            >
                                <i class="fa fa-sync-alt mr-1"></i>
                                Repeat
                            </span>
                            <span 
                                v-else
                                class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full"
                            >
                                <i class="fa fa-star mr-1"></i>
                                New
                            </span>
                        </TableData>

                        <!-- Actions -->
                        <TableData class="text-center">
                            <Button :href="route('customers.edit', customer.id)" buttonType="link">
                                <i class="fa fa-edit"></i>
                            </Button>
                            <Button
                                @click="deleteCustomerModal(customer)"
                                type="red"
                                buttonType="button"
                            >
                                <i class="fa fa-trash-alt"></i>
                            </Button>
                        </TableData>
                    </tr>
                </CardTable>
            </div>
        </div>

        <!--Delete data-->
        <Modal
            title="Delete"
            :show="showDeleteModal"
            :formProcessing="form.processing"
            @close="closeModal"
            @submitAction="deleteCustomer"
            maxWidth="sm"
            submitButtonText="Yes, delete it!"
        >
            Are you sure you want to delete this customer?
        </Modal>
    </AuthenticatedLayout>
</template>
