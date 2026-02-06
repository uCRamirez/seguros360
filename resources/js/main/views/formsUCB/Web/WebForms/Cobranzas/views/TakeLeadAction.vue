<template>

        <a-row>
            <a-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
                <a-card class="callmanager-middle-sidebar" style="overflow: auto; scrollbar-width: none;">
                    <a-tabs v-model:activeKey="activeKey">

                        <!-- TAB INFORMACION SOBRE LAS BASES -->
                        <a-tab-pane key="lead_details">
                            <template #tab>
                                <span>
                                    <DatabaseOutlined />
                                    {{ $t("cobranzas.bases") }}
                                </span>
                            </template>

                            <Bases />

                        </a-tab-pane>
                        <!-- FIN Tab con informacion del lead -->

                        <!-- TAB ASIGNACIONES -->
                        <a-tab-pane key="asignaciones">
                            <template #tab>
                                <span>
                                    <DatabaseOutlined />
                                    {{ $t("cobranzas.assignments") }}
                                </span>
                            </template>

                            <Asignaciones />

                        </a-tab-pane>
                        <!-- FIN Tab ASIGNACIONES -->

                    </a-tabs>
                </a-card>
            </a-col>
        </a-row>
</template>

<script>
import { watch, computed } from "vue";
import {
    DatabaseOutlined,
    ExclamationCircleOutlined,
    DeleteOutlined,
} from "@ant-design/icons-vue";
// import { Modal, notification, message } from "ant-design-vue";
import { useRoute, useRouter } from "vue-router";
import { useI18n } from "vue-i18n";
import { useStore } from "vuex";
import apiAdmin from "../../../../../../../common/composable/apiAdmin.js";
import common from "../../../../../../../common/composable/common.js";
// import crud from "../../../../../../../common/composable/crud";
// import SearchLead from "./SearchLead.vue";
import fields from "./fields.js";
// import functions from "./functions.js";

import Bases from "./Bases/index.vue";
import Asignaciones from "./Asignaciones/index.vue";
import esES from 'ant-design-vue/es/locale/es_ES';
import enUS from 'ant-design-vue/es/locale/en_US';
import dayjs from 'dayjs';

export default {
    components: {
        //// NUEVO ////
        DatabaseOutlined,
        ExclamationCircleOutlined,
        DeleteOutlined,
        Bases,
        Asignaciones,
        ///////////////
    },
    setup() {
        const { columns } = fields();
        const { formatDateTime, rightSidebarValue, themeMode, permsArray } = common();
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const route = useRoute();
        const router = useRouter();
        const store = useStore();
        const { t, locale } = useI18n();
        
        const antdLocale = computed(() =>
            locale.value === 'en' ? enUS : esES
        );
        watch(locale, (newLang) => {
            dayjs.locale(newLang);
        });

        return {
            // En uso
            antdLocale,
            columns,
            /////////////
        };
    },
};
</script>

<style scoped>
.callmanager-left-sidebar {
    height: calc(110vh - 80px);
}

.callmanager-left-sidebar .ps {
    height: calc(110vh - 270px);
}

.callmanager-middle-sidebar {
    height: calc(100vh - 70px);
}

.callmanager-middle-sidebar .ps {
    height: calc(100vh - 235px);
}

.callmanager-right-sidebar {
    height: calc(100vh);
}

:deep(.callmanager-right-sidebar) {
    overflow: hidden !important;
}

:deep(.callmanager-right-sidebar .ant-card-body) {
    overflow: hidden !important;
}

:deep(.callmanager-right-sidebar .ps__rail-y),
:deep(.callmanager-right-sidebar .ps__rail-x) {
    display: none !important;
}

/* Pegar en tu <style scoped> o global */
.lead-actions {
    display: flex;
    gap: 10px;
}

.lead-actions__btn {
    min-width: 112px;
}

/* LABEL FLOTANTE */
/* ------------------------------
   Labels bold
------------------------------ */
.label-bold .ant-form-item-label>label {
    font-weight: bold;
}

/* ------------------------------
   Side panel layout
------------------------------ */
.side-panel {
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

@media (max-width: 895px) {
    .side-panel>.ant-col {
        flex-wrap: wrap;
        flex: 0 0 100% !important;
        max-width: 100% !important;
    }
}

/* ------------------------------
   Floating input base
------------------------------ */
.floating-input {
    position: relative;
    /* importante para el label absoluto */
}

/* INPUT normal (Ant Input renderiza un <input>) */
.floating-input input {
    padding: 20px 12px 6px 12px;
    border-top: none !important;
}

/* TEXTAREA (Ant Textarea renderiza <textarea>) */
.floating-input textarea {
    padding: 20px 12px 6px 12px;
    border-top: none !important;
}

/* Label dentro del input */
.floating-input label {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    pointer-events: none;
    transition: all 0.2s ease;
    padding: 0 4px;
    font-weight: bold;
}

/* Cuando tiene foco o valor (input/textarea) */
.floating-input:focus-within label,
.floating-input input:not(:placeholder-shown)+label,
.floating-input textarea:not(:placeholder-shown)+label {
    top: -6px;
}

/* ------------------------------
   SELECT (AntD) - clave para que funcione
   Esto es lo que "faltaba" en el otro.
   Usamos :deep() para que funcione aunque el style sea scoped.
------------------------------ */

/* Quitar borde superior del selector visible del Select */
.floating-input :deep(.ant-select-selector) {
    border-top: none !important;
    padding-top: 12px;
    /* ayuda a que el label no choque */
}

/* Si querés mantener el label flotante con selects */
.floating-input:focus-within .floating-label,
.floating-select.has-value .floating-label {
    top: -6px;
}

/* ------------------------------
   INPUT-NUMBER (AntD) - también tiene wrapper propio
------------------------------ */
.floating-input :deep(.ant-input-number) {
    border-top: none !important;
}

/* El input interno del input-number */
.floating-input :deep(.ant-input-number-input) {
    padding: 20px 12px 6px 12px !important;
}

/* ------------------------------
   (Opcional) Mantener tu comportamiento previo de phone-select
   Por si en algún lugar dependés de esa clase
------------------------------ */
.phone-select :deep(.ant-select-selector) {
    border-top: none !important;
}

/* Control base: mismo alto/estética */
.floating-input :deep(.ant-input),
.floating-input :deep(.ant-select-selector),
.floating-input :deep(.ant-picker),
.floating-input :deep(.ant-input-number) {
    height: 44px !important;
    border-top: none !important;
    display: flex;
    align-items: center;
}

/* Input normal */
.floating-input :deep(.ant-input) {
    padding: 20px 12px 6px 12px !important;
}

/* Select */
.floating-input :deep(.ant-select-selector) {
    padding: 0 12px !important;
}

/* Texto dentro del select */
.floating-input :deep(.ant-select-selection-search-input),
.floating-input :deep(.ant-select-selection-item),
.floating-input :deep(.ant-select-selection-placeholder) {
    line-height: 44px !important;
}

/* DatePicker */
.floating-input :deep(.ant-picker-input > input) {
    padding: 20px 12px 6px 12px !important;
}

/* InputNumber */
.floating-input :deep(.ant-input-number-input) {
    height: 44px !important;
    padding: 20px 12px 6px 12px !important;
}

/* Label flotante: cuando hay foco o valor */
.floating-input:focus-within .floating-label,
.floating-input.has-value .floating-label {
    top: -6px;
}

/* Ajuste de posición inicial del label */
.floating-input .floating-label {
    top: 50%;
    transform: translateY(-50%);
}
</style>
