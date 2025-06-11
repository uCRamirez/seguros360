<template>
    <AddEdit
        :addEditType="addEditType"
        :visible="addEditVisible"
        @addEditSuccess="onAddEditSuccess"
        @closed="onCloseAddEdit"
        :formData="formData"
        :allProductos="allProductos"
        :data="viewData"
        :successMessage="successMessage"
    />

    <a-row v-if="showCampaignStatus">
        <a-col :span="24">
            <a-tabs
                v-model:activeKey="extraFilters.campaign_status"
                @change="campaignTypeChanged"
            >
                <a-tab-pane key="active">
                    <template #tab>
                        <span>
                            <PlayCircleOutlined />
                            {{ $t("campaign.active_campaign") }}
                        </span>
                    </template>
                </a-tab-pane>

                <a-tab-pane
                    v-if="
                        permsArray.includes('view_completed_campaigns') ||
                        permsArray.includes('admin')
                    "
                    key="completed"
                >
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
    <!-- FILTROS -->
    <a-row
        v-if="
            showAddButton ||
            showTableSearch ||
            showDateFilter ||
            showUserFilter ||
            permsArray.includes('leads_view_all') ||
            permsArray.includes('admin')
        "
        :gutter="[15, 15]"
        class="mb-20"
    >
        <!-- Filtro campo de busqueda -->
        <a-col :xs="24" :sm="24" :md="4" :lg="4" :xl="4">
            <a-input-search style="width: 100%" v-model:value="table.searchString" :placeholder="$t('common.select_default_text', [$t('common.information'),])" 
            show-search 
            @change="onTableSearch" 
            @search="onTableSearch"
            :loading="table.filterLoading" />
        </a-col>
        <!-- El select de los usuarios -->
        <a-col
            v-if="
                showUserFilter ||
                permsArray.includes('leads_view_all') ||
                permsArray.includes('admin')
            "
            :xs="24" :sm="24" :md="4" :lg="4" :xl="4"
        >
            <a-select
                v-model:value="filters['isSale.user_id']"
                :placeholder="$t('common.select_default_text', [$t('user.user')])"
                :allowClear="true"
                style="width: 100%"
                optionFilterProp="title"
                show-search
                @change="setUrlData"
            >
                <a-select-option
                    v-for="allUsers in allUsers"
                    :key="allUsers.xid"
                    :title="allUsers.name"
                    :value="allUsers.id"
                >
                    {{ allUsers.name }}
                </a-select-option>
            </a-select>
        </a-col>
        <!-- Filtro de estado de venta-->
        <a-col :xs="24" :sm="24" :md="12" :lg="4" :xl="4">
            <a-form-item >
                <a-select
                    v-model:value="filters['isSale.estadoVenta']"
                    :placeholder="
                    $t('common.select_default_text', [ $t('common.status'),])"
                    :allowClear="true"
                    show-search
                    @change="setUrlData"
                >
                    <a-select-option
                        key="Efectiva"
                        value="Efectiva"
                    >
                        {{ $t("lead_notes.effective") }}
                    </a-select-option>
                    <a-select-option
                        key="Cancelada"
                        value="Cancelada"
                    >
                        {{ $t("lead_notes.canceled") }}
                    </a-select-option>
                </a-select>
            </a-form-item>
        </a-col>
        <!-- Filtro de calidad-->
        <a-col :xs="24" :sm="24" :md="12" :lg="4" :xl="4">
            <a-form-item >
                <a-select
                    v-model:value="filters['isSale.calidad']"
                    :placeholder="
                    $t('common.select_default_text', [ $t('menu.quality'),])"
                    :allowClear="true"
                    show-search
                    @change="setUrlData"
                >
                    <a-select-option
                        :key="1"
                        :value="1"
                    >
                        {{ $t("common.yes") }}
                    </a-select-option>
                    <a-select-option
                        :key="'0'"
                        :value="'0'"
                    >
                        {{ $t("common.no") }}
                    </a-select-option>
                </a-select>
            </a-form-item>
        </a-col>
                                
        <!-- El dateTime de filtro en tipificaciones -->
        <a-col
            v-if="
                showDateFilter ||
                permsArray.includes('leads_view_all') ||
                permsArray.includes('admin')
            "
            :xs="24"
            :sm="24"
            :md="8"
            :lg="8"
            :xl="8"
        >
            <DateRangePicker
                @dateTimeChanged="
                    (changedDateTime) => {
                        extraFilters.dates = changedDateTime;
                        setUrlData();
                    }
                "
            />
        </a-col>
    </a-row>
    <!-- TABLA CON DATA -->
    <a-row class="mt-20">
        <a-col :span="24">
            <div class="table-responsive lead-notes-table">
                <a-table
                    :columns="columns"
                    :row-key="(record) => record.xid"
                    :data-source="table.data"
                    :pagination="table.pagination"
                    
                    @change="handleTableChange"
                    :scroll="scrollStyle"
                >
                    <template #bodyCell="{ column, record }">
                        <template v-if="column.dataIndex === 'is_sale.idVenta'">
                            {{
                                record.is_sale &&
                                record.is_sale.idVenta
                                    ? record.is_sale.idVenta
                                    : "-"
                            }}
                        </template>
                        <template v-if="column.dataIndex === 'cedula'">
                            <a-button
                                v-if="showLeadDetails"
                                type="link"
                                class="p-0"
                                @click="editItem(record)"
                            >
                                {{
                                    record.lead &&
                                    record.lead.cedula != "" &&
                                    record.lead.cedula != undefined
                                        ? record.lead.cedula
                                        : "---"
                                }}
                            </a-button>
                            <span v-else>{{ record.lead.document }}</span>
                        </template>
                        <template v-if="column.dataIndex === 'nombre'">
                            {{
                                record.lead &&
                                record.lead.nombre &&
                                record.lead.nombre != undefined
                                    ? `${record.lead.nombre} ${record.lead.apellido1} ${record.lead.apellido2}`
                                    : "-"
                            }}
                        </template>
                        <template v-if="column.dataIndex === 'campaign'">
                            {{
                                record.lead &&
                                record.lead.campaign &&
                                record.lead.campaign.name
                                    ? record.lead.campaign.name
                                    : "-"
                            }}
                        </template>
                        <template v-if="column.dataIndex === 'is_sale.estadoVenta'">
                            <a-tag v-if="record.is_sale.estadoVenta === 'Efectiva'" color="#4cb050">
                                {{ $t('lead_notes.effective') }}
                            </a-tag>
                            <a-tag v-else color="#f5b041">
                                {{ $t('lead_notes.canceled') }}
                            </a-tag>
                        </template>
                        <template v-if="column.dataIndex === 'is_sale.calidad'">
                            <a-tag v-if="record.is_sale.calidad === 1" color="#4cb050">
                                {{ $t('common.yes') }}
                            </a-tag>
                            <a-tag v-else color="#f5b041">
                                {{ $t('common.no') }}
                            </a-tag>
                        </template>
                        <template
                            v-for="allFormFieldName in allFormFieldNames"
                            :key="allFormFieldName.xid"
                        >
                            <template
                                v-if="
                                    record &&
                                    record.lead &&
                                    record.lead.lead_data &&
                                    column.dataIndex ===
                                        convertStringToKey(allFormFieldName.field_name)
                                "
                            >
                                {{
                                    findFieldValue(
                                        allFormFieldName.similar_field_names,
                                        record.lead.lead_data
                                    )
                                }}
                            </template>
                        </template>
                        <template v-if="column.dataIndex === 'notes'">
                            <a-comment>
                                <template #author>{{ record.is_sale.user.name }}</template>
                                <template #avatar>
                                    <a-avatar
                                        :src="record.is_sale.user.profile_image_url"
                                        :alt="record.is_sale.user.name"
                                    />
                                </template>
                                <template #content>
                                    <p style="text-align: justify">
                                        <a-typography-paragraph
                                            :ellipsis="{
                                                rows: 2,
                                                expandable: true,
                                                symbol: $t('common.more'),
                                            }"
                                            :content="record.notes"
                                        />
                                    </p>
                                </template>
                                <template #datetime>
                                    {{ formatDateTime(record.date_time) }}
                                </template>
                            </a-comment>
                        </template>
                        <template v-if="column.dataIndex === 'notes_file'">
                            <a-typography-link
                                :href="record.notes_file_url"
                                target="_blank"
                            >
                                <a-tag color="success">
                                    <template #icon>
                                        <DownloadOutlined />
                                    </template>
                                    {{ $t("common.download") }}
                                </a-tag>
                            </a-typography-link>
                        </template>
                        <template v-if="column.dataIndex === 'action'">
                            <a-space>
                                <a-typography-link
                                    v-if="record && record.notes_file_url"
                                    :href="record.notes_file_url"
                                    target="_blank"
                                >
                                    <a-button type="primary">
                                        <template #icon><DownloadOutlined /></template>
                                    </a-button>
                                </a-typography-link>
                                <a-button type="primary" @click="editItem(record)">
                                    <template #icon><EditOutlined v-if="(permsArray.includes('admin') || permsArray.includes('sales_edit')) && !soloVer"/>  <EyeOutlined v-else/> </template>
                                </a-button>
                                <a-button
                                    v-if="
                                        permsArray.includes('admin') || permsArray.includes('sales_delete')
                                    "
                                    type="primary"
                                    @click="showDeleteConfirm(record.xid)"
                                >
                                    <template #icon><DeleteOutlined /></template>
                                </a-button>
                            </a-space>
                        </template>
                    </template>
                </a-table>
            </div>
        </a-col>
    </a-row>

