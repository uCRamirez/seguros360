<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.quality`)" class="p-0" />
        </template>
        <template #breadcrumb>
            <a-breadcrumb separator="-" style="font-size: 12px">
                <a-breadcrumb-item>
                    <router-link :to="{ name: 'admin.dashboard.index' }">
                        {{ $t(`menu.dashboard`) }}
                    </router-link>
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.quality`) }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </AdminPageHeader>

    <admin-page-filters>
        <a-row :gutter="[16, 16]">
            <a-col :xs="24" :sm="24" :md="12" :lg="10" :xl="10">
                <a-space>
                    <template v-if="
                        permsArray.includes('plantillas_calidad_create') ||
                        permsArray.includes('admin')
                        && activeTabKey === 'templates'
                    ">
                        <a-button type="primary" @click="addItem">
                            <PlusOutlined />
                            {{ $t("message_template.add") }}
                        </a-button>
                    </template>
                    <a-button v-if="
                        table.selectedRowKeys.length > 0 &&
                        (permsArray.includes('plantillas_calidad_delete') ||
                            permsArray.includes('admin'))
                        && activeTabKey === 'templates'
                    " type="primary" @click="showSelectedDeleteConfirm" danger>
                        <template #icon>
                            <DeleteOutlined />
                        </template>
                        {{ $t("common.delete") }}
                    </a-button>
                </a-space>
            </a-col>
            <a-col :xs="24" :sm="24" :md="12" :lg="14" :xl="14">
                <a-row :gutter="[16, 16]" justify="end">
                    <a-col :xs="24" :sm="24" :md="12" :lg="12" :xl="8">
                        <a-input-group compact>
                            <a-select style="width: 35%" v-model:value="table.searchColumn" :placeholder="$t('common.select_default_text', [''])
                                ">
                                <a-select-option v-for="filterableColumn in filterableColumns"
                                    :key="filterableColumn.key">
                                    {{ filterableColumn.value }}
                                </a-select-option>
                            </a-select>
                            <a-input-search style="width: 65%" v-model:value="table.searchString" show-search
                                @change="onTableSearch" @search="onTableSearch" :loading="table.filterLoading" />
                        </a-input-group>
                    </a-col>
                </a-row>
            </a-col>
        </a-row>
    </admin-page-filters>

    <admin-page-table-content>
        <AddEdit :addEditType="addEditType" :visible="addEditVisible" :url="addEditUrl" @addEditSuccess="addEditSuccess"
            @closed="onCloseAddEdit" :formData="formData" :data="viewData" :pageTitle="pageTitle"
            :successMessage="successMessage" />
        <a-tabs v-model:activeKey="activeTabKey">
            <a-tab-pane key="templates">
                <template #tab>
                    <span>
                        <UnorderedListOutlined />
                        {{ $t("menu.templates") }}
                    </span>
                </template>
                <a-col :span="24">
                    <div class="table-responsive">
                        <a-table :row-selection="{
                            selectedRowKeys: table.selectedRowKeys,
                            onChange: onRowSelectChange,
                            getCheckboxProps: (record) => ({
                                disabled: false,
                                name: record.xid,
                            }),
                        }" :columns="columns" :row-key="(record) => record.xid" :data-source="table.data"
                            :pagination="table.pagination" :loading="table.loading" @change="handleTableChange" bordered
                            size="middle">
                            <template #bodyCell="{ column, record }">
                                <template v-if="column.dataIndex === 'activo'">
                                    <a-tag v-if="record.activo === true" color="#4cb050">
                                        {{ $t('common.active') }}
                                    </a-tag>
                                    <a-tag v-else color="#f5b041">
                                        {{ $t('common.inactive') }}
                                    </a-tag>
                                </template>
                                <template v-if="column.dataIndex === 'created_at'">
                                    {{ formatDateTime(record.created_at) }}
                                </template>
                                <template v-if="column.dataIndex === 'action'">
                                    <a-tooltip :title="$t('common.edit')">
                                        <a-button v-if="
                                            permsArray.includes(
                                                'plantillas_calidad_edit'
                                            ) || permsArray.includes('admin')
                                        " type="primary" @click="editItem(record)" style="margin-left: 4px">
                                            <template #icon>
                                                <EditOutlined />
                                            </template>
                                        </a-button>
                                    </a-tooltip>
                                    <a-tooltip :title="`${$t('common.add')} ${$t('message_template.variables')}`">
                                        <a-button v-if="
                                            permsArray.includes(
                                                'plantillas_calidad_create'
                                            ) || permsArray.includes('admin')
                                        " type="primary" @click="eddEdditVariables(record)" style="margin-left: 4px">
                                            <template #icon>
                                                <ClusterOutlined />
                                            </template>
                                        </a-button>
                                    </a-tooltip>
                                    <a-tooltip :title="$t('common.delete')">
                                        <a-button v-if="
                                            permsArray.includes(
                                                'plantillas_calidad_delete'
                                            ) || permsArray.includes('admin')
                                        " type="primary" danger @click="showDeleteConfirm(record.xid)"
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

            </a-tab-pane>

            <a-tab-pane :disabled="activeTabKey.value != 'variables'" key="variables">
                <template #tab>
                    <span>
                        <UnorderedListOutlined />
                        {{ $t("message_template.variables") }} {{ $t('menu.quality') }}
                    </span>
                </template>

                <!-- informacion del template al que se le va agregar la informacio de variables -->
                <a-row :gutter="16" class="text-center">
                    <a-col :xs="24" :sm="24" :md="12" :lg="12">
                        <h2>{{ $t('common.name') }}: {{ templateSeleccionado.nombre }}</h2>
                        <h4>{{ $t('common.description') }}: {{ templateSeleccionado.descripcion }}</h4>
                        <a-form-item>
                            <a-tag v-if="templateSeleccionado.activo === true" color="#4cb050">
                                {{ $t('common.active') }}
                            </a-tag>
                            <a-tag v-else color="#f5b041">
                                {{ $t('common.inactive') }}
                            </a-tag>
                        </a-form-item>

                    </a-col>
                </a-row>
                <a-form layout="vertical" ref="formRef" name="dynamic_form_nest_item" :model="dynamicValidateForm" @finish="onSubmit" >
                    <a-row :gutter="16">
                        <a-col :xs="24" :sm="24" :md="12" :lg="12">
                            <a-space v-for="(variable, index) in dynamicValidateForm.variables"
                                :key="variable.identificador"
                                style="display: flex;align-items: center; margin-bottom: 8px;">

                                <a-form-item :name="['variables', index, 'tipo']" :rules="{
                                    required: true,
                                    message: $t('lead_status.type'),
                                }">
                                    <a-select v-model:value="variable.tipo" :placeholder="$t('common.select_default_text', [
                                        $t('lead_status.type'),
                                    ])
                                        " :allowClear="true" show-search>
                                        <a-select-option key="critica" value="critica">
                                            {{ $t("message_template.critical_variable") }}
                                        </a-select-option>
                                        <a-select-option key="no_critica" value="no_critica">
                                            {{ $t("message_template.critical_not_variable") }}
                                        </a-select-option>
                                    </a-select>
                                </a-form-item>

                                <a-form-item :name="['variables', index, 'nombre']" :rules="{
                                    required: true,
                                    message: $t('common.name'),
                                }">
                                    <a-input v-model:value="variable.nombre" :placeholder="$t('common.name')" />
                                </a-form-item>

                                <a-form-item :name="['variables', index, 'descripcion']" :rules="{
                                    required: true,
                                    message: $t('common.description'),
                                }">
                                    <a-input v-model:value="variable.descripcion"
                                        :placeholder="$t('common.description')" />
                                </a-form-item>

                                <a-form-item :name="['variables', index, 'nota_maxima']" :rules="{
                                    required: true,
                                    message: $t('message_template.maximum_grade'),
                                }">
                                    <a-input-number v-if="variable.tipo != 'critica'" style="width: auto;" @change="validarSumaMaxima" :min="0" :max="100" v-model:value="variable.nota_maxima"
                                        :placeholder="$t('message_template.maximum_grade')" />
                                    <a-input v-else style="width: auto;" placeholder="N/A" disabled/>
                                </a-form-item>

                                <a-form-item>
                                    <MinusCircleOutlined @click="removeUser(variable)" />
                                </a-form-item>

                            </a-space>
                            <a-form-item class="text-right">
                                <h4 :style="{ color: suma > 100 ? 'red' : 'inherit' }">
                                    <a-tooltip :style="{ color: suma > 100 ? 'red' : 'inherit' }" :title="$t('message_template.maximum_grade_exceded')">
                                        <InfoCircleOutlined/>
                                    </a-tooltip>
                                    {{ $t('common.total') }}: {{ suma }}
                                </h4>
                            </a-form-item>
                            <a-form-item>
                                <a-button :disabled="suma >= 100" type="dashed" block @click="addUser">
                                    <PlusOutlined />
                                    {{ $t('common.add') }}
                                </a-button>
                            </a-form-item>
                            <a-form-item style="display: flex;text-align: center;">
                                <a-button :disabled="(suma > 100 || suma < 100)" type="primary" html-type="submit">
                                    <template #icon>
                                        <SaveOutlined />
                                    </template>
                                    {{ templateSeleccionado.accion == "add" ? $t("common.save") : $t("common.update") }}
                                </a-button>
                            </a-form-item>
                        </a-col>
                    </a-row>
                </a-form>
            </a-tab-pane>

        </a-tabs>
    </admin-page-table-content>
