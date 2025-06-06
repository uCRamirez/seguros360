<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.leads`)" class="p-0">
                <template v-if="permsArray.includes('campaigns_view_all') ||
                    permsArray.includes('admin')
                    " #extra>
                    <a-space>
                        <a-typography-text strong>
                            {{ $t("campaign.view_all_campaigns") }}
                        </a-typography-text>
                        <a-switch v-model:checked="viewType" @change="viewTypeChanged" unCheckedValue="self"
                            checkedValue="all" size="small" />
                    </a-space>
                </template>
            </a-page-header>
        </template>
        <template #breadcrumb>
            <a-breadcrumb separator="-" style="font-size: 12px">
                <a-breadcrumb-item>
                    <router-link :to="{ name: 'admin.dashboard.index' }">
                        {{ $t(`menu.dashboard`) }}
                    </router-link>
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.leads_calls`) }}
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.leads`) }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </AdminPageHeader>

    <admin-page-table-content>
        <a-card class="page-content-container mt-20 mb-20">
            <a-row>
                <a-col :span="24">
                    <a-tabs v-model:activeKey="extraFilters.campaign_status" @change="campaignTypeChanged">
                        <a-tab-pane key="active">
                            <template #tab>
                                <span>
                                    <PlayCircleOutlined />
                                    {{ $t("campaign.active_campaign") }}
                                </span>
                            </template>
                        </a-tab-pane>

                        <a-tab-pane v-if="
                            permsArray.includes('view_completed_campaigns') ||
                            permsArray.includes('admin')
                        " key="completed">
                            <template #tab>
                                <span>
                                    <StopOutlined />
                                    {{ $t("campaign.completed_campaign") }}
                                </span>
                            </template>
                        </a-tab-pane>
                    </a-tabs>
                </a-col>
            </a-row>

            <a-row :gutter="[24, 24]">
                <a-col :xs="24" :sm="24" :md="6" :lg="6" :xl="6" style="border-right: 1px solid #f0f0f0">
                    <div class="left-lead-campaigns">
                        <perfect-scrollbar :options="{
                            wheelSpeed: 1,
                            swipeEasing: true,
                            suppressScrollX: true,
                        }">
                            <a-row>
                                <a-col :span="24">
                                    <a-form layout="vertical">
                                        <a-form-item :label="$t('lead.campaign')" name="campaign">
                                            <a-select v-model:value="filters.campaign_id" :placeholder="$t('common.select_default_text', [
                                                $t('lead.campaign'),
                                            ])
                                                " :allowClear="true" style="width: 100%" optionFilterProp="title"
                                                show-search @change="campaignChanged">
                                                <a-select-option v-for="allCampaign in allCampaigns"
                                                    :key="allCampaign.xid" :title="allCampaign.name"
                                                    :value="allCampaign.xid" :campaign="allCampaign">
                                                    {{ allCampaign.name }}
                                                </a-select-option>
                                            </a-select>
                                        </a-form-item>
                                    </a-form>
                                </a-col>
                            </a-row>
                            <a-row v-if="campaignStats">
                                <a-col :span="24">
                                    <StateWidget :bodyStyle="widgetBodyStyle">
                                        <template #image>
                                            <CopyrightCircleOutlined style="color: #fff; font-size: 24px" />
                                        </template>
                                        <template #description>
                                            <h2>
                                                {{ campaignStats.total_active_campaign }}
                                            </h2>
                                            <p>
                                                {{ $t("campaign.active_campaign") }}
                                            </p>
                                        </template>
                                    </StateWidget>
                                </a-col>

                                <a-col :span="24">
                                    <StateWidget bgColor="#ffa39e" :bodyStyle="widgetBodyStyle">
                                        <template #image>
                                            <LineChartOutlined style="color: #fff; font-size: 24px" />
                                        </template>
                                        <template #description>
                                            <h2>
                                                {{
                                                    campaignStats.total_completed_campaign
                                                }}
                                            </h2>
                                            <p>
                                                {{ $t("campaign.completed_campaign") }}
                                            </p>
                                        </template>
                                    </StateWidget>
                                </a-col>

                                <a-col :span="24">
                                    <StateWidget bgColor="#d46b08" :bodyStyle="widgetBodyStyle">
                                        <template #image>
                                            <ApartmentOutlined style="color: #fff; font-size: 24px" />
                                        </template>
                                        <template #description>
                                            <h2>
                                                {{ campaignStats.total_leads }}
                                            </h2>
                                            <p>
                                                {{ $t("campaign.total_leads") }}
                                            </p>
                                        </template>
                                    </StateWidget>
                                </a-col>

                                <a-col :span="24">
                                    <StateWidget bgColor="#389e0d" :bodyStyle="widgetBodyStyle">
                                        <template #image>
                                            <MobileOutlined style="color: #fff; font-size: 24px" />
                                        </template>
                                        <template #description>
                                            <h2>
                                                {{ campaignStats.call_made }}
                                            </h2>
                                            <p>
                                                {{ $t("campaign.managed_leads") }}
                                            </p>
                                        </template>
                                    </StateWidget>
                                </a-col>

                                <a-col :span="24">
                                    <StateWidget bgColor="#656592" :bodyStyle="widgetBodyStyle">
                                        <template #image>
                                            <OrderedListOutlined style="color: #fff; font-size: 24px" />
                                        </template>
                                        <template #description>
                                            <h2>
                                                {{ campaignStats.call_not_made }}
                                            </h2>
                                            <p>
                                                {{ $t("campaign.not_managed_leads") }}
                                            </p>
                                        </template>
                                    </StateWidget>
                                </a-col>

                                <a-col :span="24">
                                    <StateWidget bgColor="#08979c" :bodyStyle="widgetBodyStyle">
                                        <template #image>
                                            <ClockCircleOutlined style="color: #fff; font-size: 24px" />
                                        </template>
                                        <template #description>
                                            <h2>
                                                {{
                                                    formatTimeDuration(
                                                        campaignStats.total_duration
                                                    )
                                                }}
                                            </h2>
                                            <p>{{ $t("campaign.total_duration") }}</p>
                                        </template>
                                    </StateWidget>
                                </a-col>
                            </a-row>
                        </perfect-scrollbar>
                    </div>
                </a-col>

                <a-col :xs="24" :sm="24" :md="18" :lg="18" :xl="18">
                    <div style="display: flex; justify-content: flex-end">
                        <a-button type="primary" @click="addMultipleUsers(table.data)" v-if="
                            table.selectedRowKeys.length > 0 &&
                            filters.campaign_id &&
                            extraFilters.started == 'not_started' &&
                            (permsArray.includes('leads_view_all') ||
                                permsArray.includes('admin'))
                        ">
                            <template #icon>
                                <UsergroupAddOutlined />
                            </template>
                            {{ $t("lead.assign_to") }}
                        </a-button>
                    </div>
                    <a-row>
                        <a-col :span="24">
                            <a-tabs v-model:activeKey="extraFilters.started" @change="setUrlData">
                                <a-tab-pane key="started">
                                    <template #tab>
                                        <span>
                                            <AreaChartOutlined />
                                            {{ $t("lead.started") }}
                                        </span>
                                    </template>
                                </a-tab-pane>

                                <a-tab-pane key="not_started">
                                    <template #tab>
                                        <span>
                                            <HistoryOutlined />
                                            {{ $t("lead.not_started") }}
                                        </span>
                                    </template>
                                </a-tab-pane>
                            </a-tabs>
                        </a-col>
                    </a-row>

                    <a-row :gutter="16">
                        <a-col :xs="24" :sm="24" :md="4" :lg="4" :xl="4">
                            <a-input-search style="width: 100%" v-model:value="table.searchString" :placeholder="$t('common.select_default_text', [
                                $t('common.information'),
                            ])
                                " show-search @change="onTableSearch" @search="onTableSearch"
                                :loading="table.filterLoading" />
                        </a-col>
                        <a-col v-if="extraFilters.started == 'started'" :xs="24" :sm="24" :md="5" :lg="5" :xl="5">
                            <a-select v-model:value="filters.lead_status_id" :placeholder="$t('common.select_default_text', [
                                $t('lead.lead_status'),
                            ])
                                " :allowClear="true" style="width: 100%" @change="setUrlData">
                                <a-select-option v-for="allLeadStatu in allLeadStatus.filter(s => s.type === 'lead_status')" :key="allLeadStatu.xid"
                                    :value="allLeadStatu.xid">
                                    {{ allLeadStatu.name }}
                                </a-select-option>
                            </a-select>
                        </a-col>

                        <a-col :xs="24" :sm="24" :md="5" :lg="5" :xl="5">
                            <a-select v-if="
                                permsArray.includes('leads_view_all') ||
                                permsArray.includes('admin')
                            " v-model:value="extraFilters.assign_to" show-search style="width: 100%" :placeholder="$t('common.select_default_text', [
                                    $t('lead.assign_to'),
                                ])
                                    " :allowClear="true" @change="refetchAllData()">
                                <a-select-option v-for="campaignUser in campaignUsers" :key="campaignUser.xid"
                                    :title="campaignUser.name" :label="campaignUser.name">
                                    {{ campaignUser.name }}
                                </a-select-option>
                            </a-select>
                        </a-col>

                        <template v-if="campaignFormFields.length != 0">
                            <a-col :xs="24" :sm="24" :md="5" :lg="5" :xl="5">
                                <a-select v-model:value="searchFieldName" :placeholder="$t('common.select_default_text', [
                                    $t('lead.search_by'),
                                ])
                                    " :allowClear="true" style="width: 100%" optionFilterProp="title" show-search
                                    @change="searchFieldChanged" :disabled="campaignFormFields.length == 0">
                                    <a-select-option v-for="campaignFormField in campaignFormFields"
                                        :key="campaignFormField.id" :title="campaignFormField.name"
                                        :value="campaignFormField.id" :formField="campaignFormField">
                                        {{ campaignFormField.name }}
                                    </a-select-option>
                                </a-select>
                            </a-col>
                            <a-col :xs="24" :sm="24" :md="5" :lg="5" :xl="5">
                                <a-input v-model:value="extraFilters.lead_field_value" :placeholder="$t('common.placeholder_default_text', [
                                    $t('lead.search_value'),
                                ])
                                    " v-on:keyup="setUrlData" :disabled="searchFieldName == undefined" />
                            </a-col>
                        </template>
                    </a-row>
                    <!-- TABLA DE DETALLE DE LEADS -->
                    <a-row class="mt-20">
                        <a-col :span="24">
                            <div class="table-responsive" v-if="columns && columns.length > 0">
                                <a-table :row-selection="{
                                    selectedRowKeys: table.selectedRowKeys,
                                    onChange: onRowSelectChange,
                                    getCheckboxProps: (record) => ({
                                        disabled: false,
                                        name: record.xid,
                                    }),
                                }" :columns="columns" :row-key="(record) => record.xid" :data-source="table.data"
                                    :pagination="table.pagination" :loading="table.loading" @change="handleTableChange">
                                    <!--Tabla de leads aqui-->
                                    <template #bodyCell="{ column, record }">
                                        <template v-if="column.dataIndex === 'reference_number'">
                                            <a-button type="link" class="p-0" @click="showViewDrawer(record)">
                                                {{
                                                    record.reference_number != "" &&
                                                        record.reference_number != undefined
                                                        ? record.reference_number
                                                        : "---"
                                                }}
                                            </a-button>
                                        </template>

                                        <template v-if="column.dataIndex === 'campaign'">
                                            {{ record.campaign.name }}
                                        </template>
                                        <template v-if="column.dataIndex === 'assign_to'">
                                            {{
                                                record.assign_users
                                                    ? record.assign_users.name
                                                    : "-"
                                            }}
                                        </template>
                                        <template v-if="column.dataIndex === 'lead_status' && record.lead_status">
                                            <a-tag :color="record.lead_status.color">
                                                {{ record.lead_status.name }}
                                            </a-tag>
                                        </template>

                                        <template v-for="allFormFieldName in allFormFieldNames"
                                            :key="allFormFieldName.xid">
                                            <template v-if="
                                                column.dataIndex ===
                                                convertStringToKey(
                                                    allFormFieldName.field_name
                                                )
                                            ">
                                                {{
                                                    findFieldValue(
                                                        allFormFieldName.similar_field_names,
                                                        record.lead_data
                                                    )
                                                }}
                                            </template>
                                        </template>
                                        <template v-if="column.dataIndex === 'action'">
                                            <a-space v-if="
                                                isLeadActionValid(record.campaign.xid)
                                            ">
                                                <a-tooltip v-if="
                                                    extraFilters.started ==
                                                    'not_started' &&
                                                    (permsArray.includes(
                                                        'leads_view_all'
                                                    ) ||
                                                        permsArray.includes('admin'))
                                                " :title="$t('lead.assign_users')">
                                                    <a-button type="primary" @click="assignUserOption(record)">
                                                        <template #icon>
                                                            <UsergroupAddOutlined />
                                                        </template>
                                                    </a-button>
                                                </a-tooltip>

                                                <a-tooltip v-if="
                                                    record.campaign &&
                                                    record.x_assign_to == user.xid &&
                                                    record.campaign.status !=
                                                    'completed'
                                                " :title="$t('lead.go_resume_call')">
                                                    <a-button type="primary" @click="goAndResumeLead(record)">
                                                        <template #icon>
                                                            <PlayCircleOutlined /><!--Boton para trabajar con el lead-->
                                                        </template>
                                                    </a-button>
                                                </a-tooltip>

                                                <a-tooltip :title="$t('lead.delete_lead')" v-if="
                                                    (permsArray.includes(
                                                        'leads_delete'
                                                    ) ||
                                                        permsArray.includes(
                                                            'admin'
                                                        )) &&
                                                    !record.started
                                                ">
                                                    <a-button type="primary" @click="
                                                        showDeleteConfirm(record.xid)
                                                        ">
                                                        <template #icon>
                                                            <DeleteOutlined />
                                                        </template>
                                                    </a-button>
                                                </a-tooltip>

                                                <a-tooltip v-if="!record.x_assign_to" :title="$t('lead.assign_to')">
                                                    <a-button type="primary" @click="assignUserOption(record)">
                                                        <template #icon>
                                                            <UserAddOutlined />
                                                        </template>
                                                    </a-button>
                                                </a-tooltip>

                                            </a-space>
                                            <span v-else>-</span>
                                        </template>
                                    </template>
                                </a-table>
                            </div>
                        </a-col>
                    </a-row>
                </a-col>
            </a-row>
        </a-card>
    </admin-page-table-content>

    <!-- Global Compaonent -->
    <view-lead-details :visible="isViewDrawerVisible" :lead="viewDrawerData" @close="hideViewDrawer"
        @setNotesCount="setUrlData" />

    <ViewLeadUphone :visible="visibleLeadUphone" :data="viewLeadUphone" @close="closed"
        :pageTitle="$t('lead.lead_uphone_details')" />

    <AssignUsers :visible="visibleUserOption" @close="closeUserOption" :data="assignUserData"
        :pageTitle="$t('lead.assign_users')" @setAssignUser="setUrlData" />

    <AssignMultipleUser :visible="visibleMultipleUser" :data="multipleUserData" :keys="table.selectedRowKeys"
        @close="closeMultipleUser" @setMultiUser="setUrlData()" :pageTitle="$t('lead.assign_users')" />
</template>

<script>
import { onMounted, ref, createVNode } from "vue";
import {
    PlusOutlined,
    EditOutlined,
    DeleteOutlined,
    UserAddOutlined,
    SearchOutlined,
    LineChartOutlined,
    EyeOutlined,
    PlayCircleOutlined,
    DownOutlined,
    AreaChartOutlined,
    HistoryOutlined,
    ExclamationCircleOutlined,
    StopOutlined,
    CopyrightCircleOutlined,
    ApartmentOutlined,
    MobileOutlined,
    ClockCircleOutlined,
    UsergroupAddOutlined,
    OrderedListOutlined,
} from "@ant-design/icons-vue";
import { Modal } from "ant-design-vue";
import { forEach, find, map } from "lodash-es";
import { useI18n } from "vue-i18n";
import { useRouter } from "vue-router";
import crud from "../../../../common/composable/crud";
import common from "../../../../common/composable/common";
import viewDrawer from "../../../../common/composable/viewDrawer";
import fields from "./fields";
import AdminPageHeader from "../../../../common/layouts/AdminPageHeader.vue";
import UpdateStatus from "./UpdateStatus.vue";
import StateWidget from "../../../../common/components/common/card/StateWidget.vue";
import ViewLeadUphone from "./ViewLeadUphone.vue";
import AssignUsers from "./AssignUsers.vue";
import AssignMultipleUser from "./AssignMultipleUser.vue";

export default {
    components: {
        PlusOutlined,
        EditOutlined,
        DeleteOutlined,
        UserAddOutlined,
        SearchOutlined,
        LineChartOutlined,
        EyeOutlined,
        PlayCircleOutlined,
        DownOutlined,
        AreaChartOutlined,
        HistoryOutlined,
        ExclamationCircleOutlined,
        StopOutlined,
        CopyrightCircleOutlined,
        ApartmentOutlined,
        MobileOutlined,
        ClockCircleOutlined,
        UsergroupAddOutlined,
        OrderedListOutlined,
        ViewLeadUphone,
        AdminPageHeader,
        UpdateStatus,
        StateWidget,
        AssignUsers,
        AssignMultipleUser,
    },
    setup() {
        const {
            permsArray,
            appSetting,
            convertStringToKey,
            formatTimeDuration,
            getCampaignUrl,
            getCampaignStatsUrl,
            user,
            formFolders, // Here
        } = common();
        const leadDrawer = viewDrawer();
        const router = useRouter();
        const {
            url,
            addEditUrl,
            initData,
            columns,
            hashableColumns,
            filterableColumns,
            allFormFieldNames,
            allCampaigns,
            campaignStats,
            userCampaigns,
            activeCampaignType,
            viewType,
        } = fields();
        const crudVariables = crud();
        const filters = ref({
            campaign_id: undefined,
            lead_status_id: undefined,
        });
        const campaignFormFields = ref([]);
        const extraFilters = ref({
            lead_field_name: undefined,
            lead_field_value: "",
            started: "started",
            campaign_status: "active",
            assign_to: undefined,
        });
        const searchFieldName = ref(undefined);
        const { t } = useI18n();
        const widgetBodyStyle = { marginLeft: "0px", marginRight: "0px" };
        const allLeadStatus = ref([]);
        const visibleLeadUphone = ref(false);
        const viewLeadUphone = ref({});
        const visibleUserOption = ref(false);
        const assignUserData = ref({});
        const allUsers = ref([]);
        const campaignUsers = ref([]);
        const campaignUserUrl = "users?limit=10000";
        const visibleMultipleUser = ref(false);
        const multipleUserData = ref({});
        const addUserSelect = ref([]);

        const viewLeadUphoneData = (item) => {
            visibleLeadUphone.value = true;
            viewLeadUphone.value = item;
        };

        const assignUserOption = (item) => {
            visibleUserOption.value = true;
            assignUserData.value = item;
        };

        const addMultipleUsers = (item) => {
            visibleMultipleUser.value = true;
            multipleUserData.value = item;
        };

        onMounted(() => {
            axiosAdmin.get("lead-status?fields=id,xid,name,color,type").then((response) => {
                allLeadStatus.value = response.data;
            });

            const campaignUsersPromise = axiosAdmin.get(campaignUserUrl);

            Promise.all([campaignUsersPromise]).then(([campaignUsersResponse]) => {
                allUsers.value = [...campaignUsersResponse.data];
                campaignUsers.value = campaignUsersResponse.data;
            });

            setUrlData();

            crudVariables.crudUrl.value = addEditUrl;
            crudVariables.langKey.value = "lead";
            crudVariables.initData.value = { ...initData };
            crudVariables.formData.value = { ...initData };
            crudVariables.hashableColumns.value = [...hashableColumns];
        });

        const closed = () => {
            visibleLeadUphone.value = false;
        };

        const closeUserOption = () => {
            visibleUserOption.value = false;
        };

        const closeMultipleUser = () => {
            visibleMultipleUser.value = false;
        };

        const setUrlData = () => {
            crudVariables.tableUrl.value = {
                url,
                filters,
                extraFilters,
            };

            crudVariables.table.filterableColumns = filterableColumns;

            // Setting page number to 1
            crudVariables.table.pagination = {
                ...crudVariables.table.pagination,
                current: 1,
            };
            crudVariables.fetch({
                page: 1,
            });
        };

        const findFieldValue = (allowedFieldName, leadDatas) => {
            let resultString = "";
            // console.log(leadDatas);
            forEach(leadDatas, (leadData) => {
                if (allowedFieldName.includes(leadData.field_name)) {
                    resultString = leadData.field_value;
                }
            });
            return resultString;
        };

        const goAndResumeLead = (record) => {
            Modal.confirm({
                title: t("common.are_you_sure") + "?",
                icon: createVNode(ExclamationCircleOutlined),
                content: t("lead.are_you_want_to_resume_this_lead"),
                centered: true,
                okText: t("common.yes"),
                okType: "danger",
                cancelText: t("common.no"),
                onOk() {
                    router.push({
                        // name: "admin.call_manager.take_action",
                        name: "admin.formsUCB.Correspondencia",
                        params: {
                            id: record.xid,
                        },
                    });
                },
                onCancel() { },
            });
        };

        const campaignChanged = (value, options) => {
            if (
                value != undefined &&
                options &&
                options.campaign &&
                options.campaign.campaign_users
            ) {
                campaignUsers.value = map(options.campaign.campaign_users, "user");
            } else {
                campaignUsers.value = allUsers.value;
            }

            campaignFormFields.value =
                options &&
                    options.campaign &&
                    options.campaign.form &&
                    options.campaign.form.form_fields
                    ? options.campaign.form.form_fields
                    : [];

            extraFilters.value = {
                ...extraFilters.value,
                lead_field_name: undefined,
                lead_field_value: "",
                started: "started",
            };
            searchFieldName.value = undefined;

            refetchAllData();
        };

        const refetchAllData = () => {
            fetchCampaignStats().then((campaignStatsResponse) => {
                campaignStats.value = campaignStatsResponse.data;

                setUrlData();
            });
        };

        const searchFieldChanged = (value, options) => {
            extraFilters.value = {
                ...extraFilters.value,
                lead_field_name:
                    options && options.formField && options.formField.name
                        ? options.formField.name
                        : undefined,
                lead_field_value: "",
            };
            setUrlData();
        };

        const campaignTypeChanged = (value) => {
            filters.value = {
                ...filters.value,
                campaign_id: undefined,
                lead_status_id: undefined,
            };

            extraFilters.value = {
                ...extraFilters.value,
                lead_field_name: undefined,
                lead_field_value: "",
                started: "started",
            };

            const campaignsUrl = getCampaignUrl(
                extraFilters.value.campaign_status,
                viewType.value
            );

            const campaignsPromise = axiosAdmin.get(campaignsUrl);
            const campaignStatsPromise = fetchCampaignStats();

            Promise.all([campaignsPromise, campaignStatsPromise]).then(
                ([campaignsResponse, campaignStatsResponse]) => {
                    allCampaigns.value = campaignsResponse.data;
                    campaignStats.value = campaignStatsResponse.data;

                    setUrlData();
                }
            );
        };

        const fetchCampaignStats = () => {
            const campaignStatsUrl = getCampaignStatsUrl(
                extraFilters.value.campaign_status,
                filters.value.campaign_id,
                extraFilters.value.assign_to
            );

            return axiosAdmin.get(campaignStatsUrl);
        };

        const viewTypeChanged = () => {
            const campaignsUrl = getCampaignUrl(
                extraFilters.value.campaign_status,
                viewType.value
            );

            const campaignsPromise = axiosAdmin.get(campaignsUrl);

            Promise.all([campaignsPromise]).then(
                ([campaignsResponse, campaignStatsResponse]) => {
                    allCampaigns.value = campaignsResponse.data;

                    setUrlData();
                }
            );
        };

        const isLeadActionValid = (leadCampaignId) => {
            if (
                permsArray.value.includes("leads_view_all") ||
                permsArray.value.includes("admin")
            ) {
                return true;
            } else if (find(userCampaigns.value, { x_campaign_id: leadCampaignId })) {
                return true;
            } else {
                return false;
            }
        };

        return {
            permsArray,
            appSetting,
            columns,
            allFormFieldNames,
            ...crudVariables,
            ...leadDrawer,
            filterableColumns,
            convertStringToKey,
            visibleUserOption,
            closeUserOption,
            assignUserOption,
            filters,
            extraFilters,
            setUrlData,
            findFieldValue,
            allCampaigns,
            campaignFormFields,
            searchFieldName,
            goAndResumeLead,
            assignUserData,
            activeCampaignType,
            widgetBodyStyle,
            campaignChanged,
            searchFieldChanged,
            campaignTypeChanged,
            campaignStats,
            formatTimeDuration,
            isLeadActionValid,
            viewType,
            viewTypeChanged,
            campaignUsers,
            allLeadStatus,
            viewLeadUphoneData,
            visibleLeadUphone,
            viewLeadUphone,
            closed,
            closeMultipleUser,
            addMultipleUsers,
            visibleMultipleUser,
            multipleUserData,
            addUserSelect,

            refetchAllData,
            user,
        };
    },
};
</script>

<style lang="less">
.left-lead-campaigns .ps {
    height: calc(100vh - 150px);
}
</style>
