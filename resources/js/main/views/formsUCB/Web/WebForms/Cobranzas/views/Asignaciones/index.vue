<template>
    <a-row :gutter="16" class="mt-20">
        <!-- IZQUIERDA (campos) -->
        <a-col :xs="24" :sm="24" :md="16" :lg="16">
            <a-row :gutter="16">
                <!-- Columna 1 -->
                <a-col :xs="24" :sm="24" :md="8" :lg="8">
                    <!-- Operación -->
                    <a-form-item class="floating-form-item" name="identificador_cartera"
                        :help="rules.identificador_cartera ? $t('cobranzas.wallet') : null"
                        :validateStatus="rules.identificador_cartera ? 'error' : ''">
                        <div class="floating-input"
                            :class="{ 'has-value': filters.identificador_cartera != null && filters.identificador_cartera !== '' }">
                            <a-input v-model:value="filters.identificador_cartera" placeholder=" " />
                            <label class="floating-label">
                                {{ $t('cobranzas.wallet') }}
                            </label>
                        </div>
                    </a-form-item>

                    <!-- Documento Cliente -->
                    <a-form-item class="floating-form-item" name="identificador_cartera">
                        <div class="floating-input">
                            <a-input v-model:value="filters.identificador_cartera" placeholder=" " />
                            <label class="floating-label">{{ $t('cobranzas.document') }}</label>
                        </div>
                    </a-form-item>

                    <!-- Nombre Cliente -->
                    <a-form-item class="floating-form-item" name="nombre_cliente">
                        <div class="floating-input">
                            <a-input v-model:value="filters.nombre_cliente" placeholder=" " />
                            <label class="floating-label">{{ $t('cobranzas.name') }}</label>
                        </div>
                    </a-form-item>
                </a-col>

                <!-- Columna 2 -->
                <a-col :xs="24" :sm="24" :md="8" :lg="8">
                    <!-- Teléfono -->
                    <a-form-item class="floating-form-item" name="telefono">
                        <div class="floating-input">
                            <a-input v-model:value="filters.telefono" placeholder=" " />
                            <label class="floating-label">{{ $t('cobranzas.phone') }}</label>
                        </div>
                    </a-form-item>

                    <!-- Correo Electrónico -->
                    <a-form-item class="floating-form-item" name="email">
                        <div class="floating-input">
                            <a-input v-model:value="filters.email" placeholder=" " />
                            <label class="floating-label">{{ $t('cobranzas.email') }}</label>
                        </div>
                    </a-form-item>

                    <!-- Asesor -->
                    <a-form-item class="floating-form-item" name="agente">
                        <div class="floating-input">
                            <a-input v-model:value="filters.agente" placeholder=" " />
                            <label class="floating-label">{{ $t('cobranzas.adviser') }}</label>
                        </div>
                    </a-form-item>
                </a-col>

                <!-- Columna 3 (selects) -->
                <a-col :xs="24" :sm="24" :md="8" :lg="8">
                    <!-- Base -->
                    <a-form-item class="floating-form-item" name="nombre_base">
                        <div class="floating-input">
                            <a-select v-model:value="filters.nombre_base" style="width: 100%" placeholder=" "
                                :options="basesOptions" allowClear />
                            <label class="floating-label">{{ $t('cobranzas.bases') }}</label>
                        </div>
                    </a-form-item>

                    <!-- Checks -->
                    <div style="flex: 1;">
                        <div style="font-weight: 600; letter-spacing: 2px; text-align: center;">
                            {{ $t('cobranzas.assignments') }}
                        </div>

                        <a-space direction="vertical" size="small">
                            <a-checkbox v-model:checked="filters.sin_asignar">{{ $t('cobranzas.unassigned')
                            }}</a-checkbox>
                            <a-checkbox v-model:checked="filters.no_gestionado">{{ $t('cobranzas.not_managed')
                            }}</a-checkbox>
                            <a-checkbox v-model:checked="filters.no_gest_mes">{{ $t('cobranzas.not_managed_month')
                            }}</a-checkbox>
                            <a-checkbox v-model:checked="filters.no_completadas">{{ $t('cobranzas.not_completed')
                            }}</a-checkbox>
                        </a-space>
                    </div>

                </a-col>
            </a-row>
        </a-col>

        <!-- DERECHA (checks + acciones) -->
        <a-col :xs="24" :sm="24" :md="8" :lg="8">
            <div class="d-flex" style="gap:16px; align-items:flex-start;">
                <!-- Registros + botones -->
                <div style="flex: 1;">
                    <div class="d-flex" style="justify-content: space-between; margin-bottom: 10px;">
                        <span>{{ $t('cobranzas.records') }}</span>
                        <span>{{ totalRegistros || 0 }}</span>
                    </div>

                    <a-tooltip :title="$t('common.search')">
                        <a-button type="primary" style="width: 45%;">
                            <template #icon>
                                <SearchOutlined />
                            </template>
                        </a-button>
                    </a-tooltip>

                    <a-tooltip :title="$t('common.clear')">
                        <a-button danger style="margin-left: 20px; width: 45%;">
                            <template #icon>
                                <ClearOutlined />
                            </template>
                        </a-button>
                    </a-tooltip>

                    <!-- Agentes de la campaña -->
                    <a-form-item class="agents-item mt-10" name="agentes">
                        <div class="agents-label">{{ $t('cobranzas.adviser') }}</div>
                        <div class="agents-select-wrapper">
                            <a-select v-model:value="filters.agentes" mode="multiple" :options="agentsOptions"
                                :open="true" :showSearch="false" :dropdownMatchSelectWidth="true"
                                :getPopupContainer="trigger => trigger.parentNode" placeholder="" />
                        </div>
                    </a-form-item>
                    
                </div>


                <div style="width: 220px;">
                    <!-- Proyecto -->
                    <a-form-item class="floating-form-item mt-30" name="proyecto">
                        <div class="floating-input">
                            <a-select v-model:value="filters.proyecto" style="width: 100%" placeholder=" "
                                :options="projectsOptions" allowClear />
                            <label class="floating-label">{{ $t('cobranzas.projects') }}</label>
                        </div>
                    </a-form-item>
                    <!-- Asignar / Desasignar-->
                    <div style="flex: 1;">
                        <a-button type="link" style="width: 45%; margin-top: 20%;"> {{ $t('cobranzas.assign') }} </a-button>
                        <a-button type="link" style="margin-left: 20px; width: 45%;"> {{ $t('cobranzas.unassign') }} </a-button>
                    </div>
                </div>
            </div>
        </a-col>
    </a-row>
    <a-row>
        <a-col :xs="24" :sm="24" :md="24" :lg="24">
            <div class="table-responsive">
                <a-table size="small" :columns="columns" :data-source="data" :scroll="{ x: 1300, y: 1000 }">
                    <template #bodyCell="{ column }">

                        <template v-if="column.key === 'action'">
                            <a-tooltip :title="$t('common.download')">
                                <a-button type="primary" @click="" style="margin-left: 4px">
                                    <template #icon>
                                        <DownloadOutlined />
                                    </template>
                                </a-button>
                            </a-tooltip>
                            <a-tooltip :title="$t('common.delete')">
                                <a-button type="primary" danger @click="showSelectedDeleteConfirm(record.xid)"
                                    style="margin-left: 4px">
                                    <template #icon>
                                        <DeleteOutlined />
                                    </template>
                                </a-button>
                            </a-tooltip>
                        </template>

                    </template>
                </a-table>
            </div>
        </a-col>
    </a-row>
