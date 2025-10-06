<template>
    <li class="items-center">
        <a
            href="#"
            @click.prevent="toggleCollapse"
            :class="[
                'text-xs uppercase py-3 font-bold block transition-all duration-200',
                isAnyRouteActive
                    ? 'text-white bg-emerald-600'
                    : 'text-emerald-100 hover:text-white hover:bg-emerald-600',
                disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'
            ]"
        >
            <i
                :class="[
                    icon,
                    'mr-2 text-sm',
                    isAnyRouteActive ? 'text-white' : 'text-emerald-200'
                ]"
            ></i>
            {{ title }}
            <i
                :class="[
                    'fas fa-chevron-down float-right mt-1 transition-transform duration-200',
                    isOpen ? 'transform rotate-180' : ''
                ]"
            ></i>
            <span v-if="disabled" class="text-xs text-gray-500 ml-2">(Coming Soon)</span>
        </a>

        <transition
            enter-active-class="transition-all duration-200 ease-out"
            enter-from-class="opacity-0 max-h-0"
            enter-to-class="opacity-100 max-h-96"
            leave-active-class="transition-all duration-200 ease-in"
            leave-from-class="opacity-100 max-h-96"
            leave-to-class="opacity-0 max-h-0"
        >
            <ul v-show="isOpen" class="ml-4 overflow-hidden bg-emerald-600 rounded-md my-1">
                <slot></slot>
            </ul>
        </transition>
    </li>
</template>

<script setup>
import {computed, ref} from "vue";
import {usePage} from "@inertiajs/vue3";

const props = defineProps({
    title: String,
    icon: String,
    routes: {
        type: Array,
        default: () => []
    },
    disabled: {
        type: Boolean,
        default: false
    }
});

const page = usePage();
const isOpen = ref(false);

const isAnyRouteActive = computed(() => {
    if (props.disabled) return false;
    return props.routes.some(routeName => route().current(routeName));
});

// Auto-open if any child route is active
if (isAnyRouteActive.value) {
    isOpen.value = true;
}

const toggleCollapse = () => {
    if (!props.disabled) {
        isOpen.value = !isOpen.value;
    }
};
</script>
