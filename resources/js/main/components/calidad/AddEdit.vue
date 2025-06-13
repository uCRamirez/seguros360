<template>
    <a-drawer :title="$t('menu.quality')" :width="drawerWidth" :open="visible" :body-style="{ paddingBottom: '80px' }"
        :maskClosable="false" @close="onClose" :destroyOnClose="true">
        <!-- Informaion general del lead -->
        <a-descriptions class="label-bold" :layout="descLayout" :column="{ xs: 1, sm: 1, md: 2, lg: 3 }"
            :title="$t('common.basic_details')">
            <a-descriptions-item class="label-bold" strong :label="$t('uphone_calls.client_id')">
                {{
                    data.lead && data.lead.id && data.lead.id
                        ? data.lead.id
                        : "-"
                }}
            </a-descriptions-item>
            <a-descriptions-item class="label-bold" :label="$t('lead.document')">
                {{ data.lead && data.lead.cedula ? data.lead.cedula : "-" }}
            </a-descriptions-item>
            <a-descriptions-item class="label-bold" :label="$t('lead.name')">
                {{
                    data.lead && data.lead.nombre && data.lead.nombre
                        ? data.lead.nombre
                        : "-"
                }}
                {{
                    data.lead && data.lead.apellido1 && data.lead.apellido1
                        ? data.lead.apellido1
                        : "-"
                }}
                {{
                    data.lead && data.lead.apellido2 && data.lead.apellido2
                        ? data.lead.apellido2
                        : "-"
                }}
            </a-descriptions-item>
        </a-descriptions>

        <a-descriptions :layout="descLayout" :column="{ xs: 1, sm: 1, md: 2, lg: 3 }">

            <a-descriptions-item class="label-bold" :label="$t('lead.date_birth')">
                {{
                    data.lead && data.lead.fechaNacimiento && data.lead.fechaNacimiento
                        ? formatDateTime(data.lead.fechaNacimiento)
                        : "-"
                }}
            </a-descriptions-item>
            <a-descriptions-item class="label-bold" :label="$t('lead.age')">
                {{
                    data.lead && data.lead.edad && data.lead.edad
                        ? data.lead.edad
                        : "-"
                }}
            </a-descriptions-item>
            <a-descriptions-item class="label-bold" :label="$t('lead.base_name')">
                {{
                    data.lead && data.lead.nombreBase && data.lead.nombreBase
                        ? data.lead.nombreBase
                        : "-"
                }}
            </a-descriptions-item>
        </a-descriptions>

        <a-descriptions :layout="descLayout" :column="{ xs: 1, sm: 1, md: 2, lg: 3 }">
            <a-descriptions-item class="label-bold" :label="$t('lead.nationality')">
                {{
                    data.lead && data.lead.nacionalidad && data.lead.nacionalidad
                        ? data.lead.nacionalidad
                        : "-"
                }}
            </a-descriptions-item>
            <a-descriptions-item class="label-bold" :label="$t('lead.email')">
                {{
                    data.lead && data.lead.email && data.lead.email
                        ? data.lead.email
                        : "-"
                }}
            </a-descriptions-item>
        </a-descriptions>

        <a-tabs v-model:activeKey="activeTabKey" class="mt-20">
            <!-- Informacion de la venta general solicitada -->
            <a-tab-pane key="info_venta">
                <template #tab>
                    <span>
                        <UnorderedListOutlined />
                        {{ $t("common.information") }} {{ $t('lead_notes.sale') }}
                    </span>
                </template>
                <a-descriptions :layout="descLayout" :column="{ xs: 1, sm: 1, md: 2, lg: 3 }">

                    <a-descriptions-item class="label-bold" :label="$t('lead.id') + ' ' + $t('lead_notes.sale')">
                        {{ data.is_sale && data.is_sale.idVenta ? data.is_sale.idVenta : "-" }}
                    </a-descriptions-item>

                    <a-descriptions-item class="label-bold" :label="$t('lead_notes.date_time')">
                        {{ data.date_time ? formatDateTime(data.date_time) : "-" }}
                    </a-descriptions-item>

                    <a-descriptions-item class="label-bold" :label="$t('lead_log.user_id')">
                        {{ data.is_sale.user && data.is_sale.user.name ? data.is_sale.user.name : "-" }}
                    </a-descriptions-item>
                </a-descriptions>
                <a-descriptions :layout="descLayout" :column="{ xs: 1, sm: 1, md: 2, lg: 3 }">
                    <a-descriptions-item class="label-bold" :label="$t('lead.internal_code')">
                        {{ data.is_sale.product && data.is_sale.product.internal_code ?
                            data.is_sale.product.internal_code : "-" }}
                    </a-descriptions-item>

                    <a-descriptions-item class="label-bold" :label="$t('lead.product')">
                        {{ data.is_sale.product && data.is_sale.product.name ? data.is_sale.product.name : "-" }}
                    </a-descriptions-item>

                    <a-descriptions-item class="label-bold" :label="$t('lead.coverage')">
                        {{ data.is_sale.product && data.is_sale.product.coverage ? data.is_sale.product.coverage : "-"
                        }}
                    </a-descriptions-item>
                </a-descriptions>
                <a-descriptions :layout="descLayout" :column="{ xs: 1, sm: 1, md: 2, lg: 3 }">
                    <a-descriptions-item class="label-bold" :label="$t('lead.price')">
                        {{ data.is_sale.product && data.is_sale.product.price ?
                            formatAmountCurrency(data.is_sale.product.price) : "-"
                        }}
                    </a-descriptions-item>

                    <a-descriptions-item class="label-bold" :label="$t('lead.total_amount')">
                        {{ data.is_sale && data.is_sale.montoTotal ? formatAmountCurrency(data.is_sale.montoTotal) : "-"
                        }}
                    </a-descriptions-item>

                    <a-descriptions-item class="label-bold" :label="$t('lead.phone')">
                        {{ data.is_sale && data.is_sale.telVenta ? data.is_sale.telVenta : "-" }}
                    </a-descriptions-item>
                </a-descriptions>
                <a-descriptions :layout="descLayout" :column="{ xs: 1, sm: 1, md: 2, lg: 3 }">

                    <a-descriptions-item class="label-bold" :span="1" :label="$t('lead.card')">
                        {{ data.is_sale && data.is_sale.tarjeta ? data.is_sale.tarjeta : "-" }}
                    </a-descriptions-item>

                    <a-col :span="2">
                        <a-form-item :label="$t('lead.beneficiaries')">
                            <a-checkbox :checked="(data.is_sale && data.is_sale.aplicaBeneficiarios == 1)"></a-checkbox>
                        </a-form-item>
                    </a-col>

                </a-descriptions>
                <a-col v-if="data.is_sale && data.is_sale.aplicaBeneficiarios === 1" :xs="16" :sm="16" :md="8" :lg="8">
                    <a-form-item class="label-bold" :label="$t('lead.beneficiary_information')" :labelCol="{ span: 24 }"
                        :wrapperCol="{ span: 24 }">
                        <a-space direction="vertical" style="width: 100%;">
                            <div v-for="(benef, i) in beneficiarios" :key="i" style="display: flex; gap: 1rem;">
                                <a-form-item :name="['beneficiarios', i, 'nombre']" noStyle>
                                    <a-input :value="benef.nombre" :placeholder="$t('lead.name')" style="flex: 1;" />
                                </a-form-item>
                                <a-form-item :name="['beneficiarios', i, 'porcentaje']" noStyle>
                                    <a-input-number disabled v-model:value="benef.porcentaje" :min="0" :precision="0"
                                        :step="1" :placeholder="$t('lead.percentage')" style="width: 80px;" />
                                </a-form-item>
                            </div>
                        </a-space>
                    </a-form-item>
                </a-col>



            </a-tab-pane>

            <!-- Informacion de calidad de la venta -->
            <a-tab-pane key="calidad_data" class="text-center">
                <template #tab>
                    <span>
                        <UnorderedListOutlined />
                        {{ $t("common.information") }} {{ $t('menu.quality') }}
                    </span>
                </template>

                <a-typography-text v-if="!plantillaCalidad" type="warning" strong>
                    {{ $t("campaign.no_template") }}
                </a-typography-text>

                <a-form v-else>
                    <a-col :xs="24" :sm="24" :md="24" :lg="24">
                        <h2 v-if="plantillaCalidad.nombre"><strong>{{ $t('common.template') }}: {{ plantillaCalidad.nombre }}</strong></h2>
                        <strong><small v-if="plantillaCalidad.descripcion"> {{ plantillaCalidad.descripcion }}</small></strong>

                        <div v-if="plantillaCalidad.variables && plantillaCalidad.variables.length"
                            class="table-responsive">
                            <a-table :columns="columns" :dataSource="plantillaCalidad.variables" :pagination="false"
                                :row-key="(record) => record.xid" bordered size="middle">
                                <template #bodyCell="{ column, record }">
                                    <!-- tipo de variables -->
                                    <template v-if="column.dataIndex === 'tipo'">
                                        <a-tag v-if="record.tipo === 'critica'" color="#f5b041">
                                            {{ $t('message_template.critical_variable') }}
                                        </a-tag>
                                        <a-tag v-else color="#4cb050">
                                            {{ $t('message_template.critical_not_variable') }}
                                        </a-tag>
                                    </template>
                                    <!-- peso de las varibales -->
                                    <template v-if="column.dataIndex === 'nota_maxima'">
                                        {{ record.tipo === 'critica' ? 'N/A' :record.nota_maxima }}
                                    </template>
                                    <!-- slot para acciones -->
                                    <template v-if="column.dataIndex === 'acciones'">
                                        <a-checkbox class="checkbox-x" @change="selectVariable(record)"></a-checkbox>
                                    </template>
                                </template>
                                <template #footer>
                                    <div class="w-full text-right">
                                        <strong> {{ $t('common.qualification') }} : <span :style="{color: calificacion === 0  ? 'red' : calificacion > 0 && calificacion <= 50 ? '#f5b041' : 'inherit'}">{{ calificacion }}</span></strong>
                                    </div>
                                </template>

                            </a-table>
                        </div>
                        <a-typography-text v-else type="warning" strong>
                            {{ $t("campaign.no_template") }}
                        </a-typography-text>
                    </a-col>
                    <a-col v-if="plantillaCalidad.variables && plantillaCalidad.variables.length" :xs="24" :sm="24"
                        :md="24" :lg="24" style="margin-top: 20px;">
                        <a-row :gutter="[16, 16]">
                            <a-col :xs="{ span: 24 }" :sm="{ span: 24 }" :md="{ span: 12 }" :lg="{ span: 12 }">
                                <a-form-item :label="$t('common.action')" name="accion_calidad_id" class="required label-bold">
                                    <a-select 
                                        style="width: 100%"
                                        :placeholder="$t('common.select_default_text', [$t('common.action')])"
                                        :allowClear="true"
                                        show-search
                                        optionFilterProp="title"
                                    >
                                        <a-select-option v-for="accion in allAccionesCalidad" 
                                            :key="accion.xid"
                                            :value="accion.xid"
                                            :title="accion.nombre"
                                        >
                                            {{ accion.nombre }}
                                        </a-select-option>
                                    </a-select>
                                </a-form-item>

                            </a-col>

                            <a-col :xs="{ span: 24 }" :sm="{ span: 24 }" :md="{ span: 12 }" :lg="{ span: 12 }">
                                <a-form-item :label="$t('message_template.reason')" name="motivo_cancelacion_id" class="required label-bold">
                                    <a-select 
                                        style="width: 100%"
                                        :placeholder="$t('common.select_default_text', [$t('message_template.reason')])"
                                        :allowClear="true"
                                        show-search
                                        optionFilterProp="title"
                                    >
                                        <a-select-option v-for="motivo in allMotivosCalidad" 
                                            :key="motivo.xid"
                                            :value="motivo.xid"
                                            :title="motivo.motivo"
                                        >
                                            {{ motivo.motivo }}
                                        </a-select-option>
                                    </a-select>
                                </a-form-item>
                            </a-col>
                        </a-row>

                        <a-row :gutter="[16, 16]">
                            <a-col :xs="{ span: 24 }" :sm="{ span: 24 }" :md="{ span: 12 }" :lg="{ span: 12 }">
                                <a-form-item :label="$t('lead.call_duration')" name="minuto" class="required label-bold">
                                        <a-input-number style="width:100%" :placeholder="$t('lead.call_duration')" :min="0" />
                                </a-form-item>

                            </a-col>

                            <a-col :xs="{ span: 24 }" :sm="{ span: 24 }" :md="{ span: 12 }" :lg="{ span: 12 }">
                                <a-form-item :label="$t('message_template.minute')" name="minuto" class="required label-bold">
                                        <a-input-number style="width:100%" :placeholder="$t('message_template.minute')" :min="0" />
                                </a-form-item>

                            </a-col>

                            <a-col :xs="{ span: 24 }" :sm="{ span: 24 }" :md="{ span: 12 }" :lg="{ span: 12 }">
                                <a-form-item :label="$t('product.price')" name="minuto_precio" class="required label-bold">
                                        <a-input-number style="width:100%" :placeholder="$t('product.price')" :min="0" />
                                </a-form-item>
                            </a-col>

                            <a-col :xs="{ span: 24 }" :sm="{ span: 24 }" :md="{ span: 12 }" :lg="{ span: 12 }">
                                <a-form-item :label="$t('common.closing_sale')" name="cierre_venta" class="required label-bold">
                                    <a-select 
                                        style="width: 100%"
                                        :placeholder="$t('common.select_default_text', [$t('common.closing_sale')])"
                                        :allowClear="true"
                                        show-search
                                        optionFilterProp="title"
                                    >
                                        <a-select-option  
                                            key="si"
                                            value="si"
                                            :title="$t('common.yes')"
                                        >
                                            {{ $t('common.yes') }}
                                        </a-select-option>
                                        <a-select-option  
                                            key="motivo.xid"
                                            value="motivo.xid"
                                            :title="$t('common.no')"
                                        >
                                            {{ $t('common.no') }}
                                        </a-select-option>
                                    </a-select>
                                </a-form-item>
                            </a-col>
 <!-- enum('CERTIFICADA','RELLAMADA_CERTIFICADA','RELLAMADA','CANCELADA_CALIDAD','CANCELADA_SUPERVISION','REASIGNADA') -->
                            <a-col :xs="{ span: 24 }" :sm="{ span: 24 }" :md="{ span: 12 }" :lg="{ span: 12 }">
                                <a-form-item
                                    :label="$t('common.status')"
                                    name="cierre_venta"
                                    class="required label-bold"
                                >
                                    <a-select
                                        style="width: 100%"
                                        :placeholder="$t('common.select_default_text', [$t('common.status')])"
                                        :allowClear="true"
                                        show-search
                                        optionFilterProp="title"
                                    >
                                        <a-select-option
                                            v-for="opt in cierreVentaOptions"
                                            :key="opt.value"
                                            :value="opt.value"
                                            :title="opt.label"
                                        >
                                            {{ opt.label }}
                                        </a-select-option>
                                    </a-select>
                                </a-form-item>
                            </a-col>



                        </a-row>
                        
                    </a-col>
                </a-form>


            </a-tab-pane>

        </a-tabs>
    </a-drawer>
