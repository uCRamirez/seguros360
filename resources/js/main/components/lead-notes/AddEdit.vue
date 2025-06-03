<template>
    <a-modal :open="visible" :closable="false" :centered="true" :width="isSale ? 1500 : 600" :title="pageTitle"
        @ok="onSubmit">
        <a-form layout="vertical" ref="formRef" :model="datos.venta" :rules="isSale ? validationRules : null">
            <a-row :gutter="[16, 16]">
                <!-- COLUMNA IZQUIERDA: formulario principal -->
                <a-col :xs="24" :sm="24" :md="isSale ? 8 : 24" :lg="isSale ? 8 : 24">
                    <a-row :gutter="[16, 16]">
                        <!-- Nivel 1 -->
                        <a-col :xs="24" :sm="24" :md="24" :lg="24">
                            <a-form-item class="required" :label="$t('lead_notes.notes_typification_1')"
                                name="notes_typification_id_1"
                                :help="rules.notes_typification_id_1 ? rules.notes_typification_id_1.message : null"
                                :validateStatus="rules.notes_typification_id_1 ? 'error' : null">
                                <a-select v-model:value="formData.notes_typification_id_1"
                                    :placeholder="$t('common.select_default_text', [$t('lead_notes.notes_typification_1')])"
                                    optionFilterProp="title" show-search :allowClear="true" @change="() => {
                                        formData.notes_typification_id_2 = undefined;
                                        formData.notes_typification_id_3 = undefined;
                                        formData.notes_typification_id_4 = undefined;
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
                                        formData.notes_typification_id_4 = undefined;
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
                                    optionFilterProp="title" show-search :allowClear="true" @change="() => {
                                        formData.notes_typification_id_4 = undefined;
                                        getLastChildrenChildTypification(formData.notes_typification_id_3);
                                    }">
                                    <a-select-option v-for="childrenChild in childrenChildData" :key="childrenChild.xid"
                                        :title="childrenChild.name" :value="childrenChild.xid">
                                        {{ childrenChild.name }}
                                    </a-select-option>
                                </a-select>
                            </a-form-item>
                        </a-col>

                        <!-- Nivel 4 -->
                        <a-col v-if="lastChildrenChildData.length > 0" :xs="24" :sm="24" :md="24" :lg="24">
                            <a-form-item :label="$t('lead_notes.notes_typification_4')" name="notes_typification_id_4"
                                :help="rules.notes_typification_id_4 ? rules.notes_typification_id_4.message : null"
                                :validateStatus="rules.notes_typification_id_4 ? 'error' : null">
                                <a-select v-model:value="formData.notes_typification_id_4"
                                    :placeholder="$t('common.select_default_text', [$t('lead_notes.notes_typification_4')])"
                                    optionFilterProp="title" show-search :allowClear="true">
                                    <a-select-option v-for="lastChildrenChild in lastChildrenChildData"
                                        :key="lastChildrenChild.xid" :title="lastChildrenChild.name"
                                        :value="lastChildrenChild.xid">
                                        {{ lastChildrenChild.name }}
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
                                    <a-checkbox :disabled="addEditType == 'edit'" v-model:checked="isSale">
                                        {{ $t('lead_notes.sale') }}
                                    </a-checkbox>
                                </div>
                            </a-form-item>
                        </a-col>
                    </a-row>
                </a-col>

                <!-- COLUMNA DERECHA: campos adicionales -->
                <a-col v-if="isSale" :xs="24" :sm="24" :md="16" :lg="16">
                    <div class="side-panel" style="display: flex; align-items: flex-start; gap: 12px;">

                        <a-col :xs="16" :sm="16" :md="esBeneficiario ? 8 : 12" :lg="esBeneficiario ? 8 : 12">

                            <a-col :xs="24" :sm="24" :md="24" :lg="24">
                                <!-- ID -->
                                <a-form-item :label="$t('lead.id')">
                                    <a-input :value="datos.venta.idLead" />
                                </a-form-item>
                            </a-col>
                            <a-col :xs="24" :sm="24" :md="24" :lg="24">
                                <!-- Cedula -->
                                <a-form-item :label="$t('lead.document')">
                                    <a-input :value="datos.venta.cedula" />
                                </a-form-item>
                            </a-col>
                            <a-col :xs="24" :sm="24" :md="24" :lg="24">
                                <!-- Nombre cliente completo -->
                                <a-form-item :label="$t('lead.name')">
                                    <a-input v-model:value="datos.venta.nombre" />
                                </a-form-item>
                            </a-col>
                            <a-col :xs="24" :sm="24" :md="24" :lg="24">
                                <!-- Email -->
                                <a-form-item :label="$t('lead.email')">
                                    <a-input v-model:value="datos.venta.email" />
                                </a-form-item>
                            </a-col>
                            <a-col :xs="24" :sm="24" :md="24" :lg="24">
                                <!-- Agente -->
                                <a-form-item :label="$t('lead.agent')">
                                    <a-input :value="datos.venta.agente" />
                                </a-form-item>
                            </a-col>
                            <a-col :xs="24" :sm="24" :md="24" :lg="24">
                                <!-- Nombre base -->
                                <a-form-item :label="$t('lead.base_name')">
                                    <a-input :value="datos.venta.nombreBase" />
                                </a-form-item>
                            </a-col>
                            <a-col :xs="24" :sm="24" :md="24" :lg="24">
                                <!-- Telefonos -->
                                <a-form-item name="telVenta" :label="$t('lead.phone')">
                                    <a-select v-model:value="datos.venta.telVenta"
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
                        </a-col>

                        <a-col :xs="16" :sm="16" style="border-left: 1px solid #f0f0f0;" :md="esBeneficiario ? 8 : 12"
                            :lg="esBeneficiario ? 8 : 12">

                            <a-col :xs="24" :sm="24" :md="24" :lg="24">
                                <!-- tarjeta -->
                                <a-form-item name="tarjeta" :label="$t('lead.card')">
                                    <a-input v-model:value="datos.venta.tarjeta" />
                                </a-form-item>
                            </a-col>
                            <a-col :xs="24" :sm="24" :md="24" :lg="24">
                                <!-- Code -->
                                <a-form-item name="internal_code" :label="$t('lead.internal_code')">
                                    <a-select v-model:value="datos.venta.internal_code"
                                        :placeholder="$t('common.select_default_text', [$t('lead.internal_code')])"
                                        style="width: 100%;" show-search option-filter-prop="title" :allowClear="true">
                                        <a-select-option v-for="code in internalCodeOptions" :title="code" :key="code"
                                            :value="code">
                                            {{ code }}
                                        </a-select-option>
                                    </a-select>
                                </a-form-item>
                            </a-col>
                            <a-col :xs="24" :sm="24" :md="24" :lg="24">
                                <!-- Producto -->
                                <a-form-item name="producto" :label="$t('lead.product')">
                                    <a-select v-model:value="datos.venta.producto"
                                        :placeholder="$t('common.select_default_text', [$t('lead.product')])"
                                        style="width: 100%;" show-search option-filter-prop="title" :allowClear="true">
                                        <a-select-option v-for="name in productNameOptions" :title="name" :key="name"
                                            :value="name">
                                            {{ name }}
                                        </a-select-option>
                                    </a-select>
                                </a-form-item>
                            </a-col>
                            <a-col :xs="24" :sm="24" :md="24" :lg="24">
                                <!-- Cobertura (aparece solo cuando code + product están definidos) -->
                                <a-form-item name="coverage" :label="$t('lead.coverage')">
                                    <a-select v-model:value="datos.venta.coverage"
                                        :placeholder="$t('common.select_default_text', [$t('lead.coverage')])"
                                        style="width: 100%;"
                                        :disabled="(!datos.venta.internal_code || !datos.venta.producto)" show-search
                                        option-filter-prop="title" :allowClear="true">
                                        <a-select-option v-for="cov in coverageOptions" :title="cov" :key="cov"
                                            :value="cov">
                                            {{ cov }}
                                        </a-select-option>
                                    </a-select>
                                </a-form-item>
                            </a-col>
                            <a-col :xs="24" :sm="24" :md="24" :lg="24">
                                <!-- Precio y Cantidad (igual que antes) -->
                                <a-row gutter="12">
                                    <!-- Precio -->
                                    <a-col :span="12">
                                        <a-form-item name="precio" :label="$t('lead.price')">
                                            <!-- 1) Si hay más de un producto candidato: select de precios -->
                                            <a-select v-if="matchingProducts.length > 1"
                                                v-model:value="datos.venta.precio"
                                                :placeholder="$t('common.select_default_text', [$t('lead.price')])"
                                                style="width: 100%;" show-search option-filter-prop="title" allowClear>
                                                <a-select-option v-for="p in matchingProducts" :key="p.xid"
                                                    :value="p.price" :title="formatAmountCurrency(p.price)">
                                                    {{ formatAmountCurrency(p.price) }}
                                                </a-select-option>
                                            </a-select>

                                            <!-- 2) Si hay exactamente uno: lo mostramos en un input bloqueado -->
                                            <a-input-number v-else-if="matchingProducts.length === 1"
                                                :value="formatAmountCurrency(datos.venta.precio)" disabled
                                                style="width: 120px;" />

                                            <!-- 3) Si aún no hay cobertura o candidatos: campo bloqueado vacío -->
                                            <a-input-number v-else :value="formatAmountCurrency(datos.venta.precio)"
                                                disabled style="width: 120px;" />
                                        </a-form-item>
                                    </a-col>

                                    <!-- Cantidad -->
                                    <a-col :span="12" style="text-align: right;">
                                        <a-form-item name="product_quantity" :label="$t('lead.product_quantity')">
                                            <a-input-number :min="1" v-model:value="datos.venta.cantidadProducto"
                                                :disabled="!datos.venta.montoTotal" style="width:120px" />
                                        </a-form-item>
                                    </a-col>
                                </a-row>
                            </a-col>

                            <a-col :xs="24" :sm="24" :md="24" :lg="24">
                                <!-- Monto total -->
                                <a-form-item name="total_amount" :label="$t('lead.total_amount')">
                                    <a-input-number style="width:100%" disabled
                                        :value="formatAmountCurrency(datos.venta.montoTotal)" />
                                </a-form-item>
                            </a-col>
                            <a-col :xs="24" :sm="24" :md="24" :lg="24">
                                <!-- Beneficiarios y % -->
                                <a-row gutter="12" style="width:100%">
                                    <a-col :span="12">
                                        <a-form-item :label="$t('lead.beneficiaries')">
                                            <a-checkbox v-model:checked="esBeneficiario"></a-checkbox>
                                        </a-form-item>
                                    </a-col>

                                    <a-col :span="12" style="text-align: right">
                                        <a-form-item :label="$t('lead.number_beneficiaries')">
                                            <a-input-number style="width:120px" :disabled="esBeneficiario === false"
                                                :min="0" :max="10" v-model:value="datos.venta.cantidadBeneficiarios" />
                                        </a-form-item>
                                    </a-col>
                                </a-row>
                            </a-col>
                        </a-col>

                        <a-col v-if="esBeneficiario" style="border-left: 1px solid #f0f0f0;" :xs="16" :sm="16" :md="8"
                            :lg="8">
                            <a-form-item style="justify-content: center;" :label="$t('lead.beneficiary_information')">

                                <a-space v-for="(benef, i) in datos.venta.beneficiarios" :key="i"
                                    style="display: flex; align-items: center;">
                                    <a-form-item :name="['beneficiarios', i, 'nombre']"
                                        :rules="[{ required: true, message: $t('lead.name') }]">
                                        <a-input v-model:value="benef.nombre" :placeholder="$t('lead.name')" />
                                    </a-form-item>
                                    <a-form-item  :name="['beneficiarios', i, 'porcentaje']"
                                        :rules="[{ required: true, type: 'number', message: $t('uphone_calls.number') }]"
                                    >
                                        <a-input-number v-model:value="benef.porcentaje" :min="0" :precision="0"
                                            :step="1" :placeholder="$t('lead.percentage')" />
                                     </a-form-item>
                                    <a-form-item>
                                        <MinusCircleOutlined @click="removeUser(i)" />
                                    </a-form-item>
                                </a-space>
                                <a-form-item>
                                    <a-button :disabled="datos.venta.cantidadBeneficiarios === 10" type="dashed" block
                                        @click="addUser">
                                        <PlusOutlined />
                                        {{ $t('common.add') }}
                                    </a-button>
                                </a-form-item>

                            </a-form-item>
                        </a-col>


                    </div>
                </a-col>
            </a-row>
        </a-form>

        <template #footer>
            <a-button v-if="addEditType == 'add' && (permsArray.includes('admin') || permsArray.includes('forms_view'))" key="submit" type="primary" :loading="loading" @click="onSubmit">
                <SaveOutlined />
                {{ $t("common.create") }}
            </a-button>
            <a-button v-if="addEditType == 'edit' && permsArray.includes('admin')" key="submit" type="primary" :loading="loading" @click="onSubmit">
                <SaveOutlined />
                {{ $t("common.update") }}
            </a-button>
            <a-button key="back" @click="onClose">
                {{ $t("common.cancel") }}
            </a-button>
        </template>
    </a-modal>