</template>
<script>
import { onMounted, reactive, ref, watch } from "vue";
import {
    PlusOutlined,
    EditOutlined,
    DeleteOutlined,
    ClusterOutlined,
    MinusCircleOutlined,
    InfoCircleOutlined,
} from "@ant-design/icons-vue";
import fields from "./fields";
import crud from "../../../common/composable/crud";
import common from "../../../common/composable/common";
import AddEdit from "./AddEdit.vue";
import AdminPageHeader from "../../../common/layouts/AdminPageHeader.vue";
import { useI18n } from "vue-i18n";
import apiAdmin from "../../../common/composable/apiAdmin";
import { notification } from "ant-design-vue";
import {useRouter} from "vue-router";


export default {
    components: {
        PlusOutlined,
        EditOutlined,
        DeleteOutlined,
        AddEdit,
        AdminPageHeader,
        InfoCircleOutlined,
        ClusterOutlined,
        MinusCircleOutlined,
    },
    setup(props, { emit }) {
        const { t } = useI18n();
        const { addEditUrl, initData, columns, filterableColumns } = fields();
        const crudVariables = crud();
        const { permsArray, formatDateTime } = common();
        const activeTabKey = ref("templates");
        const templateSeleccionado = ref([]);
        const { addEditRequestAdmin } = apiAdmin();
        const router = useRouter();
        const suma = ref(0);

        const validarSumaMaxima = () => {
            suma.value = 0;
            suma.value = dynamicValidateForm.variables
                .reduce((acc, item) => acc + Number(item.nota_maxima || 0), 0);
        };
        

        onMounted(() => {
            getDataTemplates();
        });
        
        const getDataTemplates = () => {
            crudVariables.tableUrl.value = {
                url: `plantillas-calidad?fields=id,xid,nombre,descripcion,activo,created_at,variables{id,xid,tipo,nombre,descripcion,nota_maxima}&order=id asc&limit=1000`,
            };
            crudVariables.table.filterableColumns = filterableColumns;

            crudVariables.fetch({
                page: 1,
            });

            crudVariables.crudUrl.value = addEditUrl;
            crudVariables.langKey.value = "message_template";
            crudVariables.initData.value = { ...initData };
            crudVariables.formData.value = { ...initData };
        };

        const eddEdditVariables = (record) => {
            activeTabKey.value = "variables";
            templateSeleccionado.value = record;
            dynamicValidateForm.variables = [];
            suma.value = 0;

            if (record.variables && record.variables.length > 0) {
                templateSeleccionado.value.accion = "edit";
                record.variables.forEach((item) => {
                    suma.value += item.nota_maxima;
                    dynamicValidateForm.variables.push({
                        id: item.id,
                        plantilla_id: templateSeleccionado.value.id,
                        tipo: item.tipo,
                        nombre: item.nombre,
                        descripcion: item.descripcion,
                        nota_maxima: item.nota_maxima,
                        identificador: Date.now(),
                    });
                });
            } else {
                // Modo creación
                templateSeleccionado.value.accion = "add";
            }
        };


        // variables dinamicas
        const formRef = ref();
        const dynamicValidateForm = reactive({
            variables: [],
            variables_eliminar: [],
        });
        const removeUser = item => {
            const index = dynamicValidateForm.variables.indexOf(item);
            if (index !== -1) {
                dynamicValidateForm.variables_eliminar.push(dynamicValidateForm.variables[index]);
                suma.value -= dynamicValidateForm.variables[index]['nota_maxima'];
                dynamicValidateForm.variables.splice(index, 1);
            }
        };
        const addUser = () => {
            dynamicValidateForm.variables.push({
                plantilla_id: templateSeleccionado.value.id,
                tipo: 'no_critica',
                nombre: '',
                descripcion: '',
                nota_maxima: 0,
                identificador: Date.now(),
            });
        };
        const onSubmit = () => {
            if(suma.value === 100){
                dynamicValidateForm.accion = templateSeleccionado.value.accion;
                addEditRequestAdmin({
                    url: 'variables/save',
                    data: dynamicValidateForm,
                    successMessage: props.successMessage,
                    success: (res) => {
                        if(res){
                            getDataTemplates();
                            activeTabKey.value = "templates";
                            router.push({
                                name: "admin.templates.index",
                            });
                            notification.success({message: t(`common.success`),description: t(`message_template.updated_variables`)});
                        }else{
                            console.error('Error al guardar/actualizar variables')
                        }
                    },
                });
            }else{
                notification.warning({message: t(`common.information`),description: t(`message_template.maximum_grade_exceded`)});
            }
        };

        return {
            validarSumaMaxima,
            formRef,
            dynamicValidateForm,
            removeUser,
            addUser,
            onSubmit,
            templateSeleccionado,
            suma,
            ///////
            eddEdditVariables,
            activeTabKey,
            formatDateTime,
            columns,
            ...crudVariables,
            filterableColumns,
            permsArray,
        };
    },
};
</script>