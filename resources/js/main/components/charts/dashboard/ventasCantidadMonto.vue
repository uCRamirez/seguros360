<template>
  <BarChart :chartData="testData" :options="salesByTeleopOptions" />
</template>

<script>
import { ref, watch } from "vue";
import { BarChart } from "vue-chart-3";
import { Chart, registerables } from "chart.js";
import { useI18n } from "vue-i18n";

Chart.register(...registerables);

export default {
  name: "SalesByTeleopChart",
  props: ["data"],
  components: { BarChart },
  setup(props) {
    const { t } = useI18n();

    const testData = ref({
      labels: [],

      datasets: [
        {
          label: t("dashboard.sales_quantity"),
          data: [],
          backgroundColor: [],

          yAxisID: "yQuantity",
        },
        {
          label: t("dashboard.sales_amount"),
          data: [],
          backgroundColor: [],
          yAxisID: "yAmount",
        },
      ],
    });

    const salesByTeleopOptions = ref({
      responsive: true,
      maintainAspectRatio: false,

      scales: {
        x: {
          title: {
            display: true,
            text: t("dashboard.user"),
          },
        },

        yQuantity: {
          type: "linear",
          position: "left",
          beginAtZero: true,
          title: {
            display: true,
            text: t("dashboard.quantity"), 
          },
          grid: {
            drawOnChartArea: true, 
          },
        },

        yAmount: {
          type: "linear",
          position: "right",
          beginAtZero: true,
          title: {
            display: true,
            text: t("dashboard.amount"),
          },
          grid: {
            drawOnChartArea: false,
          },
        },
      },

    });

    function randomHexColor() {
      return (
        "#" +
        Math.floor(Math.random() * 0xffffff)
          .toString(16)
          .padStart(6, "0")
      );
    }

    function transformVentasData(raw) {
      const acum = {};
      Object.values(raw.ventas).forEach((recordsForDate) => {
        recordsForDate.forEach((item) => {
          const name = item.agente;
          const logs = parseInt(item.total_logs, 10);
          const monto = parseFloat(item.total_montos);
          if (!acum[name]) {
            acum[name] = { quantity: 0, amount: 0 };
          }
          acum[name].quantity += logs;
          acum[name].amount += monto;
        });
      });
      return Object.entries(acum).map(([teleoperatorName, vals]) => ({
        teleoperatorName,
        quantity: vals.quantity,
        amount: vals.amount,
      }));
    }

    watch(
    () => props.data,
    (newData) => {
      if (!newData || !newData.ventas) {
        testData.value = {
          labels: [],
          datasets: [
            {
              label: t("dashboard.sales_quantity"),
              data: [],
              backgroundColor: [],
              yAxisID: "yQuantity",
            },
            {
              label: t("dashboard.sales_amount"),
              data: [],
              backgroundColor: [],
              yAxisID: "yAmount",
            },
          ],
        };
        return;
      }

      const processed = transformVentasData(newData);
      const labels = processed.map((p) => p.teleoperatorName);
      const qtys = processed.map((p) => p.quantity);
      const amts = processed.map((p) => p.amount);

      const colorsQty = labels.map(() => randomHexColor());
      const colorsAmt = labels.map(() => randomHexColor());

      testData.value.labels = labels;
      testData.value.datasets[0].data = qtys;
      testData.value.datasets[0].backgroundColor = colorsQty;

      testData.value.datasets[1].data = amts;
      testData.value.datasets[1].backgroundColor = colorsAmt;

      const maxQty = Math.max(...qtys);
      const maxAmt = Math.max(...amts);

      salesByTeleopOptions.value.scales.yQuantity.suggestedMax = maxQty + Math.ceil(maxQty * 0.1);
      salesByTeleopOptions.value.scales.yAmount.suggestedMax = maxAmt + Math.ceil(maxAmt * 0.1);
    },
    { immediate: true, deep: true }
  );


    return {
      testData,
      salesByTeleopOptions,
    };
  },
};
</script>
