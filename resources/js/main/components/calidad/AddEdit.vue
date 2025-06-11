<template>
    <a-drawer
        :title="$t('menu.quality')"
        :width="drawerWidth"
        :open="visible"
        :body-style="{ paddingBottom: '80px' }"
        :maskClosable="false"
        @close="onClose"
        :destroyOnClose="true"
    >
    <!-- Informaion general del lead -->
        <a-descriptions :title="$t('common.basic_details')">
            <!-- <a-descriptions-item :label="$t('lead.campaign')">
                {{ data.lead && data.lead.campaign ? data.lead.campaign.name : "-" }}
            </a-descriptions-item>
            <a-descriptions-item :label="$t('lead.call_duration')">
                {{ data.lead && data.lead.time_taken ? formatTimeDuration(data.lead.time_taken) : "-" }}
            </a-descriptions-item> -->
            <a-descriptions-item :label="$t('uphone_calls.client_id')">
                {{
                    data.lead && data.lead.id && data.lead.id
                        ? data.lead.id
                        : "-"
                }}
            </a-descriptions-item>
            <a-descriptions-item :label="$t('lead.document')">
                {{ data.lead && data.lead.cedula ? data.lead.cedula : "-" }}
            </a-descriptions-item>
            <a-descriptions-item :label="$t('lead.name')">
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
            <a-descriptions-item :label="$t('lead.date_birth')">
                {{
                    data.lead && data.lead.fechaNacimiento && data.lead.fechaNacimiento
                        ? formatDateTime(data.lead.fechaNacimiento)
                        : "-"
                }}
            </a-descriptions-item>
            <a-descriptions-item :label="$t('lead.age')">
                {{
                    data.lead && data.lead.edad && data.lead.edad
                        ? data.lead.edad
                        : "-"
                }}
            </a-descriptions-item>
            <a-descriptions-item :label="$t('lead.base_name')">
                {{
                    data.lead && data.lead.nombreBase && data.lead.nombreBase
                        ? data.lead.nombreBase
                        : "-"
                }}
            </a-descriptions-item>
            <a-descriptions-item :label="$t('lead.nationality')">
                {{
                    data.lead && data.lead.nacionalidad && data.lead.nacionalidad
                        ? data.lead.nacionalidad
                        : "-"
                }}
            </a-descriptions-item>
            <a-descriptions-item :label="$t('lead.email')">
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
                <a-descriptions>

                    <a-descriptions-item :label="$t('lead.id') + ' ' + $t('lead_notes.sale')">
                        {{ data.is_sale && data.is_sale.idVenta ? data.is_sale.idVenta : "-" }}
                    </a-descriptions-item>

                    <a-descriptions-item :label="$t('lead_notes.date_time')">
                        {{ data.date_time ? formatDateTime(data.date_time) : "-" }}
                    </a-descriptions-item>

                    <a-descriptions-item :label="$t('lead_log.user_id')">
                        {{ data.is_sale.user && data.is_sale.user.name ? data.is_sale.user.name : "-" }}
                    </a-descriptions-item>

                    <a-descriptions-item :label="$t('lead.internal_code')">
                        {{ data.is_sale.product && data.is_sale.product.internal_code ? data.is_sale.product.internal_code : "-" }}
                    </a-descriptions-item>

                    <a-descriptions-item :label="$t('lead.product')">
                        {{ data.is_sale.product && data.is_sale.product.name ? data.is_sale.product.name : "-" }}
                    </a-descriptions-item>

                    <a-descriptions-item :label="$t('lead.coverage')">
                        {{ data.is_sale.product && data.is_sale.product.coverage ? data.is_sale.product.coverage : "-" }}
                    </a-descriptions-item>

                    <a-descriptions-item :label="$t('lead.price')">
                        {{ data.is_sale.product && data.is_sale.product.price ? formatAmountCurrency(data.is_sale.product.price) : "-" }}
                    </a-descriptions-item>

                    <a-descriptions-item :label="$t('lead.total_amount')">
                        {{ data.is_sale && data.is_sale.montoTotal ? formatAmountCurrency(data.is_sale.montoTotal) : "-" }}
                    </a-descriptions-item>

                    <a-descriptions-item :label="$t('lead.phone')">
                        {{ data.is_sale && data.is_sale.telVenta ? data.is_sale.telVenta : "-" }}
                    </a-descriptions-item>

                    <a-descriptions-item :label="$t('lead.card')">
                        {{ data.is_sale && data.is_sale.tarjeta ? data.is_sale.tarjeta : "-" }}
                    </a-descriptions-item>

                    <a-col :span="12">
                        <a-form-item :label="$t('lead.beneficiaries')">
                            <a-checkbox :checked="(data.is_sale && data.is_sale.aplicaBeneficiarios == 1)"></a-checkbox>
                        </a-form-item>
                    </a-col>

                    <a-col v-if="data.is_sale && data.is_sale.aplicaBeneficiarios === 1" :xs="16" :sm="16" :md="8" :lg="8" >
                        <a-form-item :label="$t('lead.beneficiary_information')" :labelCol="{ span: 24 }" :wrapperCol="{ span: 24 }" >
                            <a-space direction="vertical" style="width: 100%;">
                                <div
                                    v-for="(benef, i) in beneficiarios"
                                    :key="i"
                                    style="display: flex; gap: 1rem;"
                                >
                                    <a-form-item :name="['beneficiarios', i, 'nombre']" noStyle>
                                        <a-input :value="benef.nombre" :placeholder="$t('lead.name')" style="flex: 1;"/>
                                    </a-form-item>
                                    <a-form-item :name="['beneficiarios', i, 'porcentaje']" noStyle>
                                        <a-input-number disabled v-model:value="benef.porcentaje" :min="0" :precision="0" :step="1" :placeholder="$t('lead.percentage')" style="width: 80px;" />
                                    </a-form-item>
                                </div>
                            </a-space>
                        </a-form-item>
                    </a-col>


                </a-descriptions>

                
                
            </a-tab-pane>
            <!-- Informacion de calidad de la venta -->
            <a-tab-pane key="calidad_data">
                <template #tab>
                    <span>
                        <UnorderedListOutlined />
                        {{ $t("common.information") }} {{ $t('menu.quality') }}
                    </span>
                </template>
            </a-tab-pane>
            
        </a-tabs>
    </a-drawer>
