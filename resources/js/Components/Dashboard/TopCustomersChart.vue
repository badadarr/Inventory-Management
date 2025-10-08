<script setup>
import { Bar } from 'vue-chartjs'
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    BarElement,
    Title,
    Tooltip,
    Legend
} from 'chart.js'

ChartJS.register(
    CategoryScale,
    LinearScale,
    BarElement,
    Title,
    Tooltip,
    Legend
)

const props = defineProps({
    chartData: {
        type: Object,
        required: true
    }
})

const data = {
    labels: props.chartData.labels.length > 0 ? props.chartData.labels : ['No Data'],
    datasets: [
        {
            label: 'Total Spent',
            data: props.chartData.data.length > 0 ? props.chartData.data : [0],
            backgroundColor: [
                'rgba(59, 130, 246, 0.8)',
                'rgba(16, 185, 129, 0.8)',
                'rgba(245, 158, 11, 0.8)',
                'rgba(139, 92, 246, 0.8)',
                'rgba(236, 72, 153, 0.8)',
            ],
            borderColor: [
                'rgb(59, 130, 246)',
                'rgb(16, 185, 129)',
                'rgb(245, 158, 11)',
                'rgb(139, 92, 246)',
                'rgb(236, 72, 153)',
            ],
            borderWidth: 2,
        }
    ]
}

const options = {
    indexAxis: 'y',
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: false
        },
        title: {
            display: false
        },
        tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            titleColor: '#fff',
            bodyColor: '#fff',
            padding: 12,
            cornerRadius: 8,
            callbacks: {
                label: function(context) {
                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.x);
                }
            }
        }
    },
    scales: {
        x: {
            beginAtZero: true,
            ticks: {
                callback: function(value) {
                    return 'Rp ' + (value / 1000000).toFixed(1) + 'M'
                },
                font: {
                    size: 11
                }
            },
            grid: {
                color: 'rgba(0, 0, 0, 0.05)',
            }
        },
        y: {
            grid: {
                display: false
            },
            ticks: {
                font: {
                    size: 11
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
                        Insights
                    </h6>
                    <h2 class="text-blueGray-700 text-xl font-semibold">
                        Top 5 Customers
                    </h2>
                </div>
            </div>
        </div>
        <div class="p-4 flex-auto">
            <div class="relative" style="height: 300px;">
                <Bar :data="data" :options="options" />
            </div>
        </div>
    </div>
</template>
