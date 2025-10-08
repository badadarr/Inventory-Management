import {usePage} from "@inertiajs/vue3";
import {push} from "notivue";

export function truncateString(str, maxLength = 10) {
    if (str.length <= maxLength) {
        return str;
    }
    return str.slice(0, maxLength) + '...';
}

export function getCurrency() {
    return usePage().props.currency;
}

export function numberFormat(number) {
    const num = parseFloat(number);
    if (isNaN(num)) return 0;
    return parseFloat(num.toFixed(usePage().props.decimal_point));
}

export function formatDatetime(datetime) {
    const date = new Date(datetime);

    const options = {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        hour12: true
    };

    return date.toLocaleString('en-US', options);
}

export function formatDate(date) {
    if (!date) return '';
    const d = new Date(date);
    const options = {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    };
    return d.toLocaleString('en-US', options);
}

export function showToast() {
    if (usePage().props.flash.isSuccess) {
        push.success(usePage().props.flash.message)
    } else {
        push.error(usePage().props.flash.message)
    }
}
