<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.audits`)" class="p-0" />
        </template>
        <template #breadcrumb>
            <a-breadcrumb separator="-" style="font-size: 12px">
                <a-breadcrumb-item>
                    <router-link :to="{ name: 'admin.dashboard.index' }">
                        {{ $t(`menu.dashboard`) }}
                    </router-link>
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.audits`) }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </AdminPageHeader>
    <a-row>
        <a-col
            :xs="24"
            :sm="24"
            :md="24"
            :lg="4"
            :xl="4"
            :style="{
                background: themeMode == 'dark' ? '#141414' : '#fff',
                borderRight:
                    themeMode == 'dark'
                        ? '1px solid #303030'
                        : '1px solid #f0f0f0',
            }"
        >
            <SettingSidebar />
        </a-col>
        <a-col :xs="24" :sm="24" :md="24" :lg="20" :xl="20">
            <admin-page-filters>
                <a-row :gutter="[16, 16]">
                    <a-col :xs="24" :sm="24" :md="12" :lg="6" :xl="6">
                        <a-button
                            type="primary"
                            style="margin-left: 4px; width: 50%; padding: 5px"
                            @click="auditDownload"
                        >
                            <template #icon
                                ><DownloadOutlined />
                                {{ $t("menu.export_audits") }}
                            </template>
                        </a-button>
                    </a-col>
                    <a-col :xs="24" :sm="24" :md="12" :lg="18" :xl="18">
                        <a-row :gutter="[16, 16]" justify="end">
                            <a-col :xs="24" :sm="24" :md="12" :lg="4" :xl="4">
                                <a-select
                                    v-model:value="extraFilters.auditable_type"
                                    show-search
                                    style="width: 100%"
                                    :placeholder="
                                        $t('common.select_default_text', [
                                            $t('audit.auditable_type'),
                                        ])
                                    "
                                    @change="setUrlData"
                                    :allowClear="true"
                                    optionFilterProp="label"
                                >
                                    <a-select-option
                                        v-for="auditData in auditDatas"
                                        :key="auditData.auditable_type"
                                        :value="auditData.auditable_type"
                                        :label="auditData.auditable_type"
                                    >
                                        {{
                                            breakString(
                                                auditData.auditable_type
                                            )
                                        }}
                                    </a-select-option>
                                </a-select>
                            </a-col>
                            <a-col :xs="24" :sm="24" :md="12" :lg="4" :xl="4">
                                <a-select
                                    v-model:value="extraFilters.event"
                                    show-search
                                    style="width: 100%"
                                    @change="setUrlData"
                                    :placeholder="
                                        $t('common.select_default_text', [
                                            $t('audit.action'),
                                        ])
                                    "
                                    :allowClear="true"
                                >
                                    <a-select-option value="created">{{
                                        $t("audit.created")
                                    }}</a-select-option>
                                    <a-select-option value="updated">
                                        {{
                                            $t("audit.updated")
                                        }}</a-select-option
                                    >
                                    <a-select-option value="deleted">
                                        {{
                                            $t("audit.deleted")
                                        }}</a-select-option
                                    >
                                </a-select>
                            </a-col>
                            <a-col :xs="24" :sm="24" :md="12" :lg="4" :xl="4">
                                <a-select
                                    v-model:value="extraFilters.user_id"
                                    show-search
                                    style="width: 100%"
                                    :placeholder="
                                        $t('common.select_default_text', [
                                            $t('audit.user'),
                                        ])
                                    "
                                    @change="setUrlData"
                                    :allowClear="true"
                                    optionFilterProp="label"
                                >
                                    <a-select-option
                                        v-for="staffMember in staffMembers"
                                        :key="staffMember.xid"
                                        :value="staffMember.xid"
                                        :label="staffMember.name"
                                    >
                                        {{ staffMember.name }}
                                    </a-select-option>
                                </a-select>
                            </a-col>
                            <a-col :xs="24" :sm="24" :md="12" :lg="10" :xl="10">
                                <a-input-group compact>
                                    <a-select
                                        style="width: 40%"
                                        v-model:value="table.searchColumn"
                                        :placeholder="
                                            $t('common.select_default_text', [
                                                '',
                                            ])
                                        "
                                    >
                                        <a-select-option
                                            v-for="filterableColumn in filterableColumns"
                                            :key="filterableColumn.key"
                                        >
                                            {{ filterableColumn.value }}
                                        </a-select-option>
                                    </a-select>
                                    <a-input-search
                                        style="width: 60%"
                                        v-model:value="table.searchString"
                                        show-search
                                        @change="onTableSearch"
                                        @search="onTableSearch"
                                        :loading="table.filterLoading"
                                        :placeholder="$t('common.search')"
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
                                :columns="columns"
                                :row-key="(record) => record.xid"
                                :data-source="table.data"
                                :pagination="table.pagination"
                                :loading="table.loading"
                                @change="handleTableChange"
                                bordered
                                size="middle"
                            >
                                <template #bodyCell="{ column, record }">
                                    <template
                                        v-if="column.dataIndex === 'event'"
                                    >
                                        {{
                                            breakString(record.auditable_type)
                                        }}&nbsp;{{
                                            record.event == "deleted"
                                                ? $t("audit.deleted")
                                                : record.event == "updated"
                                                ? $t("audit.updated")
                                                : $t("audit.created")
                                        }}
                                    </template>
                                    <template
                                        v-if="column.dataIndex === 'user_id'"
                                    >
                                        {{ record.user?.name }}
                                    </template>
                                    <template
                                        v-if="column.dataIndex === 'ip_address'"
                                    >
                                        <a-popover
                                            :title="$t('audit.user_agent')"
                                        >
                                            <template #content>
                                                <p>{{ record.user_agent }}</p>
                                            </template>
                                            {{ record.ip_address }}
                                        </a-popover>
                                    </template>
                                    <template
                                        v-if="column.dataIndex === 'created_at'"
                                    >
                                        {{ formatDateTime(record.created_at) }}
                                    </template>
                                    <template
                                        v-if="column.dataIndex === 'action'"
                                    >
                                        <a-button
                                            type="primary"
                                            @click="viewItem(record)"
                                            style="margin-left: 4px"
                                        >
                                            <template #icon
                                                ><EyeOutlined
                                            /></template>
                                        </a-button>
                                    </template>
                                </template>
                            </a-table>
                        </div>
                    </a-col>
                </a-row>
                <AuditView
                    :auditData="viewData"
                    :visible="detailsVisible"
                    @closed="onCloseDetails"
                />
            </admin-page-table-content>
        </a-col>
    </a-row>
