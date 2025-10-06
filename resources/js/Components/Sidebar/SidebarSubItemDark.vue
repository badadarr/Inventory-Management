<template>
    <li class="items-center">
        <Link
            :href="disabled ? '#' : route(routeName)"
            :class="[
                'text-xs uppercase py-3 px-4 font-normal block transition-all duration-200',
                isActive
                    ? 'text-white bg-emerald-500'
                    : 'text-emerald-50 hover:text-white hover:bg-emerald-500',
                disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'
            ]"
            @click="disabled ? $event.preventDefault() : null"
        >
            <i class="fas fa-circle text-xs mr-2" style="font-size: 6px;"></i>
            {{ name }}
            <span v-if="disabled" class="text-xs text-emerald-300 ml-2">(Soon)</span>
        </Link>
    </li>
</template>

<script setup>
import {Link, usePage} from "@inertiajs/vue3";
import {computed} from "vue";

const props = defineProps({
    name: String,
    routeName: String,
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
