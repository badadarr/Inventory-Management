import { usePage } from '@inertiajs/vue3';

export function usePermissions() {
    const page = usePage();
    
    const hasPermission = (menu) => {
        const permissions = page.props.auth.permissions || [];
        return permissions.includes(menu);
    };

    const canAccessMasterData = () => {
        return hasPermission('master_data');
    };

    const canAccessCategories = () => {
        return hasPermission('categories');
    };

    const canAccessUnitTypes = () => {
        return hasPermission('unit_types');
    };

    const canAccessProducts = () => {
        return hasPermission('products');
    };

    const canAccessSuppliers = () => {
        return hasPermission('suppliers');
    };

    const canAccessCustomers = () => {
        return hasPermission('customers');
    };

    const canAccessSales = () => {
        return hasPermission('sales');
    };

    const canAccessPOS = () => {
        return hasPermission('pos');
    };

    const canAccessOrders = () => {
        return hasPermission('orders');
    };

    const canAccessTransactions = () => {
        return hasPermission('transactions');
    };

    const canAccessSalesPoints = () => {
        return hasPermission('sales_points');
    };

    const canAccessReports = () => {
        return hasPermission('reports');
    };

    const canAccessOutstanding = () => {
        return hasPermission('outstanding');
    };

    const canAccessTopCustomers = () => {
        return hasPermission('top_customers');
    };

    const canAccessFinance = () => {
        return hasPermission('finance');
    };

    const canAccessSalaries = () => {
        return hasPermission('salaries');
    };

    const canAccessExpenses = () => {
        return hasPermission('expenses');
    };

    const canAccessEmployees = () => {
        return hasPermission('employees');
    };

    const canAccessSettings = () => {
        return hasPermission('settings');
    };

    const canAccessUsers = () => {
        return hasPermission('users');
    };

    return {
        hasPermission,
        canAccessMasterData,
        canAccessCategories,
        canAccessUnitTypes,
        canAccessProducts,
        canAccessSuppliers,
        canAccessCustomers,
        canAccessSales,
        canAccessPOS,
        canAccessOrders,
        canAccessTransactions,
        canAccessSalesPoints,
        canAccessReports,
        canAccessOutstanding,
        canAccessTopCustomers,
        canAccessFinance,
        canAccessSalaries,
        canAccessExpenses,
        canAccessEmployees,
        canAccessSettings,
        canAccessUsers,
    };
}
