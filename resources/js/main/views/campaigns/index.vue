<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.campaigns`)" class="p-0" />
        </template>
        <template #breadcrumb>
            <a-breadcrumb separator="-" style="font-size: 12px">
                <a-breadcrumb-item>
                    <router-link :to="{ name: 'admin.dashboard.index' }">
                        {{ $t(`menu.dashboard`) }}
                    </router-link>
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.campaigns`) }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </AdminPageHeader>

    <admin-page-filters>
        <a-row :gutter="[16, 16]">
            <a-col :xs="24" :sm="24" :md="12" :lg="10" :xl="10">
                <a-space>
                    <template v-if="
                        permsArray.includes('campaigns_create') ||
                        permsArray.includes('admin')
                    ">
                        <a-button type="primary" @click="addItem">
                            <PlusOutlined />
                            {{ $t("campaign.add") }}
                        </a-button>
                    </template>
                    <template v-if="
                        permsArray.includes('campaigns_create') ||
                        permsArray.includes('admin')
                    ">
                        <a-button type="primary" @click="showExportarReport">
                            <CloudDownloadOutlined />
                            {{ $t("campaign.export_report") }}
                        </a-button>
                    </template>
                    <a-button v-if="
                        table.selectedRowKeys.length > 0 &&
                        (permsArray.includes('campaigns_delete') ||
                            permsArray.includes('admin'))
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
                    <a-col :xs="24" :sm="24" :md="24" :lg="24" :xl="12">
                        <a-input-group compact>
                            <a-select style="width: 35%" v-model:value="table.searchColumn"
                                :placeholder="$t('common.select_default_text', [''])">
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
            @closed="onCloseAddEdit" :permsArray="permsArray" :formData="formData" :data="viewData"
            :pageTitle="pageTitle" :successMessage="successMessage" />

        <Exportar :visible="exportVisible" :url="exportUrl" @closed="onCloseExport" :permsArray="permsArray"
            :formData="formData" :data="viewData" :pageTitle="pageTitle" :successMessage="successMessage" :campaigns_list="exportCampaigns" />

        <AddBase :addEditType="'add'" :formData="formData" :permsArray="permsArray" :visible="addBaseVisible"
            :campaign="selectedCampaign" :pageTitle="basePageTitle" :successMessage="baseSuccessMsg"
            @addEditSuccess="onBaseSuccess" @closed="closeAddBase" />

        <AddNotification :addEditType="'add'" :visible="addNotificationVisible" :campaign="selectedCampaign"
            :successMessage="baseSuccessMsg" @addEditSuccess="onNotificationSuccess" @closed="closeAddNotification" />

        <RecycleLead :visible="visible" :campaign="selectedCampaign" @close="closeCampaign" />

        <a-row v-if="
            permsArray.includes('view_completed_campaigns') ||
            permsArray.includes('admin')
        ">
            <a-col :span="24">
                <a-tabs v-model:activeKey="extraFilters.campaign_status" @change="setUrlData">
                    <a-tab-pane key="active">
                        <template #tab>
                            <span>
                                <PlayCircleOutlined />
                                {{ $t("campaign.active_campaign") }}
                            </span>
                        </template>
                    </a-tab-pane>

                    <!-- <a-tab-pane key="completed">
                        <template #tab>
                            <span>
                                <StopOutlined />
                                {{ $t("campaign.completed_campaign") }}
                            </span>
                        </template>
                    </a-tab-pane> -->
                </a-tabs>
            </a-col>
        </a-row>

        <a-row>
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
                            <template v-if="column.dataIndex === 'image'">
                                <a-image :width="32" :src="record.image_url" />
                            </template>
                            <!-- <template v-if="column.dataIndex === 'progress'">
                                <CampaignProgress :campaign="record" @success="fetch" />
                            </template> -->
                            <template v-if="column.dataIndex === 'form'">
                                {{
                                    record.x_form_id != "" &&
                                        record.form &&
                                        record.form.xid
                                        ? record.form.name
                                        : "-"
                                }}
                            </template>
                            <template v-if="column.dataIndex === 'members'">
                                <CampaignMembers :members="record.campaign_users" />
                            </template>
                            <template v-if="column.dataIndex === 'started_on'">
                                {{
                                    record.started_on && record.started_on != ""
                                        ? formatDateTime(record.started_on)
                                        : "-"
                                }}
                            </template>
                            <template v-if="column.dataIndex === 'completed_on'">
                                {{
                                    record.completed_on && record.completed_on != ""
                                        ? formatDateTime(record.completed_on)
                                        : "-"
                                }}
                            </template>
                            <template v-if="column.dataIndex === 'lead_distribution_method'">
                                <div v-if="record.lead_distribution_method === 'random'">
                                    {{ $t("campaign.random") }}
                                </div>
                                <div v-else>
                                    {{ $t("campaign.linear") }}
                                </div>
                            </template>

                            <template v-if="column.dataIndex === 'completed_by'">
                                {{
                                    record.x_completed_by &&
                                        record.completed_by &&
                                        record.completed_by.name
                                        ? record.completed_by.name
                                        : "-"
                                }}
                            </template>
                            <template v-if="column.dataIndex === 'last_actioner'">
                                {{
                                    record.last_actioner && record.last_actioner.name
                                        ? record.last_actioner.name
                                        : "-"
                                }}
                            </template>
                            <template v-if="column.dataIndex === 'active'">
                                <a-tag color="success" v-if="record.active == 1">
                                    {{ $t("common.active") }}
                                </a-tag>
                                <a-tag color="error" v-if="record.active == 0">
                                    {{ $t("common.inactive") }}
                                </a-tag>
                            </template>
                            <template v-if="column.dataIndex === 'action'">
                                <a-space>
                                    <!-- <a-tooltip :title="$t('common.export_leads')">
                                        <a-button
                                            v-if="
                                                permsArray.includes('export_leads') ||
                                                permsArray.includes('admin')
                                            "
                                            type="primary"
                                            @click="
                                                showExportLeadsConfirm(
                                                    record.xid,
                                                    record.name
                                                )
                                            "
                                        >
                                            <template #icon>
                                                <CloudDownloadOutlined />
                                            </template>
                    </a-button>
                    </a-tooltip> -->
                                    <AddLead :campaign="record" btnType="primary" @success="setUrlData">
                                        <template #icon>
                                            <UserAddOutlined />
                                        </template>
                                    </AddLead>
                                    <a-tooltip :title="$t('common.edit')">
                                        <a-button v-if="
                                            permsArray.includes('campaigns_edit') ||
                                            permsArray.includes('admin')
                                        " type="primary" @click="editItem(record)">
                                            <template #icon>
                                                <EditOutlined />
                                            </template>
                                        </a-button>
                                    </a-tooltip>
                                    <a-tooltip :title="`${$t('common.add')} ${$t('bases.base')}`">
                                        <a-button
                                            v-if="permsArray.includes('bases_view') || permsArray.includes('admin')"
                                            type="primary" @click="showAddBase(record)">
                                            <template #icon>
                                                <FileAddOutlined />
                                            </template>
                                        </a-button>
                                    </a-tooltip>
                                    <a-tooltip :title="$t('campaign.lead_distribution')">
                                        <a-button v-if="
                                            permsArray.includes(
                                                'campaigns_recycle'
                                            ) || permsArray.includes('admin')
                                        " type="primary" @click="getAllNonCampaign(record)"
                                            style="margin-left: 4px">
                                            <template #icon>
                                                <RadarChartOutlined />
                                            </template>
                                        </a-button>
                                    </a-tooltip>
                                    <!-- notificaciones -->
                                    <a-tooltip :title="$t('common.add_notification')">
                                        <a-button
                                            v-if="permsArray.includes('campaigns_edit') || permsArray.includes('admin')"
                                            type="primary" @click="showAddNotification(record)">
                                            <template #icon>
                                                <NotificationOutlined />
                                            </template>
                                        </a-button>
                                    </a-tooltip>
                                    <!-- -------------- -->
                                    <a-tooltip :title="$t('common.delete')">
                                        <a-button v-if="
                                            (permsArray.includes(
                                                'campaigns_delete'
                                            ) ||
                                                permsArray.includes('admin')) &&
                                            (!record.children ||
                                                record.children.length == 0)
                                        " type="primary" danger @click="showDeleteConfirm(record.xid)">
                                            <template #icon>
                                                <DeleteOutlined />
                                            </template>
                                        </a-button>
                                    </a-tooltip>
                                </a-space>
                            </template>
                        </template>
                    </a-table>
                </div>
            </a-col>
        </a-row>
    </admin-page-table-content>
