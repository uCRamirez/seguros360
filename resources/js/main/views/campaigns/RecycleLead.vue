<template>
    <a-drawer
        :title="$t('campaign.recycling_campaigns')"
        :width="drawerWidth"
        :open="visible"
        :body-style="{ paddingBottom: '80px' }"
        :footer-style="{ textAlign: 'right' }"
        :maskClosable="false"
        @close="onClose"
    >
        <a-form layout="vertical">
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('campaign.campaign')"
                        name="campaign_id"
                        :help="rules.campaign_id ? rules.campaign_id.message : null"
                        :validateStatus="rules.campaign_id ? 'error' : null"
                        class="required"
                    >
                        <span style="display: flex">
                            <a-select
                                v-model:value="formData.campaign_id"
                                :placeholder="
                                    $t('common.select_default_text', [
                                        $t('campaign.campaign'),
                                    ])
                                "
                                :allowClear="true"
                            >
                                <a-select-option
                                    v-for="campaign in campaigns"
                                    :key="campaign.xid"
                                    :value="campaign.xid"
                                >
                                    {{ campaign.name }}
                                </a-select-option>
                            </a-select>
                        </span>
                    </a-form-item>
                </a-col>
            </a-row>
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('campaign.select_lead_type')"
                        name="select_lead_type"
                        :help="
                            rules.select_lead_type ? rules.select_lead_type.message : null
                        "
                        :validateStatus="rules.select_lead_type ? 'error' : null"
                        class="required"
                    >
                        <span style="display: flex">
                            <a-select
                                v-model:value="formData.select_lead_type"
                                :placeholder="
                                    $t('common.select_default_text', [
                                        $t('campaign.lead_type'),
                                    ])
                                "
                                :allowClear="true"
                            >
                                <a-select-option value="all_non_managed_leads">
                                    {{ $t("campaign.all_non_managed_leads") }}
                                </a-select-option>
                                <a-select-option value="select_non_managed_leads">
                                    {{ $t("campaign.select_non_managed_leads") }}
                                </a-select-option>
                            </a-select>
                        </span>
                    </a-form-item>
                </a-col>
            </a-row>
            <a-row :gutter="16">
                <a-col
                    :xs="12"
                    :sm="12"
                    :md="12"
                    :lg="12"
                    v-if="formData.select_lead_type == 'select_non_managed_leads'"
                >
                    <a-form-item
                        :help="
                            rules.select_all_leads ? rules.select_all_leads.message : null
                        "
                        :validateStatus="rules.select_all_leads ? 'error' : null"
                        class="required"
                    >
                        <template #label>
                            {{ $t("campaign.select_all_leads") }} : {{ selectedRowCount }}
                        </template>
                    </a-form-item>
                </a-col>
            </a-row>
        </a-form>

        <a-row>
            <a-col
                :span="24"
                v-if="formData.select_lead_type == 'select_non_managed_leads'"
            >
                <div class="table-responsive">
                    <a-table
                        :row-selection="{
                            selectedRowKeys: selectedRowKeys,
                            preserveSelectedRowKeys: true,
                            onChange: onRowSelectChange,
                            getCheckboxProps: (record) => ({
                                disabled: false,
                                name: record.xid,
                            }),
                        }"
                        :columns="leadColumn"
                        :row-key="(record) => record.xid"
                        :data-source="table.data"
                        :pagination="table.pagination"
                        :loading="table.loading"
                        @change="handleTableChange"
                    >
                        <template #bodyCell="{ column, record }">
                            <template v-if="column.dataIndex === 'reference_number'">
                                {{
                                    record.reference_number != "" &&
                                    record.reference_number != undefined
                                        ? record.reference_number
                                        : "---"
                                }}
                            </template>
                            <template v-if="column.dataIndex === 'total_notes'">
                                {{ record.notes_count }}
                            </template>
                            <template v-if="column.dataIndex === 'campaign_id'">
                                {{ record.campaign.name }}
                            </template>
                            <template v-if="column.dataIndex === 'assign_to'">
                                {{ record.assign_users ? record.assign_users.name : "-" }}
                            </template>
                            <template v-if="column.dataIndex === 'name'">
                                <div v-for="lead in record.lead_data" :key="lead.id">
                                    <div v-if="lead.field_name === 'First Name'">
                                        {{ lead.field_value }}
                                    </div>
                                </div>
                            </template>
                            <template v-if="column.dataIndex === 'email'">
                                <div v-for="lead in record.lead_data" :key="lead.id">
                                    <div v-if="lead.field_name === 'Email'">
                                        {{ lead.field_value }}
                                    </div>
                                </div>
                            </template>
                        </template>
                    </a-table>
                </div>
            </a-col>
        </a-row>

        <template #footer>
            <a-button
                type="primary"
                @click="onSubmit"
                style="margin-right: 8px"
                :loading="loading"
            >
                <template #icon>
                    <SaveOutlined />
                </template>
                {{ $t("common.create") }}
            </a-button>
            <a-button @click="onClose">
                {{ $t("common.cancel") }}
            </a-button>
        </template>
    </a-drawer>
