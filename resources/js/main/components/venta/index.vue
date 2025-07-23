<template>
    <AddEdit
        :soloVer="soloVer"
        :addEditType="addEditType"
        :visible="addEditVisible"
        :url="addEditUrl"
        @addEditSuccess="onAddEditSuccess"
        @closed="onCloseAddEdit"
        :formData="formData"
        :leadInfo="leadInfo"
        :allCampaigns="allCampaigns"
        :allProductos="allProductos"
        :data="viewData"
        :pageTitle="!soloVer 
            ? $t('common.edit') + ' ' + $t('lead_notes.sale') 
            : $t('common.details') + ' ' +$t('lead_notes.sale')"
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

                <!-- <a-tab-pane
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
                </a-tab-pane> -->
            </a-tabs>
        </a-col>
    </a-row>

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
        <!-- El btn de agregar tipificaciones -->
        <a-col v-if="showAddButton" :xs="24" :sm="24" :md="12" :lg="6" :xl="6">
            <a-button type="primary" :disabled="managing === false" @click="addItem" block>
                <PlusOutlined />
                {{ $t("notes.add") }}
            </a-button>
        </a-col>
        <!-- El select de los usuarios -->
        <a-col
            v-if="
                showUserFilter ||
                permsArray.includes('leads_view_all') ||
                permsArray.includes('admin')
            "
            :xs="24"
            :sm="24"
            :md="12"
            :lg="6"
            :xl="6"
        >
            <a-select
                v-model:value="filters.user_id"
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
                    :value="allUsers.xid"
                >
                    {{ allUsers.name }}
                </a-select-option>
            </a-select>
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
        <!-- Filtro campo de busqueda -->
        <a-col :xs="24" :sm="24" :md="8" :lg="8" :xl="8" 
            v-if="
                showUserFilter &&
                permsArray.includes('leads_view_all') ||
                permsArray.includes('admin')
            ">
            <a-input-group class="d-flex" compact>
                <a-select
                    v-model:value="table.searchColumn"
                    :placeholder="$t('common.select_default_text', [''])"
                    class="flex-none"
                    style="width: 45%;"
                    :allowClear="true"
                >
                    <a-select-option
                        v-for="filterableColumn in filterableColumns"
                        :key="filterableColumn.key"
                    >
                        {{ filterableColumn.value }}
                    </a-select-option>
                </a-select>

                <a-input-search
                    v-model:value="table.searchString"
                    :placeholder="$t('common.select_default_text', [$t('common.information')])"
                    show-search
                    @change="onTableSearch"
                    @search="onTableSearch"
                    :loading="table.filterLoading"
                    class="flex-1"
                    style="min-width: 0;"  
                />
            </a-input-group>
        </a-col>
    </a-row>

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
                        <template v-if="column.dataIndex === 'id'">
                            {{
                                record.lead &&
                                record.lead.id != "" &&
                                record.lead.id != undefined
                                    ? record.lead.id
                                    : "---"
                            }}
                        </template>
                        <template v-if="column.dataIndex === 'cedula'">
                            <a-button
                                v-if="showLeadDetails"
                                type="link"
                                class="p-0"
                                @click="showViewDrawer({ 
                                    ...record.lead, 
                                    idNota: record.id
                                })"
                            >
                                {{
                                    record.lead &&
                                    record.lead.cedula != "" &&
                                    record.lead.cedula != undefined
                                        ? record.lead.cedula
                                        : "---"
                                }}
                            </a-button>
                            <span v-else>{{ record.lead.cedula }}</span>
                        </template>
                        <template v-if="column.dataIndex === 'nombre'">
                            {{
                                record.lead &&
                                record.lead.nombre &&
                                record.lead.nombre != undefined
                                    ? `${(record.lead.nombre ?? '')} ${(record.lead.apellido1 ?? '')} ${(record.lead?.apellido2 ?? '')}`
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
                                            :content=" [
                                                record.notes_typification_name_1,
                                                record.notes_typification_name_2,
                                                record.notes_typification_name_3,
                                                record.notes_typification_name_4
                                            ].filter(Boolean).join(' - ')
                                            "
                                        />
                                    </p>
                                    <strong><small v-if="record.next_contact">{{ `${$t("common.next_contact")} : ${ formatDateTime(record.next_contact)}` }}</small></strong>
                                </template>
                                <template #datetime>
                                    {{ formatDateTime(record.date_time) }}
                                </template>
                            </a-comment>
                        </template>
                        <template v-if="column.dataIndex === 'calidad'">
                            {{ record }}
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
                                    danger
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
    <a-row v-if="showCalidad">
        <a-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
            <a-descriptions :title="`${$t('common.information')} ${$t('menu.quality')}`">
                <a-descriptions-item :label="$t('common.date_time')">
                    {{ datosCalidad.esCalidad ? formatDateTime(datosCalidad.fecha_calidad) : "-" }}
                </a-descriptions-item>
                <a-descriptions-item :label="$t('common.action')">
                    {{ datosCalidad.esCalidad ? datosCalidad.accion_calidad : "-" }}
                </a-descriptions-item>
                <a-descriptions-item :label="$t('common.qualification')">
                    {{ datosCalidad.esCalidad ? datosCalidad.nota_estado : "-" }}
                </a-descriptions-item>
                <a-descriptions-item :label="$t('lead.agent')">
                    {{ datosCalidad.esCalidad ? datosCalidad.creado_por : "-" }}
                </a-descriptions-item>
            </a-descriptions>
        </a-col>
    </a-row>

    <!-- Global Compaonent -->
    <view-lead-details
        :visible="isViewDrawerVisible"
        :soloVer="soloVer"
        tipo="lead_note"
        :lead="viewDrawerData"
        @close="hideViewDrawer"
    />
</template>

<script>
import { onMounted, ref, watch, reactive } from "vue";
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

function getItemCalidad() {
    return {
        esCalidad: false,
        fecha_calidad: "",
        nota_estado: "",
        accion_calidad: "",
        creado_por: "",
    };
};
export var datosCalidad = reactive(getItemCalidad());
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
        idNota: {
            default: undefined,
        },
        soloUna: {
            default: false,
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
            t,
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
            log_type: props.logType,
            ...(props.soloVer ? {} : { isSale: 1 }),
            user_id: undefined,
        });

        const showCalidad = ref(false);

        onMounted(() => {
            showCalidad.value = false;
            Object.assign(datosCalidad, getItemCalidad());
            getPrefetchData().then((response) => {
                
                    leadInfo.value = props.leadInfo;
                    crudVariables.crudUrl.value = addEditUrl;
                    crudVariables.langKey.value = "notes";
                    crudVariables.initData.value = { ...initData, lead_id: props.leadId };
                    crudVariables.formData.value = { ...initData, lead_id: props.leadId };
                    crudVariables.hashableColumns.value = [...hashableColumns];
                    crudVariables.hashable.value = [...hashableColumns];
                    setUrlData();
            })
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
        
        watch(() => props.idNota, async (newIdNota) => {
            showCalidad.value = false;
            Object.assign(datosCalidad, getItemCalidad());
            if(props.soloUna){
                filters.value['lead_logs.id'] = newIdNota;
                if(newIdNota){
                    try {
                        const resp = await axiosAdmin.get(`evaluaciones-calidad/{${newIdNota},nota}`);
                        colocarCalidad(resp);
                    } catch (e) {
                        console.error('Error:', e);   
                    }
                }
            }else{
                filters.value['lead_logs.id'] = undefined;
            }
        }, { immediate: true })

        const colocarCalidad = (resp) => {
            if (resp.success && resp.evaluacion_calidad && resp.estado_calidad_venta) {
                showCalidad.value = true;
                datosCalidad.esCalidad = true;
                datosCalidad.fecha_calidad = resp.evaluacion_calidad.fecha_calidad;
                datosCalidad.nota_estado = resp.estado_calidad_venta.nota_estado;
                datosCalidad.accion_calidad = resp.evaluacion_calidad.accion_calidad ? resp.evaluacion_calidad.accion_calidad.nombre : "";
                datosCalidad.creado_por = resp.evaluacion_calidad.creador ? resp.evaluacion_calidad.creador.name : "";
            } else {
                showCalidad.value = false;
                Object.assign(datosCalidad, getItemCalidad());
            }
        }


        watch(() => props.leadId, (newLeadId) => {
            showCalidad.value = false;
            Object.assign(datosCalidad, getItemCalidad());
            if (newLeadId) {
                leadInfo.value = props.leadInfo;
                extraFilters.value.lead_id = newLeadId;
                extraFilters.value.id = props.idNota;
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
            datosCalidad,
            showCalidad,
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
