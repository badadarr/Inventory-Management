<script setup>
import { Line } from 'vue-chartjs'
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend,
    Filler
} from 'chart.js'

ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend,
    Filler
)

const props = defineProps({
    chartData: {
        type: Object,
        required: true
    }
})

const data = {
    labels: props.chartData.months,
    datasets: [
        {
            label: 'Revenue',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            borderColor: 'rgb(59, 130, 246)',
            data: props.chartData.revenue,
            fill: true,
            tension: 0.4,
            pointRadius: 4,
            pointHoverRadius: 6,
        },
        {
            label: 'Expenses',
            backgroundColor: 'rgba(239, 68, 68, 0.1)',
            borderColor: 'rgb(239, 68, 68)',
            data: props.chartData.expenses,
            fill: true,
            tension: 0.4,
            pointRadius: 4,
            pointHoverRadius: 6,
        }
    ]
}

const options = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: true,
            position: 'top',
            labels: {
                usePointStyle: true,
                padding: 15,
                font: {
                    size: 12,
                    weight: 'bold'
                }
            }
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
                    let label = context.dataset.label || '';
                    if (label) {
                        label += ': ';
                    }
                    if (context.parsed.y !== null) {
                        label += 'Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                    }
                    return label;
                }
            }
        }
    },
    scales: {
        y: {
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
                drawBorder: false
            }
        },
        x: {
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
                        Performance
                    </h6>
                    <h2 class="text-blueGray-700 text-xl font-semibold">
                        Revenue vs Expenses Trend
                    </h2>
                </div>
            </div>
        </div>
        <div class="p-4 flex-auto">
            <div class="relative" style="height: 350px;">
                <Line :data="data" :options="options" />
            </div>
        </div>
    </div>
</template>
