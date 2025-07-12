<template>
  <DoughnutChart
    ref="chartRef"
    :chartData="chartData"
    :options="options"
  />
</template>

<script>
import { ref, watch } from "vue";
import { DoughnutChart } from "vue-chart-3";
import { Chart, registerables } from "chart.js";

Chart.register(...registerables);

export default {
  name: "TopProductsDonut",
  props: ["data"],
  components: { DoughnutChart },
  setup(props) {
    const chartRef = ref();

    const options = ref({
      responsive: true,
      plugins: {
        legend: { position: "bottom" },
        title: { display: false }
      }
    });

    const chartData = ref({
      labels: [],
      datasets: [{ data: [], backgroundColor: [] }]
    });

    watch(
      () => props.data.topProducts,
      (tp) => {
        chartData.value = {
          labels: tp?.labels || [],
          datasets: [
            {
              data: tp?.data || [],
              backgroundColor: tp?.colors || []
            }
          ]
        };
      },
      { immediate: true }
    );

    return { chartRef, chartData, options };
  }
};
</script>
