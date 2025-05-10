<template>
    <a-modal :open="visible" :closable="false" :centered="true" :width="isSele ? 1100 : 600" :title="pageTitle"
        @ok="onSubmit">
        <a-form layout="vertical">
            <a-row :gutter="[16, 16]">
                <!-- COLUMNA IZQUIERDA: formulario principal -->
                <a-col :xs="24" :sm="24" :md="isSele ? 8 : 24" :lg="isSele ? 8 : 24">
                    <a-row :gutter="[16, 16]">
                        <!-- Nivel 1 -->
                        <a-col :xs="24" :sm="24" :md="24" :lg="24">
                            <a-form-item :label="$t('lead_notes.notes_typification_1')" name="notes_typification_id_1"
                                :help="rules.notes_typification_id_1 ? rules.notes_typification_id_1.message : null"
                                :validateStatus="rules.notes_typification_id_1 ? 'error' : null">
                                <a-select v-model:value="formData.notes_typification_id_1"
                                    :placeholder="$t('common.select_default_text', [$t('lead_notes.notes_typification_1')])"
                                    optionFilterProp="title" show-search :allowClear="true" @change="() => {
                                        formData.notes_typification_id_2 = undefined;
                                        formData.notes_typification_id_3 = undefined;
                                        getChildTypification(formData.notes_typification_id_1);
                                    }">
                                    <a-select-option v-for="parentTypification in parentTypificationData"
                                        :key="parentTypification.xid" :title="parentTypification.name"
                                        :value="parentTypification.xid">
                                        {{ parentTypification.name }}
                                    </a-select-option>
                                </a-select>
                            </a-form-item>
                        </a-col>

                        <!-- Nivel 2 -->
                        <a-col v-if="childrenTypificationData.length > 0" :xs="24" :sm="24" :md="24" :lg="24">
                            <a-form-item :label="$t('lead_notes.notes_typification_2')" name="notes_typification_id_2"
                                :help="rules.notes_typification_id_2 ? rules.notes_typification_id_2.message : null"
                                :validateStatus="rules.notes_typification_id_2 ? 'error' : null">
                                <a-select v-model:value="formData.notes_typification_id_2"
                                    :placeholder="$t('common.select_default_text', [$t('lead_notes.notes_typification_2')])"
                                    optionFilterProp="title" show-search :allowClear="true" @change="() => {
                                        formData.notes_typification_id_3 = undefined;
                                        getChildrenChildTypification(formData.notes_typification_id_2);
                                    }">
                                    <a-select-option v-for="childrenTypification in childrenTypificationData"
                                        :key="childrenTypification.xid" :title="childrenTypification.name"
                                        :value="childrenTypification.xid">
                                        {{ childrenTypification.name }}
                                    </a-select-option>
                                </a-select>
                            </a-form-item>
                        </a-col>

                        <!-- Nivel 3 -->
                        <a-col v-if="childrenChildData.length > 0" :xs="24" :sm="24" :md="24" :lg="24">
                            <a-form-item :label="$t('lead_notes.notes_typification_3')" name="notes_typification_id_3"
                                :help="rules.notes_typification_id_3 ? rules.notes_typification_id_3.message : null"
                                :validateStatus="rules.notes_typification_id_3 ? 'error' : null">
                                <a-select v-model:value="formData.notes_typification_id_3"
                                    :placeholder="$t('common.select_default_text', [$t('lead_notes.notes_typification_3')])"
                                    optionFilterProp="title" show-search :allowClear="true">
                                    <a-select-option v-for="childrenChild in childrenChildData" :key="childrenChild.xid"
                                        :title="childrenChild.name" :value="childrenChild.xid">
                                        {{ childrenChild.name }}
                                    </a-select-option>
                                </a-select>
                            </a-form-item>
                        </a-col>

                        <!-- Comentario de la nota -->
                        <a-col :xs="24" :sm="24" :md="24" :lg="24">
                            <a-form-item :label="$t('common.notes')" name="notes"
                                :help="rules.notes ? rules.notes.message : null"
                                :validateStatus="rules.notes ? 'error' : null" class="required">
                                <a-textarea v-model:value="formData.notes"
                                    :placeholder="$t('common.placeholder_default_text', [$t('common.notes')])"
                                    :rows="4" />
                            </a-form-item>
                        </a-col>

                        <!-- Adjunto + checkbox -->
                        <a-col :xs="24" :sm="24" :md="24" :lg="24">
                            <a-form-item :label="$t('lead_notes.notes_file')">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <UploadFile :acceptFormat="'image/*,.pdf'" :formData="formData" folder="leadNotes"
                                        uploadField="notes_file" @onFileUploaded="file => {
                                            formData.notes_file = file.file;
                                            formData.notes_file_url = file.file_url;
                                        }" />
                                    <a-checkbox v-model:checked="isSele">
                                        {{ $t('lead_notes.sale') }}
                                    </a-checkbox>
                                </div>
                            </a-form-item>
                        </a-col>
                    </a-row>
                </a-col>

                <!-- COLUMNA DERECHA: campos adicionales -->
                <a-col v-if="isSele" :xs="24" :sm="24" :md="16" :lg="16">
                    <div class="side-panel" style="display: flex; align-items: center; gap: 12px;">
                        <a-col :xs="16" :sm="16" :md="12" :lg="12">
                            <!-- Cedula -->
                            <a-form-item :label="$t('lead.document')">
                                <a-input :value="leadInfo.cedula" />
                            </a-form-item>
                            <!-- Nombre cliente completo -->
                            <a-form-item :label="$t('lead.name')">
                                <a-input
                                    :value="leadInfo.nombre + ' ' + leadInfo.apellido1 + ' ' + leadInfo.apellido2" />
                            </a-form-item>
                            <!-- Email -->
                            <a-form-item :label="$t('lead.email')">
                                <a-input :value="leadInfo.email" />
                            </a-form-item>
                            <!-- Agente -->
                            <a-form-item :label="$t('lead.agent')">
                                <a-input :value="leadInfo.assign_to.name" />
                            </a-form-item>

                            <!-- Nombre base -->
                            <a-form-item :label="$t('lead.base_name')">
                                <a-input :value="leadInfo.nombreBase" />
                            </a-form-item>

                            <!-- Telefonos -->
                            <a-form-item :label="$t('lead.phone')">
                                <a-select v-model="selectedTel"
                                    :placeholder="$t('common.select_default_text', [$t('lead.phone'),])"
                                    style="width: 100%;">
                                    <a-select-option v-for="tel in [
                                        leadInfo.tel1,
                                        leadInfo.tel2,
                                        leadInfo.tel3,
                                        leadInfo.tel4,
                                        leadInfo.tel5,
                                        leadInfo.tel6
                                    ].filter(t => t)" :key="tel" :value="tel">
                                        {{ tel }}
                                    </a-select-option>
                                </a-select>
                            </a-form-item>

                        </a-col>
                        <a-col :xs="16" :sm="16" :md="12" :lg="12">

                            <!-- tarjeta -->
                            <a-form-item :label="$t('lead.card')">
                                <a-input :value="leadInfo.tarjeta" />
                            </a-form-item>

                            <!-- Productos -->
                            <a-form-item :label="$t('lead.product')">
                                <a-select v-model="selectedTel"
                                    :placeholder="$t('common.select_default_text', [$t('lead.product'),])"
                                    style="width: 100%;">
                                    <a-select-option v-for="tel in [
                                        leadInfo.tel1,
                                        leadInfo.tel2,
                                        leadInfo.tel3,
                                        leadInfo.tel4,
                                        leadInfo.tel5,
                                        leadInfo.tel6
                                    ].filter(t => t)" :key="tel" :value="tel">
                                        {{ tel }}
                                    </a-select-option>
                                </a-select>
                            </a-form-item>

                            <!-- Cobertura -->
                            <a-form-item :label="$t('lead.coverage')">
                                <a-select v-model="selectedTel"
                                    :placeholder="$t('common.select_default_text', [$t('lead.coverage'),])"
                                    style="width: 100%;">
                                    <a-select-option v-for="tel in [
                                        leadInfo.tel1,
                                        leadInfo.tel2,
                                        leadInfo.tel3,
                                        leadInfo.tel4,
                                        leadInfo.tel5,
                                        leadInfo.tel6
                                    ].filter(t => t)" :key="tel" :value="tel">
                                        {{ tel }}
                                    </a-select-option>
                                </a-select>
                            </a-form-item>

                            <!-- Precio y cantidad de producto -->
                            <a-row gutter="12" style="width:100%">
                                <!-- Columna izquierda -->
                                <a-col :span="12">
                                    <a-form-item :label="$t('lead.price')">
                                        <a-input-number
                                        style="width:120px"
                                        disabled
                                        :value="datos.venta.montoTotal"
                                        />
                                    </a-form-item>
                                </a-col>

                                <!-- Columna derecha, texto e input alineados a la derecha -->
                                <a-col :span="12" style="text-align: right">
                                    <a-form-item :label="$t('lead.product_quantity')">
                                        <a-input-number
                                        style="width:120px"
                                        :disabled="datos.producto === null"
                                        v-model:value="datos.venta.cantidadProducto"
                                        />
                                    </a-form-item>
                                </a-col>
                            </a-row>

                            <!-- Beneficiarios y % -->
                            <a-row gutter="12" style="width:100%">
                                <!-- Columna izquierda -->
                                <a-col :span="12">
                                    <a-form-item :label="$t('lead.beneficiaries')">

                                        <a-checkbox v-model:checked="esBeneficiario"></a-checkbox>
                                    </a-form-item>
                                </a-col>

                                <!-- Columna derecha, texto e input alineados a la derecha -->
                                <a-col :span="12" style="text-align: right">
                                    <a-form-item :label="$t('lead.beneficiary_percentage')">
                                        <a-input-number style="width:120px" :disabled="esBeneficiario === false" v-model:value="datos.venta.porcentajeBeneficiario" />
                                    </a-form-item>
                                </a-col>
                            </a-row>

                             <!-- Monto total -->
                            <a-form-item :label="$t('lead.total_amount')">
                                <a-input disabled :value="datos.venta.montoTotal" />
                            </a-form-item>

                        </a-col>


                    </div>
                </a-col>
            </a-row>
        </a-form>

        <template #footer>
            <a-button key="submit" type="primary" :loading="loading" @click="onSubmit">
                <SaveOutlined />
                {{ addEditType == "add" ? $t("common.create") : $t("common.update") }}
            </a-button>
            <a-button key="back" @click="onClose">
                {{ $t("common.cancel") }}
            </a-button>
        </template>
    </a-modal>
