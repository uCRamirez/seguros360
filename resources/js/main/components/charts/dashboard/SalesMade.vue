<template>
  <BarChart ref="chartRef" :chartData="chartData" :options="options" />
</template>

<script>
import { ref, watch, nextTick } from 'vue';
import { BarChart } from 'vue-chart-3';
import { Chart, registerables } from 'chart.js';
import { useI18n } from 'vue-i18n';

Chart.register(...registerables);

export default {
  props: ['data'],
  components: { BarChart },
  setup(props) {
    const { t } = useI18n();
    const chartRef = ref();

    const chartData = ref({
      labels: [],
      datasets: [
        {
          label: t('dashboard.total_sales'),
          data: [],
          backgroundColor: [],
        },
      ],
    });

    const options = ref({
      indexAxis: 'y',
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
          title: {
            display: true,
            text: t('dashboard.total_sales'),
          },
          beginAtZero: true,
          ticks: {
            autoSkip: true,
            maxTicksLimit: 10,
          },
        },
        y: {
          beginAtZero: true,
        },
      },
    });

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
                label: t('dashboard.total_sales'),
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

        // Solo incluir fechas donde haya al menos un registro y omitir ceros
        newVal.dates.forEach((fecha, idxFecha) => {
          const totalEnFecha = newVal.series.reduce(
            (sum, serie) => sum + (serie.counts[idxFecha] || 0),
            0
          );
          if (totalEnFecha > 0) {
            newVal.series.forEach((serie) => {
              const valor = serie.counts[idxFecha] || 0;
              if (valor > 0) {
                labelsPlano.push(`${fecha} – ${serie.user}`);
                dataPlano.push(valor);
                colorPlano.push(serie.color || '#333');
              }
            });
          }
        });

        chartData.value = {
          labels: labelsPlano,
          datasets: [
            {
              label: t('dashboard.total_sales'),
              data: dataPlano,
              backgroundColor: colorPlano,
            },
          ],
        };

        // Ajuste dinámico del máximo en el eje X
        const maxVal = Math.max(...dataPlano, 0);
        options.value.scales.x.suggestedMax = maxVal * 1.2;

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