</template>
<script>
import { defineComponent, computed, ref, onMounted, watch } from "vue";
import { useI18n } from "vue-i18n";
import {
    UnorderedListOutlined,
    PhoneOutlined,
    FileTextOutlined,
} from "@ant-design/icons-vue";
import common from "../../../common/composable/common";
import crud from "../../../common/composable/crud";
// import fields from "./fields";
// import { value } from "lodash-es";
import { Grid } from 'ant-design-vue';

export default defineComponent({
    props: [
        "formData",
        "allCalidadTemplates",
        "allAccionesCalidad",
        "allMotivosCalidad",
        "data",
        "visible",
        "addEditType",
        "pageTitle",
        "successMessage",
    ],
    emits: ["close"],
    components: {
        UnorderedListOutlined,
        PhoneOutlined,
        FileTextOutlined,
    },
    setup(props, { emit }) {
        const { formatTimeDuration, formatDateTime, formatAmountCurrency } = common();
        const crudVariables = crud();
        // const {  } = fields();
        const { t } = useI18n();
        const drawerTitle = ref(t("lead.lead_details"));
        const activeTabKey = ref("info_venta");
        const beneficiarios = ref([]);
        const plantillaCalidad = ref([]);
        const calificacion = ref(100);
        const variablesUse = [];
        const notaInicial = 100;


        const columns = [
            { title: t('lead_status.type'), dataIndex: 'tipo', key: 'tipo' },
            { title: t('common.name'), dataIndex: 'nombre', key: 'nombre' },
            { title: t('common.description'), dataIndex: 'descripcion', key: 'descripcion' },
            { title: t('message_template.maximum_grade'), dataIndex: 'nota_maxima', key: 'nota_maxima' },
            { title: t('message_template.not_made'), dataIndex: 'acciones', key: 'acciones' },
        ];

        const cierreVentaOptions = [
            { value: 'CERTIFICADA', label: t('common.certified') },
            { value: 'RELLAMADA_CERTIFICADA', label: t('common.certified_recall') },
            { value: 'RELLAMADA', label: t('common.recall') },
            { value: 'CANCELADA_CALIDAD', label: t('common.cancelled_quality') },
            { value: 'CANCELADA_SUPERVISION', label: t('common.cancelled_supervision') },
            { value: 'REASIGNADA', label: t('common.reassigned') },
        ];

        const extraFilters = ref({
            lead_id: "",
        });


        onMounted(() => {

        });

        const onClose = () => {
            emit("closed");
        };

        const setNotesUrl = () => {
            emit("setNotesCount");
        };

        watch(
            () => props.visible,
            (newVal) => {
                if (newVal) {
                    activeTabKey.value = "info_venta";
                    if (props.data.is_sale.aplicaBeneficiarios) {

                        let raw = props.data.is_sale.beneficiarios || '[]';
                        let list;
                        try {
                            list = JSON.parse(raw);
                        } catch {
                            list = [];
                        }
                        beneficiarios.value = list;
                    } else {
                        beneficiarios.value = [];
                    }
                    obtenerPlantillaUso();
                }

            }
        );

        const obtenerPlantillaUso = () => {
            calificacion.value = 100;
            const campId = props.data.lead?.campaign?.x_plantilla_calidad_id;
            if (campId) {
                plantillaCalidad.value = props.allCalidadTemplates.find(
                    tpl => tpl.xid === campId
                );
            } else {
                plantillaCalidad.value = undefined;
            }
        };

        const calcularNota = () => {
            if (variablesUse.some(v => v.tipo === 'critica')) {
                return 0;
            }

            const totalRestar = variablesUse
                .filter(v => v.tipo !== 'critica')
                .reduce((sum, v) => sum + v.nota_maxima, 0);

            return Math.max(0, notaInicial - totalRestar);
        }

        const selectVariable = (variable) => {
            const idx = variablesUse.findIndex(v => v.id === variable.id);
            if (idx === -1) {
                variablesUse.push({ id: variable.id, tipo: variable.tipo, nota_maxima: variable.nota_maxima });
            } else {
                variablesUse.splice(idx, 1);
            }
            calificacion.value = calcularNota();
        };


        const screens = Grid.useBreakpoint();
        const descLayout = computed(() =>
            (screens.value.xs || screens.value < 950) ? 'vertical' : 'horizontal'
        );

        return {
            cierreVentaOptions,
            descLayout,
            calificacion,
            selectVariable,
            columns,
            plantillaCalidad,
            ...crudVariables,
            drawerTitle,
            beneficiarios,
            formatTimeDuration,
            activeTabKey,
            setNotesUrl,
            onClose,
            formatDateTime,
            formatAmountCurrency,
            drawerWidth: window.innerWidth <= 991 ? "90%" : "60%",
        };
    },
});
</script>
<style scoped>
    :deep(.checkbox-x .ant-checkbox-input:checked + .ant-checkbox-inner::after) {
        border: none !important;
        transform: none !important;
        content: '×' !important;
        position: absolute !important;
        top: 50% !important;
        left: 50% !important;
        transform: translate(-50%, -50%) !important;
        font-size: 14px !important;
        color: white !important;
    }
    :deep(.label-bold .ant-descriptions-item-label) {
        font-weight: bold;
    }
</style>