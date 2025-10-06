<template>
    <div
        class="relative flex flex-col min-w-0 break-words bg-white w-full mb-6 shadow-lg rounded"
    >
        <div class="rounded-t mb-0 px-4 py-3 bg-transparent">
            <div class="flex flex-wrap items-center">
                <div class="relative w-full max-w-full flex-grow flex-1">
                    <h6 class="uppercase text-blueGray-400 mb-1 text-xs font-semibold">
                        Overview
                    </h6>
                    <h2 class="text-blueGray-700 text-xl font-semibold">
                        Profit value
                    </h2>
                </div>
            </div>
        </div>
        <div class="p-4 flex-auto">
            <!-- Chart -->
            <div class="relative h-350-px">
                <canvas id="line-chart"></canvas>
            </div>
        </div>
    </div>
</template>
<script>
import Chart from "chart.js";

export default {
    props: {
        profit_line_chart: Object,
    },
    mounted: function () {
        this.$nextTick(function () {
            var config = {
                type: "line",
                data: {
                    labels: this.profit_line_chart.months,
                    datasets: [
                        {
                            label: new Date().getFullYear(),
                            backgroundColor: "#4c51bf",
                            borderColor: "#4c51bf",
                            data: this.profit_line_chart.current_year,
                            fill: false,
                        },
                        {
                            label: new Date().getFullYear() - 1,
                            fill: false,
                            backgroundColor: "#fff",
                            borderColor: "#fff",
                            data: this.profit_line_chart.last_year,
                        },
                    ],
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    title: {
                        display: false,
                        text: "Sales Charts",
                        fontColor: "white",
                    },
                    legend: {
                        labels: {
                            fontColor: "rgba(0,0,0,.4)",
                        },
                        align: "end",
                        position: "bottom",
                    },
                    tooltips: {
                        mode: "index",
                        intersect: false,
                    },
                    hover: {
                        mode: "nearest",
                        intersect: true,
                    },
                    scales: {
                        xAxes: [
                            {
                                display: false,
                                scaleLabel: {
                                    display: true,
                                    labelString: "Month",
                                },
                                gridLines: {
                                    borderDash: [2],
                                    borderDashOffset: [2],
                                    color: "rgba(33, 37, 41, 0.3)",
                                    zeroLineColor: "rgba(33, 37, 41, 0.3)",
                                    zeroLineBorderDash: [2],
                                    zeroLineBorderDashOffset: [2],
                                },
                            },
                        ],
                        yAxes: [
                            {
                                display: true,
                                scaleLabel: {
                                    display: false,
                                    labelString: "Value",
                                },
                                gridLines: {
                                    borderDash: [2],
                                    drawBorder: false,
                                    borderDashOffset: [2],
                                    color: "rgba(33, 37, 41, 0.2)",
                                    zeroLineColor: "rgba(33, 37, 41, 0.15)",
                                    zeroLineBorderDash: [2],
                                    zeroLineBorderDashOffset: [2],
                                },
                            },
                        ],
                    },
                },
            };
            var ctx = document.getElementById("line-chart").getContext("2d");
            window.myLine = new Chart(ctx, config);
        });
    },
};
</script>
