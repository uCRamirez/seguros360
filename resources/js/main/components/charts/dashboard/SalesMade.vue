<template>
  <BarChart ref="chartRef" :chartData="chartData" :options="options" />
</template>

<script>
import { ref, watch, nextTick } from "vue";
import { BarChart } from "vue-chart-3";
import { Chart, registerables } from "chart.js";
import { useI18n } from "vue-i18n";

Chart.register(...registerables);

export default {
  props: ["data"],
  components: { BarChart },
  setup(props) {
    const { t } = useI18n();
    const chartRef = ref();

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

    const options = ref({
      responsive: true,
      plugins: {
        legend: { display: false },
        title: { display: false },
        tooltip: {
          callbacks: {
            title: (ctx) => chartData.value.labels[ctx[0].dataIndex],
          },
        },
      },
      scales: {
        x: {
          ticks: { display: false },
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

    // Plugin para mostrar texto encima de las barras
    const drawTextPlugin = {
      id: "drawTextPlugin",
      afterDatasetsDraw(chart) {
        const ctx = chart.ctx;
        const datasetMeta = chart.getDatasetMeta(0);
        const labels = chartData.value.labels;

        ctx.save();
        ctx.font = "10px sans-serif";
        ctx.fillStyle = "#333";
        ctx.textAlign = "center";
        ctx.textBaseline = "bottom";

        datasetMeta.data.forEach((bar, index) => {
          const value = chart.data.datasets[0].data[index];
          if (value <= 0) return;

          const label = labels[index];
          const x = bar.x;
          const y = bar.y;

          const maxWidth = bar.width * 1.5;
          const split = label.length > 16 ? label.split(" – ") : [label];

          split.forEach((line, i) => {
            ctx.fillText(line, x, y - 5 - i * 12, maxWidth);
          });
        });

        ctx.restore();
      },
    };

    Chart.register(drawTextPlugin);

    watch(
      () => props.data?.salesMade,
      async (newVal) => {
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

        const labelsPlano = [];
        const dataPlano = [];
        const colorPlano = [];

        newVal.dates.forEach((fecha) => {
          newVal.series.forEach((serie) => {
            labelsPlano.push(`${fecha} – ${serie.user}`);
            const idxFecha = newVal.dates.indexOf(fecha);
            const valor = serie.counts[idxFecha] || 0;
            dataPlano.push(valor);
            colorPlano.push(serie.color || "#333");
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

        // 🔁 ACTUALIZA el suggestedMax dinámicamente (20% más)
        const maxVal = Math.max(...dataPlano);
        options.value.scales.y.suggestedMax = maxVal + maxVal * 0.2;

        await nextTick();
        chartRef.value?.chart?.update();
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

