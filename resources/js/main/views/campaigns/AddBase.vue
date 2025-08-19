<template>
    <a-drawer :title="$t('bases.bases')" :width="drawerWidth" :open="visible" :body-style="{ paddingBottom: '80px' }"
        :footer-style="{ textAlign: 'right' }" :maskClosable="false" @close="onClose">
        <a-form layout="vertical">
            <a-steps :current="currentStep" type="navigation">
                <a-step :title="$t('bases.bases_history')" :description="$t('bases.bases_history')">
                    <template #icon>
                        <FileTextOutlined />
                    </template>
                </a-step>
                <a-step :title="$t('campaign.import_leads')" :description="$t('campaign.import_leads_description')" />
            </a-steps>

            <a-divider />

            <template v-if="currentStep == 0">
                <a-row :gutter="16" class="mb-10">
                    <a-col :xs="24" :sm="24" :md="24" :lg="24">
                        <a-typography-paragraph>
                            <ul>
                                <li>
                                    <a-typography-link
                                        :href="sampleFileUrl"
                                        target="_blank"
                                    >
                                        {{
                                            $t(
                                                "messages.click_here_to_download_sample_file"
                                            )
                                        }}
                                    </a-typography-link>
                                </li>
                            </ul>
                        </a-typography-paragraph>
                    </a-col>
                    <a-col v-if="camposRequerido" :xs="24" :sm="24" :md="24" :lg="24">
                        <strong>{{ camposRequerido }} : Name - Internal Code - Price - Campaign</strong>
                    </a-col>
                </a-row>
                <a-row>
                    <a-col :span="24">
                        <div class="table-responsive">
                            <a-table 
                                :row-key="record => record.id" 
                                :columns="columns" 
                                :data-source="tableClienteSerch.data"
                                :pagination="tableClienteSerch.pagination" 
                                :loading="tableClienteSerch.loading"
                                @change="handleClientSerchTableChange" bordered size="middle">
                                <template #bodyCell="{ column, record }">
                                    <template v-if="column.dataIndex === 'campaign_id'">
                                        {{  record.campaign && record.campaign.name
                                            ? record.campaign.name
                                            : ''
                                        }}
                                    </template>
                                    <template v-if="column.dataIndex === 'user_id'">
                                        {{  record.user && record.user.name
                                            ? record.user.name
                                            : ''
                                        }}
                                    </template>
                                    <template v-if="column.dataIndex === 'etapa'">
                                    {{ 
                                        record.etapa === 'nueva'
                                        ? $t('bases.new')
                                        : record.etapa === 'reproceso'
                                        ? $t('bases.reprocessing')
                                        : record.etapa === 'na'
                                        ? $t('bases.na')
                                        : '' 
                                    }}
                                    </template>

                                    <template v-if="column.dataIndex === 'created_at'">
                                        {{
                                            record.created_at && record.created_at != ""
                                                ? formatDateTime(record.created_at)
                                                : "-"
                                        }}
                                    </template>
                                    <template v-if="column.dataIndex === 'action'">
                                        <a-tooltip :title="$t('common.export_leads_active')">
                                            <a-button
                                                v-if="
                                                    permsArray.includes('export_leads') ||
                                                    permsArray.includes('admin')
                                                "
                                                type="primary"
                                                @click="
                                                    showExportLeadsConfirm(
                                                        record.x_campaign_id,
                                                        record.nombreBase,
                                                        1
                                                    )
                                                "
                                                style="width: 60px;margin-right: 5px;"
                                            >
                                                <template #icon>
                                                    <UserAddOutlined />
                                                    <CloudDownloadOutlined />
                                                </template>
                                            </a-button>
                                        </a-tooltip>
                                        <a-tooltip :title="$t('common.export_leads_inactive')">
                                            <a-button
                                                v-if="
                                                    permsArray.includes('export_leads') ||
                                                    permsArray.includes('admin')
                                                "
                                                type="primary"
                                                @click="
                                                    showExportLeadsConfirm(
                                                        record.x_campaign_id,
                                                        record.nombreBase,
                                                        2
                                                    )
                                                "
                                                style="width: 60px;"
                                            >
                                                <template #icon>
                                                    <UserDeleteOutlined />
                                                    <CloudDownloadOutlined />
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

            <template v-if="currentStep == 1">
                <ImportLeads v-if="permsArray.includes('bases_view') || permsArray.includes('admin')" 
                    acceptFormat=".csv" :allFields="selectedFormFields" @fileUploaded="leadFileUploaded"
                    @leadColumnChanged="leadColumnChanged" />
                <h4 class="text-center" v-else>{{ $t('bases.not_permission') }}</h4>
            </template>
        </a-form>
        <template #footer>
            <a-space>

                <a-button v-if="currentStep === 1" key="previous" type="primary" :loading="loading" @click="goBack">
                    <DoubleLeftOutlined />
                    {{ $t("campaign.previous_step") }}
                </a-button>

                <a-button v-if="currentStep === 0" key="next" type="primary" :loading="loading" @click="onSubmit">
                    {{ $t("campaign.next_step") }}
                    <DoubleRightOutlined />
                </a-button>

                <a-button v-if="currentStep === 1" key="finalize" type="primary" :loading="loading" @click="submitForm"
                    :disabled="addFieldsButtonStatus">
                    <template #icon>
                        <SaveOutlined />
                    </template>
                    {{ $t("common.add") }}
                </a-button>

                <a-button key="cancel" @click="onClose">
                    {{ $t("common.cancel") }} 
                </a-button>
            </a-space>
        </template>
    </a-drawer>