</template>
<script>
import { defineComponent,computed, ref, onMounted, watch } from "vue";
import { useI18n } from "vue-i18n";
import {
    UnorderedListOutlined,
    PhoneOutlined,
    FileTextOutlined,
} from "@ant-design/icons-vue";
import common from "../../../common/composable/common";
import crud from "../../../common/composable/crud";
import fields from "./fields";

export default defineComponent({
    props: [
        "formData",
        "allProductos",
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
        // const notesTypificationUrl = "notes-typifications?limit=10000";
        // const notesTypifications = ref([]);
        // const parentTypificationData = ref([]);
        // const childrenTypificationData = ref([]);
        // const childrenChildData = ref([]);
        // const lastChildrenChildData = ref([]);
        const extraFilters = ref({
            lead_id: "",
        });

        // funciones para obtener la tipificacion especifica
        // function getParentTypification() {
        //     parentTypificationData.value = [];
        //     notesTypifications.value.forEach(p => {
        //         if (p.x_parent_id == null) parentTypificationData.value.push(p);
        //     });
        // }
        // function getChildTypification(xid) {
        //     childrenTypificationData.value = [];
        //     childrenChildData.value = [];
        //     lastChildrenChildData.value = [];
        //     notesTypifications.value.forEach(c => {
        //         if (c.x_parent_id === xid) childrenTypificationData.value.push(c);
        //     });
        // }
        // function getChildrenChildTypification(xid) {
        //     childrenChildData.value = [];
        //     lastChildrenChildData.value = [];
        //     notesTypifications.value.forEach(c => {
        //         if (c.x_parent_id === xid) childrenChildData.value.push(c);
        //     });
        // }
        // function getLastChildrenChildTypification(xid) {
        //     lastChildrenChildData.value = [];
        //     notesTypifications.value.forEach(c => {
        //         if (c.x_parent_id === xid) lastChildrenChildData.value.push(c);
        //     });
        // }

        onMounted(() => {
            // axiosAdmin.get(notesTypificationUrl).then(res => {
            //     notesTypifications.value = res.data;
            //     getParentTypification();
            // });
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
                    activeTabKey.value= "info_venta";

                    // if (props.formData.notes_typification_id_1 != null)
                    // getChildTypification(props.formData.notes_typification_id_1);
                    // if (props.formData.notes_typification_id_2 != null)
                    //     getChildrenChildTypification(props.formData.notes_typification_id_2);
                    // if (props.formData.notes_typification_id_3 != null)
                    //     getLastChildrenChildTypification(props.formData.notes_typification_id_3);
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
                }

            }
        );

        return {
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
