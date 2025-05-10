<template>
    <a-drawer
        :title="drawerTitle"
        :width="drawerWidth"
        :open="visible"
        :body-style="{ paddingBottom: '80px' }"
        :maskClosable="false"
        @close="onClose"
        :destroyOnClose="true"
    >
        <a-descriptions :title="$t('common.basic_details')">
            <a-descriptions-item :label="$t('lead.reference_number')">
                {{ lead && lead.reference_number ? lead.reference_number : "-" }}
            </a-descriptions-item>
            <a-descriptions-item :label="$t('lead.campaign')">
                {{ lead && lead.campaign ? lead.campaign.name : "-" }}
            </a-descriptions-item>
            <a-descriptions-item :label="$t('campaign.first_actioner')">
                {{
                    lead && lead.first_actioner && lead.first_actioner.name
                        ? lead.first_actioner.name
                        : "-"
                }}
            </a-descriptions-item>
            <a-descriptions-item :label="$t('campaign.last_actioner')">
                {{
                    lead && lead.last_actioner && lead.last_actioner.name
                        ? lead.last_actioner.name
                        : "-"
                }}
            </a-descriptions-item>
            <a-descriptions-item :label="$t('lead.call_duration')">
                {{ lead && lead.time_taken ? formatTimeDuration(lead.time_taken) : "-" }}
            </a-descriptions-item>
        </a-descriptions>

        <a-tabs v-model:activeKey="activeTabKey" class="mt-20">
            <a-tab-pane key="lead_data">
                <template #tab>
                    <span>
                        <UnorderedListOutlined />
                        {{ $t("lead.lead_data") }}
                    </span>
                </template>
                <a-descriptions
                    class="mt-10"
                    v-if="lead && lead.lead_data && lead.lead_data.length > 0"
                >
                    <a-descriptions-item
                        v-for="leadData in lead.lead_data"
                        :key="leadData.id"
                    >
                        <template #label>{{ leadData.field_name }}</template>
                        {{ leadData.field_value ? leadData.field_value : "-" }}
                    </a-descriptions-item>
                </a-descriptions>
            </a-tab-pane>
            <a-tab-pane key="call_logs">
                <template #tab>
                    <span>
                        <PhoneOutlined />
                        {{ $t("menu.call_logs") }}
                    </span>
                </template>

                <LeadLogTable
                    :leadId="lead.x_lead_id"
                    :showLeadDetails="false"
                    :showTableSearch="false"
                    :showAction="false"
                />
            </a-tab-pane>
            <a-tab-pane key="notes">
                <template #tab>
                    <span>
                        <FileTextOutlined />
                        {{ $t("menu.lead_notes") }}
                    </span>
                </template>
                <LeadNotesTable
                    :leadId="lead.xid"
                    :showAddButton="
                        lead && lead.campaign && lead.campaign.status != 'completed'
                            ? true
                            : false
                    "
                    @success="setNotesUrl"
                />
            </a-tab-pane>
            <a-tab-pane key="uphone_calls" @change="fetchUphoneCallData()">
                <template #tab>
                    <span>
                        <FileTextOutlined />
                        {{ $t("menu.uphone_calls") }}
                    </span>
                </template>
                <admin-page-filters>
                    <a-row :gutter="[16, 16]">
                        <a-col :xs="24" :sm="24" :md="12" :lg="10" :xl="10"> </a-col>
                        <a-col :xs="24" :sm="24" :md="12" :lg="14" :xl="14">
                            <a-row :gutter="[16, 16]" justify="end">
                                <a-col :xs="24" :sm="24" :md="14" :lg="16" :xl="16">
                                    <a-input-group compact>
                                        <a-select
                                            style="width: 25%"
                                            v-model:value="table.searchColumn"
                                            :placeholder="
                                                $t('common.select_default_text', [''])
                                            "
                                        >
                                            <a-select-option
                                                v-for="filterableColumn in uphoneFilterableColumns"
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
                                            :placeholder="
                                                $t('common.placeholder_search_text', [
                                                    $t('uphone_calls.uphone_calls'),
                                                ])
                                            "
                                        />
                                    </a-input-group>
                                </a-col>
                            </a-row>
                        </a-col>
                    </a-row>
                </admin-page-filters>
                <a-row>
                    <a-col :span="24">
                        <div class="table-responsive">
                            <a-table
                                :columns="uphoneColumn"
                                :row-key="(record) => record.xid"
                                :data-source="table.data"
                                :pagination="table.pagination"
                                :loading="table.loading"
                                @change="handleTableChange"
                            >
                                <template #bodyCell="{ column, record }">
                                    <template v-if="column.dataIndex === 'date'">
                                        {{ formatDateTime(record.date) }}
                                    </template>
                                    <!-- <template v-if="column.dataIndex === 'campaign_id'">
                                        {{ record.campaigns.name }}
                                    </template> -->
                                </template>
                            </a-table>
                        </div>
                    </a-col>
                </a-row>
            </a-tab-pane>
        </a-tabs>
    </a-drawer>
</template>
<script>
import { defineComponent, ref, onMounted, watch, computed } from "vue";
import { useI18n } from "vue-i18n";
import {
    UnorderedListOutlined,
    PhoneOutlined,
    FileTextOutlined,
} from "@ant-design/icons-vue";
import common from "../../../../common/composable/common";
import LeadLogTable from "../../../components/lead-logs/index.vue";
import LeadNotesTable from "../../../components/lead-notes/index.vue";
import VueLeadUphone from "./ViewLeadUphone.vue";
import { forEach } from "lodash-es";
import crud from "../../../../common/composable/crud";
import fields from "./fields";

export default defineComponent({
    props: {
        visible: {
            default: false,
        },
        title: {
            default: "",
        },
        lead: {
            default: {},
        },
    },
    emits: ["close"],
    components: {
        UnorderedListOutlined,
        PhoneOutlined,
        FileTextOutlined,
        VueLeadUphone,
        LeadLogTable,
        LeadNotesTable,
    },
    setup(props, { emit }) {
        const { formatTimeDuration, formatDateTime } = common();
        const crudVariables = crud();
        const { uphoneColumn, uphoneFilterableColumns } = fields();
        const { t } = useI18n();
        const drawerTitle = ref(t("lead.lead_details"));
        const activeTabKey = ref("lead_data");

        const extraFilters = ref({
            lead_id: "",
        });

        onMounted(() => {
            // fetchUphoneCallData();
        });

        const fetchUphoneCallData = () => {
            crudVariables.tableUrl.value = {
                url: `uphone-calls?fields=id,xid,campaign,client_id,date,direction,duration,guid,discriptions,number,response_data,campaign_id,x_campaign_id,campaigns{id,xid,name},lead_id,x_lead_id,leadData{id,xid}`,
                extraFilters,
            };
            crudVariables.table.filterableColumns = uphoneFilterableColumns;

            crudVariables.fetch({
                page: 1,
            });
        };

        const onClose = () => {
            activeTabKey.value = "lead_data";
            emit("close");
        };

        const setNotesUrl = () => {
            emit("setNotesCount");
        };

        watch(
            () => props.visible,
            (newVal) => {
                if (newVal && props.lead.xid) {
                    extraFilters.value.lead_id = props.lead.xid;
                    fetchUphoneCallData();
                }
            }
        );

        return {
            ...crudVariables,
            drawerTitle,
            formatTimeDuration,
            activeTabKey,
            setNotesUrl,
            onClose,
            uphoneColumn,
            formatDateTime,
            drawerWidth: window.innerWidth <= 991 ? "90%" : "60%",
            uphoneFilterableColumns,
        };
    },
});
</script>