</template>
<script>
import { defineComponent, ref, onMounted, watch, createVNode } from "vue";
import {
    LoadingOutlined,
    SaveOutlined,
    FileTextOutlined,
    DoubleRightOutlined,
    DoubleLeftOutlined,
    CloudDownloadOutlined,
    ExclamationCircleOutlined,
    UserAddOutlined,
    UserDeleteOutlined,
} from "@ant-design/icons-vue";
import apiAdmin from "../../../common/composable/apiAdmin";
import StaffMemberAddButton from "../users/StaffAddButton.vue";
import ImportLeads from "./ImportLeads.vue";
import { useI18n } from "vue-i18n";
import { reactive } from "vue";
import common from "../../../common/composable/common";
import { Modal } from "ant-design-vue";


const tableClienteSerch = reactive({
    data: [],
    pagination: {
        current: 1,
        pageSize: 10,
        total: 0,
        showSizeChanger: false,
    },
    loading: false,
    selectedRowKeys: [],
});


export default defineComponent({
    props: [
        "formData",
        "permsArray",
        "campaign",
        "visible",
        "successMessage",
    ],
    components: {
        LoadingOutlined,
        SaveOutlined,
        FileTextOutlined,
        DoubleRightOutlined,
        DoubleLeftOutlined,
        CloudDownloadOutlined,
        ImportLeads,
        StaffMemberAddButton,
        ExclamationCircleOutlined,
        UserAddOutlined,
        UserDeleteOutlined,
    },
    setup(props, { emit }) {
        const currentStep = ref(0);
        const {
            addEditRequestAdmin,
            loading,
            rules,
        } = apiAdmin();
        const { t } = useI18n();
        // const ejemplo = "users?limit=10000"; // No se usa
        const leadFile = ref(undefined);
        const importLeadColumns = ref(undefined);
        const selectedFormFields = ref([]);
        const { formatDateTime } = common();
        const sampleFileUrl = window.config.base_sample_file;


        const addFieldsButtonStatus = ref(false);

        const columns = [
                {
                    title: t("campaign.name"),
                    dataIndex: "campaign_id",
                    key: "campaign_id",
                },
                {
                    title: t("user.user"),
                    dataIndex: "user_id",
                    key: "user_id",
                },
                {
                    title: t("lead.base_name"),
                    dataIndex: "nombreBase",
                    key: "nombreBase",
                },
                {
                    title: t("campaign.records"),
                    dataIndex: "cantidadRegistros",
                    key: "cantidadRegistros",
                },
                {
                    title: t("bases.stage"),
                    dataIndex: "etapa",
                    key: "etapa",
                },
                {
                    title: t("campaign.created_at"),
                    dataIndex: "created_at",
                    key: "created_at",
                },
                {
                    title: t("common.action"),
                    dataIndex: "action",
                },
                // {
                //     title: t("campaign.updated_at"),
                //     dataIndex: "updated_at",
                //     key: "updated_at",
                // },
            ];


        onMounted(() => {
            props.formData.etapa = 'nueva';
            if(props.campaign){
                setUrlData();
            }

        });

        const setUrlData = async () => {
            try {
                const resp = await axiosAdmin.get(`bases/${props.campaign.xid}`);
                fillSearchTable(resp);
            } catch (error) {
                if (error.response) {
                    console.error('Error 500 al obtener bases:', error.response.data);
                    this.$message.error('Ocurrió un error interno al cargar las bases.');
                } else {
                    console.error('Error de red o de configuración:', error);
                    this.$message.error('No se pudo conectar al servidor.');
                }
            }
        };

        const fillSearchTable = (data) => {
            if (data.length >= 1) {
                tableClienteSerch.data = data;
                tableClienteSerch.pagination.total = data.length;
                tableClienteSerch.selectedRowKeys = [];

            } else {
                tableClienteSerch.data = [];
                tableClienteSerch.pagination.total = 0;
                tableClienteSerch.selectedRowKeys = [];
            }
        };

        const handleClientSerchTableChange = (pagination) => {
            tableClienteSerch.pagination.current = pagination.current;
            tableClienteSerch.pagination.pageSize = pagination.pageSize;
        };      

        const onSubmit = () => {
            currentStep.value === 0 ? currentStep.value = 1 : currentStep.value = 0;
        };

        const submitForm = () => {
            var newFormData = { ...props.formData };

            let isCSV = newFormData.import_lead_fields ? { 
                data: newFormData.import_lead_fields, 
                file: newFormData.file.name, 
                campaign_id: props.campaign ? props.campaign.id : null,
                company_id: JSON.parse(localStorage.getItem('global_settings') || '{}').xid || '',
                myId:  JSON.parse(localStorage.getItem("auth_user") || "{}").id,
                etapa: newFormData.etapa ? newFormData.etapa : 'nueva',
                } 
                : false;

            if (isCSV) {
                addEditRequestAdmin({
                    url: 'leadcsv/push',
                    data: isCSV,
                    successMessage: t("messages.imported_successfully"), 
                    success: (res) => {
                        // console.log(res);
                        emit("success");
                        onClose(); 
                    },
                });
            } 
        };

        const goBack = () => {
            if (currentStep.value > 0) {
                currentStep.value = currentStep.value - 1;
                if (props.formData) props.formData.current_step = currentStep.value; // Actualizar formData si existe
            }
        };

        const onClose = () => {
            rules.value = {};
            emit("closed");
        };

        const leadFileUploaded = (file) => {
            leadFile.value = file;
            if (props.formData) props.formData.file = file;
        };

        const leadColumnChanged = (allColumns) => {
            importLeadColumns.value = allColumns;
            if (props.formData) props.formData.import_lead_fields = allColumns;

        };
        
        const handleTableChange = (pagination, filters, sorter) => {
            console.log('Table params', pagination, filters, sorter);
        };

        const formSelected = () => {
            axiosAdmin.get('columns/leads_aux')
                .then(response => {
                const payload = response.message ?? response.data ?? response;
                const allFields = Array.isArray(payload)
                    ? payload
                    : Object.values(payload);
                selectedFormFields.value = allFields;
                })
                .catch(err => {
                console.error('Error cargando columnas', err);
                selectedFormFields.value = [];
            });
        };


        watch(() => props.visible,
            (newVal) => {
                if (newVal) {
                    if (
                        props.addEditType == "edit" &&
                        props.data &&
                        props.data.campaign_users &&
                        props.data.campaign_users.length > 0
                    ) {
                        if(props.formData) { // Chequeo de props.formData
                            props.formData.user_id = props.data.campaign_users.map(function (el) {
                                return el.x_user_id;
                            });
                        }
                    } else {
                        if(props.formData) props.formData.user_id = undefined; // Chequeo
                    }
                    props.formData.etapa = 'nueva';
                    setUrlData();
                    formSelected();
                    currentStep.value = 0;
                    if(props.formData) props.formData.current_step = 0; // Chequeo
                } else {
                    // Resetear estado cuando se cierra si es necesario
                    leadFile.value = undefined;
                    importLeadColumns.value = undefined;
                    if (props.formData) { // Chequeo
                        props.formData.file = undefined;
                        props.formData.import_lead_fields = undefined;
                    }
                }
            }
        );

        const showExportLeadsConfirm = (campaignId, nombreBase,state) => {
            Modal.confirm({
                title: t("common.export_leads") + "?",
                icon: createVNode(ExclamationCircleOutlined),
                content: t(`campaign.export_leads`),
                centered: true,
                okText: t("common.yes"),
                okType: "danger",
                cancelText: t("common.no"),
                onOk() {
                    return axiosAdmin
                        .post(
                            `campaigns/export-leads/base/${campaignId}/${nombreBase}/${state}`,
                            {},
                            {
                                responseType: "blob",
                            }
                        )
                        .then((response) => {
                            // Response is a blob type object

                            // Create a temporary URL for the blob
                            const url = window.URL.createObjectURL(response);

                            // Create a temporary link element
                            const link = document.createElement("a");
                            link.href = url;

                            // Set the link attributes to trigger download
                            let estado = state === 1 ? 'Worked' : 'Not_Worked';
                            link.setAttribute("download", `${nombreBase}_${estado}.xlsx`);
                            document.body.appendChild(link);

                            // Simulate click on the link to start the download
                            link.click();

                            // Clean up by removing the temporary link and URL object
                            document.body.removeChild(link);
                            window.URL.revokeObjectURL(url);

                            notification.success({
                                message: t("common.success"),
                                description: t(`campaign.exports`),
                                placement: "bottomRight",
                            });
                        })
                        .catch(() => {});
                },
                onCancel() {},
            });
        };


        return {
            sampleFileUrl,
            showExportLeadsConfirm,
            formatDateTime,
            tableClienteSerch,
            handleClientSerchTableChange,
            columns,
            currentStep,
            loading,
            rules,
            onClose,
            onSubmit,
            goBack,
            submitForm,
            handleTableChange,
            leadFileUploaded,
            leadColumnChanged,
            selectedFormFields,
            addFieldsButtonStatus,

            drawerWidth: window.innerWidth <= 991 ? "90%" : "60%",
        };
    },
});
</script>