<template>
    <nav
        class="md:left-0 md:block md:fixed md:top-0 md:bottom-0 md:overflow-y-auto md:flex-row md:flex-nowrap md:overflow-hidden shadow-xl bg-emerald-700 flex flex-wrap items-center justify-between relative md:w-64 z-10 py-4 px-6"
    >
        <div
            class="md:flex-col md:items-stretch md:min-h-full md:flex-nowrap px-0 flex flex-wrap items-center justify-between w-full mx-auto"
        >
            <!-- Toggler -->
            <button
                class="cursor-pointer text-white opacity-50 md:hidden px-3 py-1 text-xl leading-none bg-transparent rounded border border-solid border-transparent"
                type="button"
                v-on:click="toggleCollapseShow('bg-emerald-600 m-2 py-3 px-6')"
            >
                <i class="fas fa-bars"></i>
            </button>

            <!-- Company Branding -->
            <div class="hidden md:block w-full px-4 py-6 border-b border-emerald-600">
                <h1 class="text-white text-lg font-bold">{{ $page.props.auth.user.company_name || 'PT. Company Name' }}</h1>
                <p class="text-emerald-100 text-xs mt-1">Inventory Management System</p>
            </div>

            <!-- Mobile Brand -->
            <Link
                class="md:hidden text-left text-white mr-0 inline-block whitespace-nowrap text-sm font-bold p-4 px-0"
                href="/"
            >
                {{ $page.props.auth.user.company_name || 'Company Name' }}
            </Link>

            <!-- User (Mobile) -->
            <ul class="md:hidden items-center flex flex-wrap list-none">
                <li class="inline-block relative">
                    <notification-dropdown/>
                </li>
                <li class="inline-block relative">
                    <user-dropdown/>
                </li>
            </ul>

            <!-- Collapse -->
            <div
                class="md:flex md:flex-col md:items-stretch md:opacity-100 md:relative md:mt-4 md:shadow-none shadow absolute top-0 left-0 right-0 z-40 overflow-y-auto overflow-x-hidden h-auto items-center flex-1 rounded"
                v-bind:class="collapseShow"
            >
                <!-- Collapse header (Mobile) -->
                <div
                    class="md:min-w-full md:hidden block pb-4 mb-4 border-b border-solid border-emerald-600"
                >
                    <div class="flex flex-wrap">
                        <div class="w-6/12">
                            <Link
                                class="text-left text-white mr-0 inline-block whitespace-nowrap text-sm font-bold p-4 px-0"
                                href="/"
                            >
                                {{ $page.props.auth.user.company_name || 'Company Name' }}
                            </Link>
                        </div>
                        <div class="w-6/12 flex justify-end">
                            <button
                                type="button"
                                class="cursor-pointer text-white opacity-50 md:hidden px-3 py-1 text-xl leading-none bg-transparent rounded border border-solid border-transparent"
                                v-on:click="toggleCollapseShow('hidden')"
                            >
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Search Form (Mobile) -->
                <form class="mt-6 mb-4 md:hidden px-4" @submit.prevent="searchProduct">
                    <div class="mb-3 pt-0">
                        <input
                            type="text"
                            v-model="form.keyword"
                            placeholder="Search product..."
                            class="border-0 px-3 py-2 h-12 border border-solid border-emerald-600 placeholder-emerald-200 text-white bg-emerald-600 rounded text-base leading-snug shadow-none outline-none focus:outline-none focus:ring-2 focus:ring-white w-full font-normal"
                        />
                    </div>
                </form>

                <!-- Divider -->
                <hr class="my-4 md:min-w-full border-emerald-600"/>

                <!-- Navigation -->
                <ul class="md:flex-col md:min-w-full flex flex-col list-none">
                    <SidebarItemDark
                        name="Dashboard"
                        routeName="dashboard"
                        icon="fas fa-tv"
                    />

                    <!-- Master Data -->
                    <SidebarCollapsibleItemDark
                        v-if="permissions.canAccessMasterData()"
                        title="Master Data"
                        icon="fas fa-database"
                        :routes="['categories.index', 'unit-types.index', 'products.index', 'suppliers.index', 'customers.index', 'sales.index']"
                    >
                        <SidebarSubItemDark v-if="permissions.canAccessCategories()" name="Categories" routeName="categories.index" />
                        <SidebarSubItemDark v-if="permissions.canAccessUnitTypes()" name="Unit Types" routeName="unit-types.index" />
                        <SidebarSubItemDark v-if="permissions.canAccessProducts()" name="Products" routeName="products.index" />
                        <SidebarSubItemDark v-if="permissions.canAccessSuppliers()" name="Suppliers" routeName="suppliers.index" />
                        <SidebarSubItemDark v-if="permissions.canAccessCustomers()" name="Customers" routeName="customers.index" />
                        <SidebarSubItemDark v-if="permissions.canAccessSales()" name="Sales" routeName="sales.index" />
                    </SidebarCollapsibleItemDark>

                    <!-- Purchasing (Future) -->
                    <SidebarCollapsibleItemDark
                        title="Purchasing"
                        icon="fas fa-shopping-bag"
                        :routes="[]"
                        :disabled="true"
                    >
                        <SidebarSubItemDark name="Purchase Orders" routeName="#" :disabled="true" />
                        <SidebarSubItemDark name="Purchase Requests" routeName="#" :disabled="true" />
                    </SidebarCollapsibleItemDark>

                    <!-- Inventory -->
                    <SidebarCollapsibleItemDark
                        v-if="permissions.canAccessPOS() || permissions.canAccessOrders()"
                        title="Inventory"
                        icon="fas fa-boxes"
                        :routes="['carts.index', 'orders.index']"
                    >
                        <SidebarSubItemDark v-if="permissions.canAccessPOS()" name="POS" routeName="carts.index" />
                        <SidebarSubItemDark v-if="permissions.canAccessOrders()" name="Orders" routeName="orders.index" />
                        <SidebarSubItemDark name="Stock Movement" routeName="#" :disabled="true" />
                    </SidebarCollapsibleItemDark>

                    <!-- Reports -->
                    <SidebarCollapsibleItemDark
                        v-if="permissions.canAccessReports() || permissions.canAccessTransactions() || permissions.canAccessSalesPoints() || permissions.canAccessOutstanding() || permissions.canAccessTopCustomers()"
                        title="Reports"
                        icon="fas fa-chart-bar"
                        :routes="['transactions.index', 'sales-points.index', 'reports.outstanding', 'reports.top-customers']"
                    >
                        <SidebarSubItemDark v-if="permissions.canAccessTransactions()" name="Transactions" routeName="transactions.index" />
                        <SidebarSubItemDark v-if="permissions.canAccessSalesPoints()" name="Sales Points" routeName="sales-points.index" />
                        <SidebarSubItemDark v-if="permissions.canAccessOutstanding()" name="Outstanding" routeName="reports.outstanding" />
                        <SidebarSubItemDark v-if="permissions.canAccessTopCustomers()" name="Top Customers" routeName="reports.top-customers" />
                        <SidebarSubItemDark name="Sales Report" routeName="#" :disabled="true" />
                        <SidebarSubItemDark name="Stock Report" routeName="#" :disabled="true" />
                    </SidebarCollapsibleItemDark>

                    <!-- Finance -->
                    <SidebarCollapsibleItemDark
                        v-if="permissions.canAccessFinance() || permissions.canAccessSalaries() || permissions.canAccessExpenses()"
                        title="Finance"
                        icon="fas fa-dollar-sign"
                        :routes="['salaries.index', 'expenses.index']"
                    >
                        <SidebarSubItemDark v-if="permissions.canAccessSalaries()" name="Salary" routeName="salaries.index" />
                        <SidebarSubItemDark v-if="permissions.canAccessExpenses()" name="Expenses" routeName="expenses.index" />
                    </SidebarCollapsibleItemDark>

                    <!-- Others -->
                    <SidebarCollapsibleItemDark
                        title="Others"
                        icon="fas fa-ellipsis-h"
                        :routes="['employees.index', 'users.index', 'profile.edit']"
                    >
                        <SidebarSubItemDark v-if="permissions.canAccessEmployees()" name="Employees" routeName="employees.index" />
                        <SidebarSubItemDark v-if="permissions.canAccessUsers()" name="Users" routeName="users.index" />
                        <SidebarSubItemDark name="Settings" routeName="profile.edit" />
                    </SidebarCollapsibleItemDark>

                    <!-- Adjustment Stock (Future) -->
                    <SidebarItemDark
                        name="Adjustment Stock"
                        routeName="#"
                        icon="fas fa-balance-scale"
                        :disabled="true"
                    />

                    <!-- Mutasi (Future) -->
                    <SidebarItemDark
                        name="Mutasi"
                        routeName="#"
                        icon="fas fa-exchange-alt"
                        :disabled="true"
                    />
                </ul>

                <!-- Divider -->
                <hr class="my-4 md:min-w-full border-emerald-600"/>

                <!-- User Info (Desktop) -->
                <div class="hidden md:block px-4 py-4 border-t border-emerald-600 mt-auto">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center">
                            <i class="fas fa-user text-emerald-700"></i>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-white text-sm font-medium">{{ $page.props.auth.user.name }}</p>
                            <p class="text-emerald-100 text-xs">{{ $page.props.auth.user.role_label }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</template>

<script setup>
import {useForm, usePage} from "@inertiajs/vue3";
import { usePermissions } from "@/Utils/permissions.js";

const form = useForm({
    keyword: null,
});

const permissions = usePermissions();

const searchProduct = () => {
    form.get(route('carts.index'), {
        preserveScroll: true,
    });
};
</script>

<script>
import NotificationDropdown from "@/Components/Dropdowns/NotificationDropdown.vue";
import UserDropdown from "@/Components/Dropdowns/UserDropdown.vue";
import {Link} from "@inertiajs/vue3";
import SidebarItemDark from "@/Components/Sidebar/SidebarItemDark.vue";
import SidebarCollapsibleItemDark from "@/Components/Sidebar/SidebarCollapsibleItemDark.vue";
import SidebarSubItemDark from "@/Components/Sidebar/SidebarSubItemDark.vue";

export default {
    data() {
        return {
            collapseShow: "hidden",
        };
    },
    methods: {
        toggleCollapseShow: function (classes) {
            this.collapseShow = classes;
        },
    },
    components: {
        SidebarItemDark,
        SidebarCollapsibleItemDark,
        SidebarSubItemDark,
        NotificationDropdown,
        UserDropdown,
        Link,
    },
};
</script>
