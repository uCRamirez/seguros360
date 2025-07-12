<template>
  <BarChart 
    ref="chartRef" 
    :chartData="chartData" 
    :options="options" 
  />
</template>

<script>
import { ref, watch } from "vue";
import { BarChart } from "vue-chart-3";
import { Chart, registerables } from "chart.js";
import { useI18n } from "vue-i18n";

Chart.register(...registerables);

export default {
  props: ["data"],
  components: {
    BarChart,
  },
  setup(props) {
    const { t } = useI18n();
    const chartRef = ref();

    const options = ref({
      responsive: true,
      plugins: {
        legend: {
          display: false,
        },
        title: {
          display: false,
        },
      },
      scales: {
        x: {
          ticks: {
            maxRotation: 45,
            minRotation: 45,
            autoSkip: false, 
            font: {
              size: 10,
            },
          },
        },
        y: {
          beginAtZero: true,
          title: {
            display: true,
            text: t("dashboard.total_sales"),
          },
        },
      },
    });

    const chartData = ref({
      labels: [],    
      datasets: [
        {
          label: t("dashboard.total_sales"), 
          data: [],                          
          backgroundColor: [],             
        },
      ],
    });

    watch(
      () => props.data.salesMade,
      (newVal) => {
        if (
          !newVal ||
          !Array.isArray(newVal.dates) ||
          !Array.isArray(newVal.series)
        ) {
          chartData.value = {
            labels: [],
            datasets: [
              {
                label: t("dashboard.total_sales"),
                data: [],
                backgroundColor: [],
              },
            ],
          };
          return;
        }

        const dates = newVal.dates;      
        const series = newVal.series;   

        const labelsPlano = [];
        const dataPlano = [];
        const colorPlano = [];

        dates.forEach((fecha) => {
          series.forEach((serie) => {
            labelsPlano.push(`${fecha} – ${serie.user}`);

            const idxFecha = dates.indexOf(fecha);
            const valor = serie.counts[idxFecha] || 0;
            dataPlano.push(valor);

            colorPlano.push(serie.color || "#333333");
          });
        });

        chartData.value = {
          labels: labelsPlano,
          datasets: [
            {
              label: t("dashboard.total_sales"),
              data: dataPlano,
              backgroundColor: colorPlano,
            },
          ],
        };
      },
      { immediate: true }
    );

    return {
      chartRef,
      chartData,
      options,
    };
  },
};
</script>