</template>

<script>
import { ref, onMounted, computed } from "vue";
import {
    PlusOutlined,
    EditOutlined,
    DeleteOutlined,
    EyeOutlined,
    DownloadOutlined,
} from "@ant-design/icons-vue";
import crud from "../../../../common/composable/crud";
import common from "../../../../common/composable/common";
import AdminPageHeader from "../../../../common/layouts/AdminPageHeader.vue";
import fields from "./fields";
import SettingSidebar from "../SettingSidebar.vue";
import AuditView from "./View.vue";
import { useStore } from "vuex";

export default {
    components: {
        PlusOutlined,
        EditOutlined,
        DeleteOutlined,
        AdminPageHeader,
        SettingSidebar,
        EyeOutlined,
        AuditView,
        DownloadOutlined,
    },
    setup() {
        const {
            permsArray,
            appSetting,
            formatAmountCurrency,
            formatDateTime,
            themeMode,
        } = common();
        const { fieldsString, url, addEditUrl, columns, filterableColumns } =
            fields();
        const crudVariables = crud();
        const store = useStore();
        const staffMembers = ref([]);
        const extraFilters = ref({
            user_id: undefined,
            event: undefined,
            auditable_type: undefined,
        });
        const auditDatas = ref([]);

        onMounted(() => {
            const auditsDataPromise = axiosAdmin.get("audits-data");
            const staffMembersPromise = axiosAdmin.get("users?limit=10000");

            Promise.all([staffMembersPromise, auditsDataPromise]).then(
                ([staffMembersResponse, auditsDataResponse]) => {
                    staffMembers.value = staffMembersResponse.data;
                    auditDatas.value = auditsDataResponse.data.data;
                }
            );
            setUrlData();
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

            crudVariables.crudUrl.value = addEditUrl;
            crudVariables.langKey.value = "audit";
        };

        function parseQueryString(queryString) {
            // Split the query string into individual key-value pairs
            const pairs = queryString.split("&");

            // Initialize an empty object
            const result = {};

            // Iterate through the pairs
            pairs.forEach((pair) => {
                // Split each pair into key and value
                const [key, value] = pair.split("=");

                // Add the key-value pair to the result object
                result[key] = value;
            });

            return result;
        }

        const auditDownload = () => {
            // pdfDownloadLoading.value = true;
            var data = {};

            const urlString =
                crudVariables.getUrlWithQueryStringFromUrl(fieldsString);
            const requestObject = parseQueryString(urlString);
            const filtervalue = decodeURIComponent(requestObject.filters);
            if (
                requestObject &&
                requestObject.filters != "" &&
                requestObject.filters != undefined
            ) {
                data = { ...requestObject, filters: filtervalue };
            } else {
                data = { ...requestObject };
            }

            axiosAdmin
                .post("audit/download", data, {
                    responseType: "blob",
                })
                .then((response) => {
                    // Convert the response to a blob
                    const pdfBlob = response;

                    // Create a URL for the blob
                    const blobURL = URL.createObjectURL(pdfBlob);

                    // Create a temporary link element
                    const link = document.createElement("a");
                    link.href = blobURL;
                    link.download = "audit.xlsx"; // Set the desired file name
                    document.body.appendChild(link);

                    // Trigger the download
                    link.click();

                    // Clean up
                    URL.revokeObjectURL(blobURL);
                    document.body.removeChild(link);

                    // pdfDownloadLoading.value = false;
                })
                .catch((error) => {
                    // pdfDownloadLoading.value = false;
                });
        };

        const breakString = (str) => {
            return str.slice(11);
        };
        return {
            permsArray,
            appSetting,
            formatAmountCurrency,
            columns,
            ...crudVariables,
            filterableColumns,
            formatDateTime,
            breakString,
            themeMode,
            staffMembers,
            extraFilters,
            setUrlData,
            auditDatas,
            auditDownload,
        };
    },
};
</script>
