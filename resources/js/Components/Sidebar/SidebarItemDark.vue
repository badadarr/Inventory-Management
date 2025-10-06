<template>
    <li class="items-center">
        <Link
            :href="disabled ? '#' : route(routeName)"
            :class="[
                'text-xs uppercase py-3 font-bold block transition-all duration-200',
                isActive
                    ? 'text-white bg-emerald-600'
                    : 'text-emerald-100 hover:text-white hover:bg-emerald-600',
                disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'
            ]"
            @click="disabled ? $event.preventDefault() : null"
        >
            <i
                :class="[
                    icon,
                    'mr-2 text-sm',
                    isActive ? 'text-white' : 'text-emerald-200'
                ]"
            ></i>
            {{ name }}
            <span v-if="disabled" class="text-xs text-gray-500 ml-2">(Coming Soon)</span>
        </Link>
    </li>
</template>

<script setup>
import {Link, usePage} from "@inertiajs/vue3";
import {computed} from "vue";

const props = defineProps({
    name: String,
    routeName: String,
    icon: String,
    disabled: {
        type: Boolean,
        default: false
    }
});

const page = usePage();

const isActive = computed(() => {
    if (props.disabled) return false;
    return route().current(props.routeName);
});
</script>
