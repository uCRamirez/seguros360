<template>
  <div class="indicadores-layout">
    <!-- KPI: Total Rellamadas -->
    <div class="kpi-box">
      <h1 class="kpi-value">{{ totalRellamadas }}</h1>
      <p class="kpi-label">{{ $t('common.recall') }}</p>
    </div>

    <!-- Gauge: Promedio Nota -->
    <div class="gauge-box">
      <DoughnutChart :chartData="chartData" :options="options" />
      <div class="gauge-center">
        <div :class="['gauge-value', gaugeColorClass]">
          {{ promedioNota.toFixed(1) }}
        </div>
        <div class="gauge-label">{{ $t('common.average_grade') }}</div>
      </div>
    </div>
  </div>
</template>

<script>
import { DoughnutChart } from "vue-chart-3";
import { Chart, ArcElement, Tooltip } from "chart.js";
import { ref, watch, computed } from "vue";

Chart.register(ArcElement, Tooltip);

export default {
  name: "IndicadoresCalidad",
  components: { DoughnutChart },
  props: {
    totalRellamadas: {
      type: Number,
      required: true,
    },
    promedioNota: {
      type: Number,
      required: true,
    },
    maxNota: {
      type: Number,
      default: 100,
    },
  },
  setup(props) {
    const chartData = ref({
      labels: ["Promedio", "Restante"],
      datasets: [
        {
          data: [0, props.maxNota],
          backgroundColor: ["#4caf50", "#e0e0e0"],
          borderWidth: 0,
          cutout: "80%",
          circumference: 180,
          rotation: -90,
        },
      ],
    });

    const options = {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: {
          callbacks: {
            label: () =>
              `${props.promedioNota.toFixed(1)} / ${props.maxNota}`,
          },
        },
      },
    };

    const gaugeColor = computed(() => {
      const nota = props.promedioNota;
      if (nota >= 75) return "#4caf50";
      if (nota >= 50) return "#ff9800";
      return "#f44336";
    });

    const gaugeColorClass = computed(() => {
      const nota = props.promedioNota;
      if (nota >= 75) return "green";
      if (nota >= 50) return "orange";
      return "red";
    });

    watch(
      () => [props.promedioNota, props.maxNota],
      () => {
        const filled = Math.min(props.promedioNota, props.maxNota);
        const empty = props.maxNota - filled;

        chartData.value = {
          labels: ["Promedio", "Restante"],
          datasets: [
            {
              data: [filled, empty],
              backgroundColor: [gaugeColor.value, "#e0e0e0"],
              borderWidth: 0,
              cutout: "80%",
              circumference: 180,
              rotation: -90,
            },
          ],
        };
      },
      { immediate: true }
    );

    return {
      chartData,
      options,
      gaugeColorClass,
    };
  },
};
</script>


<style scoped>
.indicadores-layout {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-around;
  gap: 2rem;
  align-items: center;
  width: 100%;
}

.kpi-box {
  padding: 2rem;
  border-radius: 12px;
  text-align: center;
  flex: 0 0 200px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.kpi-value {
  font-size: 3.2rem;
  font-weight: bold;
  color: #222;
  margin: 0;
}

.kpi-label {
  font-size: 1.1rem;
  color: #666;
  margin-top: 0.5rem;
}

.gauge-box {
  position: relative;
  width: 220px;
  height: 140px;
  margin-top: 10px;
}

.gauge-center {
  position: absolute;
  top: 0%;
  left: 50%;
  transform: translate(-50%, -40%);
  text-align: center;
  width: 100%;
}

.gauge-value {
  font-size: 1.7rem;
  font-weight: bold;
  margin-bottom: 4px;
}

.gauge-label {
  font-size: 0.95rem;
  color: #666;
}

/* Colores dinámicos */
.gauge-value.green {
  color: #4caf50;
}
.gauge-value.orange {
  color: #ff9800;
}
.gauge-value.red {
  color: #f44336;
}
</style>