</template>
<script>
import { watch, onMounted, ref } from "vue";
import fields from "./fields.js";
import {
    SearchOutlined,
    ClearOutlined,
} from "@ant-design/icons-vue";
import Upload from "../../../../components/upload.vue";
import crud from "../../../../../../../../common/composable/crud";
import apiAdmin from "../../../../../../../../common/composable/apiAdmin";

export default {
    components: {
        //// NUEVO ////
        Upload,
        SearchOutlined,
        ClearOutlined,
        ///////////////
    },
    setup() {
        const crudVariables = crud();
        const { rules } = apiAdmin();
        const { columns, filters } = fields();

        const totalRegistros = ref(0);

        const setUrlData = () => {
            crudVariables.tableUrl.value = {
                url: url,
            };
            crudVariables.table.filterableColumns = filterableColumns;

            crudVariables.fetch({
                page: 1,
            });

            crudVariables.crudUrl.value = addEditUrl;
            crudVariables.langKey.value = "user";
            crudVariables.initData.value = { ...initData };
            crudVariables.filters.value = { ...initData };
            crudVariables.hashableColumns.value = [...hashableColumns];
        };

        // onMounted(() => {
        //     setUrlData();
        // });

        return {
            // En uso
            columns,
            ...crudVariables,
            rules,
            filters,
            totalRegistros,
            /////////////
        };
    },
};
</script>
<style>
.agents-label {
    font-weight: 600;
}

/* Oculta el "input/selector" superior, dejamos solo la lista */
.agents-select-wrapper .ant-select-selector {
    display: none !important;
}

/* Convierte el dropdown en un panel fijo (no flotante) */
.agents-select-wrapper .ant-select-dropdown {
    position: static !important;
    inset: auto !important;
    transform: none !important;
    box-shadow: none !important;
    border-radius: 8px;
    max-height: 160px;
    /* ajustá la altura como en tu imagen */
    overflow: auto;
}
</style>