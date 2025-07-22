<template>
  <div class="chart-layout">
      <DoughnutChart
      ref="chartRef"
      :chartData="testData"
      :options="options"
      style="flex: 1;"
      />
      <div ref="legendContainer" class="custom-scroll-legend">
        <ul></ul>
      </div>
  </div>
</template>

<script>
import { ref, watch, onMounted } from "vue";
import { DoughnutChart } from "vue-chart-3";
import { Chart, registerables } from "chart.js";

Chart.register(...registerables);

// Plugin para exportar la leyenda como HTML con clic funcional
const htmlLegendPlugin = {
  id: "htmlLegend",
  afterUpdate(chart, args, options) {
    const ul = options.container?.querySelector("ul");
    if (!ul) return;

    while (ul.firstChild) {
      ul.firstChild.remove();
    }

    const items = chart.options.plugins.legend.labels.generateLabels(chart);

    items.forEach((item) => {
      const li = document.createElement("li");
      li.style.display = "flex";
      li.style.alignItems = "center";
      li.style.cursor = "pointer";
      li.style.marginBottom = "8px";

      const box = document.createElement("span");
      box.style.width = "14px";
      box.style.height = "14px";
      box.style.marginRight = "8px";
      box.style.background = item.fillStyle;
      box.style.borderRadius = "2px";

      const text = document.createElement("span");
      text.style.textDecoration = item.hidden ? "line-through" : "";
      text.style.opacity = item.hidden ? "0.5" : "1";
      text.innerText = item.text;

      li.onclick = () => {
        chart.toggleDataVisibility(item.index);
        chart.update();
      };

      li.appendChild(box);
      li.appendChild(text);
      ul.appendChild(li);
    });
  },
};

export default {
  props: ["data"],
  components: {
    DoughnutChart,
  },
  setup(props) {
    const chartRef = ref();
    const legendContainer = ref();

    onMounted(() => {
      Chart.register(htmlLegendPlugin);
    });

    const options = ref({
      responsive: true,
      plugins: {
        legend: {
          display: false,
        },
        htmlLegend: {
          container: legendContainer,
        },
        title: {
          display: false,
        },
      },
    });

    const testData = ref({
      labels: [],
      datasets: [],
    });

    watch(
      () => props.data,
      (newVal) => {
        testData.value = {
          labels: newVal?.actionedCampaigns?.labels || [],
          datasets: [
            {
              data: newVal?.actionedCampaigns?.values || [],
              backgroundColor: newVal?.actionedCampaigns?.colors || [],
            },
          ],
        };
      },
      { immediate: true }
    );

    return {
      chartRef,
      testData,
      options,
      legendContainer,
    };
  },
};
</script>

<style scoped>
.chart-layout {
  display: flex;
  flex-direction: row-reverse; /* leyenda a la izquierda */
  align-items: flex-start;
  flex-wrap: nowrap;
  width: 100%;
  margin: 0;
  padding: 0;
}

.custom-scroll-legend {
  max-height: 60%;
  overflow-y: auto;
  overflow-x: hidden;
  padding: 0 8px 0 0; /* solo padding derecho para separarla del gráfico */
  border-right: 1px solid #ddd;
  margin: 0;
  flex-shrink: 0;
  width: auto;
}

.custom-scroll-legend ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.custom-scroll-legend li {
  display: flex;
  align-items: center;
  cursor: pointer;
  margin-bottom: 8px;
  padding-left: 0;
}

.custom-scroll-legend li:hover {
  background-color: #f5f5f5;
  border-radius: 4px;
}

.color-box {
  width: 14px;
  height: 14px;
  margin-right: 8px;
  border-radius: 2px;
}

</style>