</template>

<script>
import { defineComponent, ref, watch } from "vue";
import { PlusOutlined, MinusSquareOutlined, SaveOutlined } from "@ant-design/icons-vue";
import apiAdmin from "../../../common/composable/apiAdmin";
import { useI18n } from "vue-i18n";
import fields from "./fields";
import crud from "../../../common/composable/crud";
import { filter, forEach } from "lodash-es";

export default defineComponent({
    props: ["data", "visible", "pageTitle"],
    components: {
        PlusOutlined,
        MinusSquareOutlined,
        SaveOutlined,
    },
    setup(props, { emit }) {
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const { leadColumn } = fields();
        const crudVariables = crud();

        const { t } = useI18n();
        const campaigns = ref([]);
        const formData = ref({
            campaign_id: undefined,
            select_lead_type: undefined,
            xid: undefined,
        });

        const selectedRowKeys = ref([]);

        const selectedRowCount = ref(0);

        const onRowSelectChange = (newSelectedRowKeys) => {
            selectedRowKeys.value = newSelectedRowKeys;
            selectedRowCount.value = selectedRowKeys.value.length;
        };

        const extraFilters = ref({
            xid: undefined,
        });

        const onSubmit = () => {
            const newFormData = {
                ...formData.value,
                selectedRowKeys: selectedRowKeys.value,
            };
            addEditRequestAdmin({
                url: "campaigns/recycle-campaign-leads",
                data: newFormData,
                successMessage: t("campaign.recucle_campaign_add"),
                success: (response) => {
                    formData.value = {};
                    selectedRowKeys.value = [];
                    selectedRowCount.value = 0;
                    emit("close");
                },
            });
        };

        const setUrlData = () => {
            crudVariables.tableUrl.value = {
                url: `leads?fields=id,xid,reference_number,lead_data,campaign_id,x_campaign_id,campaign{id,xid,name,status},notes_count,campaignUsers{id,xid,user_id,x_user_id},campaignUsers:user{id,xid,name},assign_to,x_assign_to,assignUsers{id,xid,name}`,
                extraFilters,
            };
            // crudVariables.table.filterableColumns = uphoneFilterableColumns;

            crudVariables.fetch({
                page: 1,
            });
        };

        const onClose = () => {
            rules.value = {};
            formData.value = {
                campaign_id: undefined,
                select_lead_type: undefined,
                xid: undefined,
            };

            selectedRowKeys.value = [];
            emit("close");
        };

        watch(
            () => props.visible,
            (newVal, oldVal) => {
                if (newVal) {
                    campaigns.value = [];
                    extraFilters.value.xid = props.data;
                    setUrlData();

                    formData.value.xid = props.data;

                    axiosAdmin
                        .post("campaigns/campaign-lists-except", { xid: props.data })
                        .then((response) => {
                            campaigns.value = response.data.campaigns;
                        });
                }
            }
        );

        return {
            ...crudVariables,
            rules,
            loading,
            onClose,
            onSubmit,
            campaigns,
            formData,
            setUrlData,
            leadColumn,
            onRowSelectChange,
            selectedRowKeys,
            selectedRowCount,
            drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
        };
    },
});
</script>
