<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.uphone_calls`)" class="p-0" />
        </template>
        <template #breadcrumb>
            <a-breadcrumb separator="-" style="font-size: 12px">
                <a-breadcrumb-item>
                    <router-link :to="{ name: 'admin.dashboard.index' }">
                        {{ $t(`menu.dashboard`) }}
                    </router-link>
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.uphone_calls`) }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </AdminPageHeader>

    <admin-page-filters>
        <a-row :gutter="[16, 16]">
            <a-col :xs="24" :sm="24" :md="12" :lg="10" :xl="10">
                <!-- <a-button
                    v-if="
                        table.selectedRowKeys.length > 0 &&
                        (permsArray.includes('leads_view_all') ||
                            permsArray.includes('admin'))
                    "
                    type="primary"
                    @click="showSelectedDeleteConfirm"
                    danger
                >
                    <template #icon><DeleteOutlined /></template>
                    {{ $t("common.delete") }}
                </a-button> -->
            </a-col>
            <a-col :xs="24" :sm="24" :md="12" :lg="14" :xl="14">
                <a-row :gutter="[16, 16]" justify="end">
                    <a-col :xs="24" :sm="24" :md="16" :lg="8" :xl="8">
                        <!-- <a-input-group compact>
                            <a-input-search
                                style="width: 100%"
                                v-model:value="table.campaign"
                                show-search
                                @change="onTableSearch"
                                @search="onTableSearch"
                                :loading="table.filterLoading"
                                :placeholder="
                                    $t('common.placeholder_search_text', [
                                        $t('lead.campaign'),
                                    ])
                                "
                            />
                        </a-input-group> -->
                    </a-col>
                    <a-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12">
                        <a-input-group compact>
                            <a-select
                                style="width: 25%"
                                v-model:value="table.searchColumn"
                                :placeholder="$t('common.select_default_text', [''])"
                            >
                                <a-select-option
                                    v-for="filterableColumn in filterableColumns"
                                    :key="filterableColumn.key"
                                >
                                    {{ filterableColumn.value }}
                                </a-select-option>
                            </a-select>
                            <a-input-search
                                style="width: 75%"
                                v-model:value="table.searchString"
                                show-search
                                @change="onTableSearch"
                                @search="onTableSearch"
                                :loading="table.filterLoading"
                            />
                        </a-input-group>
                    </a-col>
                </a-row>
            </a-col>
        </a-row>
    </admin-page-filters>

    <admin-page-table-content>
        <a-row>
            <a-col :span="24">
                <div class="table-responsive">
                    <a-table
                        :row-selection="{
                            selectedRowKeys: table.selectedRowKeys,
                            onChange: onRowSelectChange,
                            getCheckboxProps: (record) => ({
                                disabled: false,
                                name: record.xid,
                            }),
                        }"
                        :columns="columns"
                        :row-key="(record) => record.xid"
                        :data-source="table.data"
                        :pagination="table.pagination"
                        :loading="table.loading"
                        @change="handleTableChange"
                        bordered
                    >
                        <template #bodyCell="{ column, record }">
                            <template v-if="column.dataIndex === 'reference_number'">
                                <a-button
                                    v-if="record && record.x_lead_id && record.lead_data"
                                    type="link"
                                    class="p-0"
                                    @click="showViewDrawer(record.lead_data)"
                                >
                                    {{
                                        record.lead_data.reference_number != "" &&
                                        record.lead_data.reference_number != undefined
                                            ? record.lead_data.reference_number
                                            : "---"
                                    }}
                                </a-button>
                                <span v-else> - </span>
                            </template>
                            <template v-if="column.dataIndex === 'campaign_id'">
                                {{ record.campaigns.name }}
                            </template>
                            <template v-if="column.dataIndex === 'campaign'">
                                {{ record.campaign }}
                            </template>
                            <template v-if="column.dataIndex === 'action'">
                                <a-space>
                                    <a-button
                                        type="primary"
                                        @click="viewUphoneCalls(record)"
                                        style="margin-left: 4px"
                                    >
                                        <template #icon><EyeOutlined /></template>
                                    </a-button>
                                </a-space>
                            </template>
                        </template>
                    </a-table>
                </div>
            </a-col>
        </a-row>
    </admin-page-table-content>

    <!-- Global Compaonent -->
    <view-lead-details
        :visible="isViewDrawerVisible"
        :lead="viewDrawerData"
        @close="hideViewDrawer"
    />

    <ViewUphone
        :data="uphoneCallsData"
        :visible="visibleUphoneCalls"
        @close="closed"
        :pageTitle="$t('uphone_calls.uphone_calls_details')"
    />
</template>

<script>
import { ref, onMounted, watch } from "vue";
import { DoubleRightOutlined, DeleteOutlined, EyeOutlined } from "@ant-design/icons-vue";
import common from "../../../common/composable/common";
import fields from "./fields";
import AdminPageHeader from "../../../common/layouts/AdminPageHeader.vue";
import crud from "../../../common/composable/crud";
import ViewUphone from "./ViewUphone.vue";
import viewDrawer from "../../../common/composable/viewDrawer";

export default {
    components: {
        DoubleRightOutlined,
        EyeOutlined,
        AdminPageHeader,
        ViewUphone,
    },
    setup() {
        const { permsArray, reloadUphoneStringData } = common();
        const { url, columns, hashableColumns, filterableColumns } = fields();
        const leadDrawer = viewDrawer();
        const crudVariables = crud();
        const uphoneCallsData = ref({});
        const visibleUphoneCalls = ref(false);
        onMounted(() => {
            setUrlData();
        });

        const viewUphoneCalls = (item) => {
            visibleUphoneCalls.value = true;
            uphoneCallsData.value = item;
        };

        const setUrlData = () => {
            crudVariables.tableUrl.value = {
                url,
            };
            crudVariables.table.filterableColumns = filterableColumns;

            crudVariables.fetch({
                page: 1,
            });
            crudVariables.hashableColumns.value = [...hashableColumns];
        };

        const closed = () => {
            visibleUphoneCalls.value = false;
        };

        watch(reloadUphoneStringData, (newVal, oldVal) => {
            setUrlData();
        });

        return {
            ...crudVariables,
            ...leadDrawer,
            filterableColumns,
            url,
            columns,
            hashableColumns,
            permsArray,
            setUrlData,
            viewUphoneCalls,
            uphoneCallsData,
            visibleUphoneCalls,
            closed,
        };
    },
};
</script>