</template>

<script>
import { defineComponent, ref, reactive, onMounted, nextTick, watch, computed, toRef } from "vue";
import { PlusOutlined, MinusCircleOutlined, LoadingOutlined, SaveOutlined } from "@ant-design/icons-vue";
import apiAdmin from "../../../common/composable/apiAdmin";
import common from "../../../common/composable/common";
import UploadFile from "../../../common/core/ui/file/UploadFile.vue";
import { useI18n } from "vue-i18n";
import { fill } from "lodash-es";

function getEmptyVenta() {
    return {
        idLead: null,
        cedula: "",
        nombre: "",
        email: "",
        agente: "",
        user_id: null,
        nombreBase: "",
        tarjeta: "",
        idProducto: null,
        internal_code: null,
        producto: null,
        coverage: null,
        precio: 0,
        estadoVenta: "Efectiva",
        telVenta: null,
        cantidadProducto: 0,
        aplicaBeneficiarios: false,
        cantidadBeneficiarios: 0,
        beneficiarios: [],
        montoTotal: 0,
    };
}

export const datos = reactive({
    venta: getEmptyVenta(),
});

export default defineComponent({
    props: [
        "formData",
        "leadInfo",
        "allProductos",
        "data",
        "visible",
        "url",
        "addEditType",
        "pageTitle",
        "successMessage",
    ],
    components: {
        PlusOutlined,
        MinusCircleOutlined,
        LoadingOutlined,
        SaveOutlined,
        UploadFile,
    },
    setup(props, { emit }) {
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const { permsArray, formatAmountCurrency } = common();
        const { t } = useI18n();

        // Typifications
        const notesTypifications = ref([]);
        const notesTypificationUrl = "notes-typifications?limit=10000";
        const parentTypificationData = ref([]);
        const childrenTypificationData = ref([]);
        const childrenChildData = ref([]);
        const lastChildrenChildData = ref([]);
        const isInitializing = ref(false);
        onMounted(() => {
            axiosAdmin.get(notesTypificationUrl).then(res => {
                notesTypifications.value = res.data;
                getParentTypification();
            });
        });

        function getParentTypification() {
            parentTypificationData.value = [];
            notesTypifications.value.forEach(p => {
                if (p.x_parent_id == null) parentTypificationData.value.push(p);
            });
        }
        function getChildTypification(xid) {
            childrenTypificationData.value = [];
            childrenChildData.value = [];
            lastChildrenChildData.value = [];
            notesTypifications.value.forEach(c => {
                if (c.x_parent_id === xid) childrenTypificationData.value.push(c);
            });
        }
        function getChildrenChildTypification(xid) {
            childrenChildData.value = [];
            lastChildrenChildData.value = [];
            notesTypifications.value.forEach(c => {
                if (c.x_parent_id === xid) childrenChildData.value.push(c);
            });
        }
        function getLastChildrenChildTypification(xid) {
            lastChildrenChildData.value = [];
            notesTypifications.value.forEach(c => {
                if (c.x_parent_id === xid) lastChildrenChildData.value.push(c);
            });
        }

        // Flags sale/beneficiarios
        const isSale = ref(false);
        const esBeneficiario = ref(false);

        // Computed lists
        const filteredByCode = computed(() =>
            datos.venta.internal_code
                ? props.allProductos.filter(p => p.internal_code === datos.venta.internal_code)
                : props.allProductos
        );
        const filteredByName = computed(() =>
            datos.venta.producto
                ? props.allProductos.filter(p => p.name === datos.venta.producto)
                : props.allProductos
        );
        const intersection = computed(() => {
            let arr = props.allProductos;
            if (datos.venta.internal_code) arr = arr.filter(p => p.internal_code === datos.venta.internal_code);
            if (datos.venta.producto) arr = arr.filter(p => p.name === datos.venta.producto);
            return arr;
        });
        const matchingProducts = computed(() =>
            intersection.value.filter(p => p.coverage === datos.venta.coverage)
        );
        const internalCodeOptions = computed(() =>
            [...new Set(filteredByName.value.map(p => p.internal_code))]
        );
        const productNameOptions = computed(() =>
            [...new Set(filteredByCode.value.map(p => p.name))]
        );
        const coverageOptions = computed(() =>
            [...new Set(intersection.value.map(p => p.coverage))]
        );
        const finalProduct = computed(() =>
            intersection.value.find(p => p.coverage === datos.venta.coverage) || {}
        );

        // Watchers refactorizados
        watch(() => datos.venta.internal_code, internal_code => {
            if (isInitializing.value) return;
            if (internal_code && !productNameOptions.value.includes(datos.venta.producto)) {
                datos.venta.producto = null;
            }
            datos.venta.coverage = null;
            datos.venta.precio = 0;
        });

        watch(() => datos.venta.producto, prod => {
            if (isInitializing.value) return;
            if (prod && !internalCodeOptions.value.includes(datos.venta.internal_code)) {
                datos.venta.internal_code = null;
            }
            datos.venta.coverage = null;
        });

        watch(() => datos.venta.coverage, cov => {
            if (isInitializing.value) return;
            const precio = finalProduct.value.price || 0;
            datos.venta.precio = precio;
            datos.venta.cantidadProducto = cov
                ? (props.addEditType === 'edit'
                    ? datos.venta.cantidadProducto
                    : 1
                )
                : 0;
            datos.venta.montoTotal = precio * datos.venta.cantidadProducto;
        });

        watch(() => datos.venta.precio, (nuevoPrecio) => {
            if (isInitializing.value) return;
            if (!nuevoPrecio) {
                datos.venta.cantidadProducto = 0;
                datos.venta.montoTotal = 0;
            } else {
                datos.venta.cantidadProducto = 1;
                datos.venta.montoTotal = nuevoPrecio;
            }
        },
            { immediate: true }
        );


        watch(() => datos.venta.cantidadProducto, qty => {
            if (isInitializing.value) return;
            datos.venta.montoTotal = datos.venta.precio * qty;
        }, { immediate: true });

        watch(
            matchingProducts,
            (lista) => {
                if (isInitializing.value) return;
                if (lista.length >= 1) {
                    const p = lista[0];
                    datos.venta.idProducto = p.xid;
                    datos.venta.precio = p.price;
                } else {
                    datos.venta.idProducto = null;
                    datos.venta.precio = null;
                    datos.venta.cantidadProducto = 0;
                    datos.venta.montoTotal = 0;
                }
            },
            { immediate: true }
        );


        // Mostrar/editar
        watch(() => props.visible, async newVal => {
            datos.venta = getEmptyVenta();
            isSale.value = false;
            getParentTypification();
            childrenTypificationData.value = [];
            childrenChildData.value = [];
            lastChildrenChildData.value = [];
            if (newVal && props.addEditType === "edit") {
                isInitializing.value = true;
                if (props.formData.notes_typification_id_1 != null)
                    getChildTypification(props.formData.notes_typification_id_1);
                if (props.formData.notes_typification_id_2 != null)
                    getChildrenChildTypification(props.formData.notes_typification_id_2);
                if (props.formData.notes_typification_id_3 != null)
                    getLastChildrenChildTypification(props.formData.notes_typification_id_3);

                datos.venta = (isSale.value = !!props.formData.is_sale)
                    ? { ...props.formData.is_sale }
                    : getEmptyVenta();

                if (isSale.value) {
                    const prod = props.allProductos.find(p => p.id === datos.venta.idProducto);
                    if (prod) {
                        datos.venta.internal_code = prod.internal_code;
                        datos.venta.producto = prod.name;
                        datos.venta.coverage = prod.coverage;
                        datos.venta.precio = prod.price;
                    }
                }

                if (props.formData.is_sale.aplicaBeneficiarios) {
                    esBeneficiario.value = true

                    let raw = props.formData.is_sale.beneficiarios || '[]'
                    let list
                try {
                    list = JSON.parse(raw)
                } catch {
                    list = []
                }
                    datos.venta.beneficiarios = list
                } else {
                    esBeneficiario.value = false
                    datos.venta.beneficiarios = []
                }

            }
            Object.assign(
                datos.venta,
                props.addEditType === "add" ? getEmptyVenta() : {},
                {
                    idLead: props.leadInfo.id,
                    cedula: props.leadInfo.cedula,
                    nombre: `${props.leadInfo.nombre} ${props.leadInfo.apellido1} ${props.leadInfo.apellido2}`,
                    email: props.leadInfo.email,
                    agente: props.addEditType === "edit"
                        ? (props.data.user?.name ?? "")
                        : props.leadInfo.assign_to.name,
                    user_id: props.leadInfo.assign_to.id,

                    nombreBase: props.leadInfo.nombreBase,

                    tarjeta: props.addEditType === "edit"
                        ? (props.formData.is_sale?.tarjeta ?? "")
                        : props.leadInfo.tarjeta,

                    telVenta: props.addEditType === "edit"
                        ? (props.formData.is_sale?.telVenta ?? null)
                        : null,

                }
            );

            // 3) Espera al siguiente tick para que Vue aplique todos los cambios
            await nextTick();
            isInitializing.value = false;
        });

        // Validaciones beneficiarios
        const formRef = ref();
        const validationRules = reactive({
            telVenta: [{ required: true, message: t('lead.phone') }],
            internal_code: [{ required: true, message: t('lead.internal_code') }],
            producto: [{ required: true, message: t('lead.product') }],
            coverage: [{ required: true, message: t('lead.coverage') }],
            precio: [{ required: true, message: t('lead.price') }],
            tarjeta: [{ required: true, message: t('lead.card') }],
            beneficiarios: [],
        });

        watch(
            () => datos.venta.cantidadBeneficiarios,
            count => {
                if (isInitializing.value) return;
                // recorta exceso
                datos.venta.beneficiarios.splice(count);
                // añade faltantes
                while (datos.venta.beneficiarios.length < count) {
                    datos.venta.beneficiarios.push({ nombre: "", porcentaje: 0 });
                }
            },
            { immediate: true }
        );

        watch(
            () => datos.venta.cantidadProducto,
            qty => {
                if (isInitializing.value) return;

                datos.venta.montoTotal = datos.venta.precio * qty;
            },
            { immediate: true }
        );

        const addUser = () => {
            datos.venta.cantidadBeneficiarios++;
        };
        const removeUser = (index) => {
            datos.venta.beneficiarios.splice(index, 1);
            datos.venta.cantidadBeneficiarios = datos.venta.beneficiarios.length;
            if (props.addEditType === 'edit' || props.formData.is_sale) {
                removeUserFormData(index);
            }
        };

        const removeUserFormData = (index) => {
            const arr = JSON.parse(props.formData.is_sale.beneficiarios || '[]')
            arr.splice(index, 1)
            props.formData.is_sale.beneficiarios = JSON.stringify(arr)
        }


        const clearDataVenta = () => {
            datos.venta = getEmptyVenta();
        };

        // Submit
        const onSubmit = async () => {
            try {
                props.formData.isSale = isSale.value ? 1 : 0;
                props.formData.campaign_id = props.leadInfo.campaign.id;

                props.formData.lead_id ||= props.leadInfo.xid;

                if (isSale.value) {
                    await formRef.value.validate();

                    addEditRequestAdmin({
                        url: props.url,
                        data: props.formData,
                        successMessage: null,
                        success: (res) => {
                            datos.venta.idNota = res.xid;
                            datos.venta.accion = props.addEditType;
                            datos.venta.aplicaBeneficiarios = esBeneficiario.value;
                            datos.venta.beneficiarios = esBeneficiario
                            ? JSON.stringify(datos.venta.beneficiarios)
                            : {};

                            addEditRequestAdmin({
                                url: 'ventas/save',
                                data: datos.venta,
                                successMessage: props.successMessage,
                                success: () => { },
                            });
                            emit("addEditSuccess", res.xid);
                            clearDataVenta();
                        },
                    });
                } else {
                    addEditRequestAdmin({
                        url: props.url,
                        data: props.formData,
                        successMessage: props.successMessage,
                        success: (res) => {
                            emit("addEditSuccess", res.xid);
                            clearDataVenta();
                        },
                    });
                }
            } catch (validationErrors) {
                console.log("Errores de validación:", validationErrors);
            }
        };

        const onClose = () => {
            clearDataVenta();
            rules.value = {};
            emit("closed");
        };

        return {
            datos,
            isSale,
            esBeneficiario,
            loading,
            rules,
            internalCodeOptions,
            productNameOptions,
            coverageOptions,
            matchingProducts,
            formatAmountCurrency,
            notesTypifications,
            parentTypificationData,
            childrenTypificationData,
            childrenChildData,
            lastChildrenChildData,
            getChildTypification,
            getChildrenChildTypification,
            getLastChildrenChildTypification,
            formRef,
            validationRules,
            addUser,
            removeUser,
            onSubmit,
            onClose,
            permsArray,
        };
    },
});
</script>



<style scoped>
.side-panel {
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

@media (max-width: 895px) {
    .side-panel>.ant-col {
        /* selecciona cada col de Ant */
        flex-wrap: wrap;
        /* permite envoltura */
        flex: 0 0 100% !important;
        /* ancho 100% */
        max-width: 100% !important;
        flex-wrap: wrap;
    }
}
</style>