</template>

<script>
import { onMounted, ref, createVNode, computed } from "vue";
import {
    EditOutlined,
    FileAddOutlined,
    NotificationOutlined,
    DeleteOutlined,
    PlayCircleOutlined,
    StopOutlined,
    ExportOutlined,
    ExclamationCircleOutlined,
    CloudDownloadOutlined,
    PlusOutlined,
    SwapOutlined,
    RadarChartOutlined,
    UserAddOutlined,
} from "@ant-design/icons-vue";
import { Modal, notification } from "ant-design-vue";
import crud from "../../../common/composable/crud";
import common from "../../../common/composable/common";
import fields from "./fields";
import AddEdit from "./AddEdit.vue";
import Exportar from "./Exportar.vue";
import AddBase from "./AddBase.vue";
import AddNotification from "./AddNotification.vue";
import AdminPageHeader from "../../../common/layouts/AdminPageHeader.vue";
import AddLead from "./AddLead.vue";
import CampaignMembers from "./CampaignMembers.vue";
// import CampaignProgress from "./CampaignProgress.vue";
import { useI18n } from "vue-i18n";
import RecycleLead from "./RecycleLead.vue";

export default {
    components: {
        RadarChartOutlined,
        UserAddOutlined,
        EditOutlined,
        FileAddOutlined,
        NotificationOutlined,
        DeleteOutlined,
        PlayCircleOutlined,
        StopOutlined,
        ExportOutlined,
        ExclamationCircleOutlined,
        CloudDownloadOutlined,
        PlusOutlined,
        SwapOutlined,
        RecycleLead,
        AddEdit,
        Exportar,
        AddBase,
        AddNotification,
        AdminPageHeader,
        AddLead,
        CampaignMembers,
        // CampaignProgress,
    },
    setup() {
        const { permsArray, appSetting, formatDateTime } = common();
        const {
            url,
            addEditUrl,
            initData,
            columns,
            filterableColumns,
            hashableColumns,
            extraFilters,
        } = fields();
        const crudVariables = crud();
        const { t } = useI18n();
        const visible = ref(false);
        const campaignData = ref();

        const getAllNonCampaign = (items) => {
            try {
                if (!items) {
                    throw new Error('Campaign data is required');
                }
                selectedCampaign.value = items;
                visible.value = true;
                campaignData.value = items.xid;
            } catch (error) {
                console.error('Error in getAllNonCampaign:', error);
                notification.error({
                    message: t('common.error'),
                    description: t('campaign.error_loading_distribution'),
                    placement: 'bottomRight'
                });
            }
        };

        onMounted(() => {
            setUrlData();

            crudVariables.crudUrl.value = addEditUrl;
            crudVariables.langKey.value = "campaign";
            crudVariables.initData.value = { ...initData };
            crudVariables.formData.value = { ...initData };
            crudVariables.hashableColumns.value = [...hashableColumns];
        });

        const setUrlData = () => {
            crudVariables.tableUrl.value = {
                url,
                extraFilters,
            };

            crudVariables.table.filterableColumns = filterableColumns;

            crudVariables.fetch({
                page: 1,
            });
        };
        const showExportLeadsConfirm = (campaignId, campaignName) => {
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
                            `campaigns/export-leads/${campaignId}`,
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
                            link.setAttribute("download", `${campaignName}.xlsx`);
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
                        .catch(() => { });
                },
                onCancel() { },
            });
        };

        const closeCampaign = () => {
            visible.value = false;
        };

        const addBaseVisible = ref(false)
        const addNotificationVisible = ref(false)
        const selectedCampaign = ref(null)
        const baseFormData = ref({})
        const baseUrl = ref('')
        const basePageTitle = ref('')
        const baseSuccessMsg = ref('')

        function showAddBase(record) {
            selectedCampaign.value = record
            baseFormData.value = { ...record }
            baseUrl.value = `${addEditUrl}/${record.xid}/bases`
            basePageTitle.value = `${t('common.add')} ${t('bases.base')}`
            baseSuccessMsg.value = t('bases.base_added_success')
            addBaseVisible.value = true
        }

        function showAddNotification(record) {
            selectedCampaign.value = record
            baseSuccessMsg.value = t('common.created')
            addNotificationVisible.value = true
        }

        function closeAddBase() {
            addBaseVisible.value = false
        }

        function closeAddNotification() {
            addNotificationVisible.value = false
        }

        function onBaseSuccess() {
            addBaseVisible.value = false;
            addNotificationVisible.value = false;
            fetch();
        }

        const exportVisible = ref(false)

        function showExportarReport() {
            exportVisible.value = true
        }

        function onCloseExport() {
            exportVisible.value = false
        }

        const exportCampaigns = computed(() => {
            const rows = crudVariables.table?.data ?? [];
            return rows.map(r => ({
                name: r?.name ?? "",
                id: r?.id ?? null,
                xid: r?.xid ?? null,
            }));
        });


        return {
            exportCampaigns,
            exportVisible,
            showExportarReport,
            onCloseExport,
            closeAddNotification,
            addBaseVisible,
            addNotificationVisible,
            selectedCampaign,
            baseFormData,
            baseUrl,
            basePageTitle,
            baseSuccessMsg,
            showAddBase,
            showAddNotification,
            closeAddBase,
            onBaseSuccess,
            permsArray,
            formatDateTime,
            appSetting,
            columns,
            ...crudVariables,
            filterableColumns,
            extraFilters,
            setUrlData,
            closeCampaign,
            visible,
            campaignData,
            getAllNonCampaign,
            showExportLeadsConfirm,
        };
    },
};
</script>