</template>

<script>
import { onMounted, ref, watch } from "vue";
import {
    PlusOutlined,
    EditOutlined,
    EyeOutlined,
    DeleteOutlined,
    PlayCircleOutlined,
    StopOutlined,
    DownloadOutlined,
} from "@ant-design/icons-vue";
import { forEach } from "lodash-es";
import crud from "../../../common/composable/crud";
import common from "../../../common/composable/common";
import viewDrawer from "../../../common/composable/viewDrawer";
import fields from "./fields";
import AddEdit from "./AddEdit.vue";
import DateRangePicker from "../../../common/components/common/calendar/DateRangePicker.vue";

export default {
    props: {
        pageName: {
            default: "index",
        },
        soloVer: {
            type: Boolean,
            default: true
        },
        leadId: {
            default: undefined,
        },
        managing: {
            default: undefined,
        },
        leadInfo: {
            default: undefined,
        },
        logType: {
            default: "notes",
        },
        scrollStyle: {
            default: {},
        },
        showCampaignStatus: {
            default: false,
        },
        showTableSearch: {
            default: false,
        },
        showUserFilter: {
            default: false,
        },
        showDateFilter: {
            default: true,
        },
        showAddButton: {
            default: false,
        },
        showActionButton: {
            default: true,
        },
        showFormFields: {
            default: false,
        },
        showLeadDetails: {
            default: false,
        },
    },
    emits: ["success"],
    components: {
        PlusOutlined,
        EditOutlined,
        EyeOutlined,
        DeleteOutlined,
        PlayCircleOutlined,
        StopOutlined,
        DownloadOutlined,
        AddEdit,
        DateRangePicker,
    },
    setup(props, { emit }) {
        const {
            permsArray,
            appSetting,
            user,
            formatDateTime,
            convertStringToKey,
            getCampaignUrl,
        } = common();
        const {
            url,
            addEditUrl,
            initData,
            columns,
            filterableColumns,
            allFormFieldNames,
            hashableColumns,
            allCampaigns,
            allUsers,
            getPrefetchData,
            allProductos,
        } = fields(props);
        const crudVariables = crud();
        const leadInfo = ref({});
        const leadDrawer = viewDrawer();
        const extraFilters = ref({
            page_name: props.pageName,
            log_type: props.logType,
            campaign_id: undefined,
            campaign_status: "active",
            lead_id: props.leadId,
            leadInfo: props.leadInfo,
            dates: [],
        });
        const filters = ref({
            'isSale.estadoVenta': undefined,
            'isSale.calidad': undefined,
            log_type: props.logType,
            isSale: 1,
            // user_id: undefined,
        });

        onMounted(() => {
            getPrefetchData().then((response) => {
                    filters.value = {
                        ...filters.value,
                    };
                
                    leadInfo.value = props.leadInfo;
                    crudVariables.crudUrl.value = addEditUrl;
                    crudVariables.langKey.value = "notes";
                    crudVariables.initData.value = { ...initData, lead_id: props.leadId };
                    crudVariables.formData.value = { ...initData, lead_id: props.leadId };
                    crudVariables.hashableColumns.value = [...hashableColumns];
                    crudVariables.hashable.value = [...hashableColumns];
                    setUrlData();
                    let test = allUsers.value;

            });
        });

        const setUrlData = () => {
            crudVariables.hashableColumns.value = [...hashableColumns];

            crudVariables.tableUrl.value = {
                url,
                filters,
                extraFilters,
            };
            crudVariables.table.filterableColumns = filterableColumns;

            crudVariables.table.pagination = {
                ...crudVariables.table.pagination,
                current: 1,
            };
            crudVariables.fetch({
                page: 1,
            });

            emit("updateNotesCount");
        };

        const findFieldValue = (allowedFieldName, leadDatas) => {
            var resultString = "-";

            forEach(leadDatas, (leadData) => {
                if (allowedFieldName.includes(leadData.field_name)) {
                    resultString = leadData.field_value;
                }
            });

            return resultString;
        };

        const onAddEditSuccess = (id) => {
            crudVariables.addEditSuccess(id);

            emit("success");
        };

        const campaignTypeChanged = (value) => {
            extraFilters.value = {
                ...extraFilters.value,
                campaign_id: undefined,
            };

            const campaignsUrl = getCampaignUrl(extraFilters.value.campaign_status);
            const campaignsPromise = axiosAdmin.get(campaignsUrl);

            Promise.all([campaignsPromise]).then(([campaignsResponse]) => {
                allCampaigns.value = campaignsResponse.data;

                setUrlData();
            });
        };

        watch(() => props.leadId, (newLeadId) => {
            if (newLeadId) {
                leadInfo.value = props.leadInfo;
                extraFilters.value.lead_id = newLeadId
                crudVariables.formData.value = { ...initData, lead_id: props.leadId };
                getPrefetchData();
                setUrlData();
            } else {
                leadInfo.value = {};
                crudVariables.table.data = []
                crudVariables.table.pagination.current = 1
                emit('update:leadId', null)
            }
        }, 
        { immediate: true }
        );

        return {
            permsArray,
            appSetting,
            columns,
            ...crudVariables,
            ...leadDrawer,
            filterableColumns,
            user,
            formatDateTime,
            allFormFieldNames,
            findFieldValue,
            convertStringToKey,
            onAddEditSuccess,

            allCampaigns,
            allProductos,
            allUsers,
            filters,
            extraFilters,
            setUrlData,
            campaignTypeChanged,
        };
    },
};
</script>
