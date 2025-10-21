<script setup>
import { computed } from 'vue';

const props = defineProps({
    activities: {
        type: Array,
        required: true,
        default: () => []
    }
});

// Format date time
const formatDateTime = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleString('id-ID', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

// Get color classes for activity type
const getColorClasses = (color) => {
    const colorMap = {
        'blue': {
            bg: 'bg-blue-100',
            border: 'border-blue-300',
            icon: 'text-blue-600',
            dot: 'bg-blue-600'
        },
        'yellow': {
            bg: 'bg-yellow-100',
            border: 'border-yellow-300',
            icon: 'text-yellow-600',
            dot: 'bg-yellow-600'
        },
        'purple': {
            bg: 'bg-purple-100',
            border: 'border-purple-300',
            icon: 'text-purple-600',
            dot: 'bg-purple-600'
        },
        'green': {
            bg: 'bg-green-100',
            border: 'border-green-300',
            icon: 'text-green-600',
            dot: 'bg-green-600'
        },
        'emerald': {
            bg: 'bg-emerald-100',
            border: 'border-emerald-300',
            icon: 'text-emerald-600',
            dot: 'bg-emerald-600'
        },
        'red': {
            bg: 'bg-red-100',
            border: 'border-red-300',
            icon: 'text-red-600',
            dot: 'bg-red-600'
        },
        'gray': {
            bg: 'bg-gray-100',
            border: 'border-gray-300',
            icon: 'text-gray-600',
            dot: 'bg-gray-600'
        }
    };
    
    return colorMap[color] || colorMap['gray'];
};

// Format changes to readable text
const formatChanges = (activity) => {
    if (!activity.old_values || !activity.new_values) return null;
    
    const changes = [];
    const oldVals = activity.old_values;
    const newVals = activity.new_values;
    
    for (let key in newVals) {
        if (oldVals[key] !== undefined && oldVals[key] !== newVals[key]) {
            const fieldName = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
            changes.push(`${fieldName}: ${oldVals[key]} → ${newVals[key]}`);
        }
    }
    
    return changes.length > 0 ? changes : null;
};
</script>

<template>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 print:shadow-none print:border-gray-300">
        <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
            <i class="fas fa-history mr-2 text-gray-600"></i>
            Order Timeline & History
        </h3>

        <div v-if="activities && activities.length > 0" class="space-y-6">
            <!-- Timeline Items -->
            <div v-for="(activity, index) in activities" :key="activity.id" class="relative">
                <!-- Timeline Line -->
                <div 
                    v-if="index < activities.length - 1"
                    class="absolute left-[23px] top-12 w-0.5 h-full bg-gray-200"
                    style="height: calc(100% + 1.5rem)"
                ></div>

                <!-- Activity Card -->
                <div class="flex gap-4">
                    <!-- Icon Circle -->
                    <div class="flex-shrink-0 relative z-10">
                        <div 
                            :class="[
                                'w-12 h-12 rounded-full flex items-center justify-center border-4 border-white shadow',
                                getColorClasses(activity.color).dot
                            ]"
                        >
                            <i :class="['fas', activity.icon, 'text-white text-lg']"></i>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 pb-6">
                        <!-- Activity Header -->
                        <div :class="[
                            'p-4 rounded-lg border',
                            getColorClasses(activity.color).bg,
                            getColorClasses(activity.color).border
                        ]">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="font-bold text-gray-900">{{ activity.description }}</h4>
                                <span class="text-xs text-gray-600 whitespace-nowrap ml-4">
                                    {{ formatDateTime(activity.created_at) }}
                                </span>
                            </div>

                            <!-- User Info -->
                            <div v-if="activity.user" class="text-sm text-gray-600 mb-2">
                                <i class="fas fa-user text-xs mr-1"></i>
                                {{ activity.user.name }}
                            </div>

                            <!-- Changes Detail -->
                            <div v-if="formatChanges(activity)" class="mt-3 pt-3 border-t border-gray-300">
                                <p class="text-xs font-semibold text-gray-700 mb-2">Changes:</p>
                                <ul class="text-sm text-gray-700 space-y-1">
                                    <li v-for="(change, idx) in formatChanges(activity)" :key="idx" class="flex items-start">
                                        <i class="fas fa-arrow-right text-xs mr-2 mt-1 opacity-50"></i>
                                        <span>{{ change }}</span>
                                    </li>
                                </ul>
                            </div>

                            <!-- Notes -->
                            <div v-if="activity.notes" class="mt-3 pt-3 border-t border-gray-300">
                                <p class="text-xs font-semibold text-gray-700 mb-1">Notes:</p>
                                <p class="text-sm text-gray-700 italic">{{ activity.notes }}</p>
                            </div>

                            <!-- Special Display for New Values (for created/payment) -->
                            <div v-if="activity.activity_type === 'created' && activity.new_values" class="mt-3 pt-3 border-t border-gray-300">
                                <div class="grid grid-cols-2 gap-3 text-sm">
                                    <div v-if="activity.new_values.customer">
                                        <span class="text-gray-600">Customer:</span>
                                        <span class="ml-2 font-semibold text-gray-900">{{ activity.new_values.customer }}</span>
                                    </div>
                                    <div v-if="activity.new_values.total">
                                        <span class="text-gray-600">Total:</span>
                                        <span class="ml-2 font-semibold text-gray-900">Rp {{ Number(activity.new_values.total).toLocaleString('id-ID') }}</span>
                                    </div>
                                    <div v-if="activity.new_values.status">
                                        <span class="text-gray-600">Status:</span>
                                        <span class="ml-2 font-semibold text-gray-900 capitalize">{{ activity.new_values.status }}</span>
                                    </div>
                                </div>
                            </div>

                            <div v-if="activity.activity_type === 'payment_added' && activity.new_values" class="mt-3 pt-3 border-t border-gray-300">
                                <div class="grid grid-cols-2 gap-3 text-sm">
                                    <div v-if="activity.new_values.amount">
                                        <span class="text-gray-600">Amount:</span>
                                        <span class="ml-2 font-bold text-green-700">Rp {{ Number(activity.new_values.amount).toLocaleString('id-ID') }}</span>
                                    </div>
                                    <div v-if="activity.new_values.method">
                                        <span class="text-gray-600">Method:</span>
                                        <span class="ml-2 font-semibold text-gray-900 capitalize">{{ activity.new_values.method.replace('_', ' ') }}</span>
                                    </div>
                                    <div v-if="activity.new_values.total_paid">
                                        <span class="text-gray-600">Total Paid:</span>
                                        <span class="ml-2 font-bold text-gray-900">Rp {{ Number(activity.new_values.total_paid).toLocaleString('id-ID') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-12 text-gray-500">
            <i class="fas fa-inbox text-4xl mb-3 opacity-50"></i>
            <p class="text-lg">No activity recorded yet</p>
            <p class="text-sm mt-2">Activity history will appear here when actions are performed on this order</p>
        </div>
    </div>
</template>

<style scoped>
@media print {
    .print\:shadow-none {
        box-shadow: none !important;
    }
    
    .print\:border-gray-300 {
        border-color: #d1d5db !important;
    }
}
</style>
