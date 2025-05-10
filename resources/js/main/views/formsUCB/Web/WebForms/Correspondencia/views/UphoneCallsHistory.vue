<template>
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
                                :placeholder="$t('common.select_default_text', [''])"
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
                        <template v-if="column.dataIndex === 'lead_id'">
                            {{ record.lead_data.id }}
                        </template>
                        <template v-if="column.dataIndex === 'campaign_id'">
                            {{ record.campaigns.name }}
                        </template>
                    </template>
                </a-table>
            </div>
        </a-col>
    </a-row>
</template>

<script>
import { defineComponent, watch, ref, onMounted } from "vue";
import apiAdmin from "../../../../../../../common/composable/apiAdmin";
import { useI18n } from "vue-i18n";
import fields from "../../../../../../views/leads-calls/leads/fields";
import crud from "../../../../../../../common/composable/crud";

export default defineComponent({
    props: ["leadId", "visible"],
    setup(props, { emit }) {
        const { loading } = apiAdmin();
        const { t } = useI18n();
        const { uphoneColumn, uphoneFilterableColumns } = fields();

        const crudVariables = crud();
        const uphoneData = ref([]);
        const extraFilters = ref({
            lead_id: "",
        });

        onMounted(() => {
            extraFilters.value.lead_id = props.leadId;
            setUrlData();
        });

        const setUrlData = () => {
            crudVariables.tableUrl.value = {
                url: `uphone-calls?fields=id,xid,campaign,client_id,date,direction,duration,guid,discriptions,number,response_data,campaign_id,x_campaign_id,campaigns{id,xid,name},lead_id,x_lead_id,leadData{id,xid}`,
                extraFilters,
            };
            crudVariables.table.filterableColumns = uphoneFilterableColumns;

            crudVariables.fetch({
                page: 1,
            });
        };

        return {
            ...crudVariables,
            loading,
            uphoneColumn,
            uphoneData,
            setUrlData,
            uphoneFilterableColumns,
        };
    },
});
</script>