</template>

<script>
import { defineComponent, ref, reactive, onMounted, watch } from "vue";
import { PlusOutlined, LoadingOutlined, SaveOutlined } from "@ant-design/icons-vue";
import apiAdmin from "../../../common/composable/apiAdmin";
import UploadFile from "../../../common/core/ui/file/UploadFile.vue";
import { forEach } from "lodash-es";
import { data } from "jquery";

function getEmptyVenta() {
    return {
        id: null,
        estadoVenta: 'Efectiva', // Por defecto incialmente siempre efectiva
        telefonoVenta: null,
        producto: null,
        cantidadProducto: 0,
        esBeneficiarios: false,
        porcentajeBeneficiario: 0,
        montoTotal: 0
    }
};


export const datos = reactive({
    venta: getEmptyVenta()
});

export default defineComponent({
    props: [
        "formData",
        "leadInfo",
        "data",
        "visible",
        "url",
        "addEditType",
        "pageTitle",
        "successMessage",
    ],
    components: {
        PlusOutlined,
        LoadingOutlined,
        SaveOutlined,
        UploadFile,
    },
    setup(props, { emit }) {
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const notesTypifications = ref([]);
        const notesTypificationUrl = "notes-typifications?limit=10000";
        const parentTypificationData = ref([]);
        const childrenTypificationData = ref([]);
        const childrenChildData = ref([]);

        const isSele = ref(false);
        const esBeneficiario = ref(false);



        onMounted(() => {
            const notesTypificationPromise = axiosAdmin.get(notesTypificationUrl);

            Promise.all([notesTypificationPromise]).then(
                ([notesTypificationResponse]) => {
                    notesTypifications.value = notesTypificationResponse.data;
                    getParentTypification();
                }
            );
        });

        const getParentTypification = () => {
            parentTypificationData.value = [];
            forEach(notesTypifications.value, (parentData) => {
                if (parentData.x_parent_id == null) {
                    parentTypificationData.value.push(parentData);
                }
            });
        };

        const getChildTypification = (xid) => {
            childrenTypificationData.value = [];
            childrenChildData.value = [];
            if (xid !== undefined) {
                forEach(notesTypifications.value, (childrenData) => {
                    if (xid == childrenData.x_parent_id) {
                        childrenTypificationData.value.push(childrenData);
                    }
                });
            }
        };

        const getChildrenChildTypification = (xid) => {
            childrenChildData.value = [];
            if (xid !== undefined) {
                forEach(notesTypifications.value, (children) => {
                    if (xid == children.x_parent_id) {
                        childrenChildData.value.push(children);
                    }
                });
            }
        };

        const onSubmit = () => {
            addEditRequestAdmin({
                url: props.url,
                data: props.formData,
                successMessage: props.successMessage,
                success: (res) => {
                    emit("addEditSuccess", res.xid);
                },
            });
        };

        const onClose = () => {
            rules.value = {};
            emit("closed");
        };

        watch(
            () => props.visible,
            (newVal) => {
                getParentTypification();
                childrenTypificationData.value = [];
                childrenChildData.value = [];
                if (newVal && props.addEditType == "edit") {
                    if (props.formData.notes_typification_id_1 != null) {
                        getChildTypification(props.formData.notes_typification_id_1);
                    }
                    if (props.formData.notes_typification_id_2 != null) {
                        getChildrenChildTypification(props.formData.notes_typification_id_2);
                    }
                }
            }
        );

        return {
            datos,
            isSele,
            esBeneficiario,
            loading,
            rules,
            onClose,
            onSubmit,
            notesTypifications,
            parentTypificationData,
            childrenTypificationData,
            childrenChildData,
            getChildTypification,
            getChildrenChildTypification,
        };
    },
});
</script>

<style scoped>
.side-panel {
    padding: 0 16px;
    border-left: 1px solid #f0f0f0;
}
</style>