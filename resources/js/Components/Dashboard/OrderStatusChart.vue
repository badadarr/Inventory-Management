<script setup>
import { Doughnut } from 'vue-chartjs'
import {
    Chart as ChartJS,
    ArcElement,
    Tooltip,
    Legend
} from 'chart.js'

ChartJS.register(ArcElement, Tooltip, Legend)

const props = defineProps({
    chartData: {
        type: Object,
        required: true
    }
})

const data = {
    labels: props.chartData.labels,
    datasets: [
        {
            label: 'Orders',
            data: props.chartData.data,
            backgroundColor: [
                'rgba(34, 197, 94, 0.8)',   // Green for Completed
                'rgba(251, 191, 36, 0.8)',   // Yellow for Pending
                'rgba(239, 68, 68, 0.8)',    // Red for Cancelled
            ],
            borderColor: [
                'rgb(34, 197, 94)',
                'rgb(251, 191, 36)',
                'rgb(239, 68, 68)',
            ],
            borderWidth: 2,
        }
    ]
}

const options = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: true,
            position: 'bottom',
            labels: {
                usePointStyle: true,
                padding: 15,
                font: {
                    size: 12
                }
            }
        },
        tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            titleColor: '#fff',
            bodyColor: '#fff',
            padding: 12,
            cornerRadius: 8,
            callbacks: {
                label: function(context) {
                    let label = context.label || '';
                    let value = context.parsed || 0;
                    let total = context.dataset.data.reduce((a, b) => a + b, 0);
                    let percentage = ((value / total) * 100).toFixed(1);
                    
                    return label + ': ' + value + ' (' + percentage + '%)';
                }
            }
        }
    }
}
</script>

<template>
    <div class="relative flex flex-col min-w-0 break-words w-full mb-6 shadow-lg rounded bg-white">
        <div class="rounded-t mb-0 px-4 py-3 bg-transparent">
            <div class="flex flex-wrap items-center">
                <div class="relative w-full max-w-full flex-grow flex-1">
                    <h6 class="uppercase text-blueGray-400 mb-1 text-xs font-semibold">
                        Distribution
                    </h6>
                    <h2 class="text-blueGray-700 text-xl font-semibold">
                        Orders by Status
                    </h2>
                </div>
            </div>
        </div>
        <div class="p-4 flex-auto">
            <div class="relative" style="height: 300px;">
                <Doughnut :data="data" :options="options" />
            </div>
        </div>
    </div>
</template>
