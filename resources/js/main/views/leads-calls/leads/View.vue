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
                >
                    <a-descriptions-item :label="$t('lead.document')">
                        {{ lead && lead.cedula ? lead.cedula : "-" }}
                    </a-descriptions-item>
                </a-descriptions>
                <a-descriptions
                    class="mt-10"
                >
                    <a-descriptions-item :label="$t('lead.name')">
                        {{
                            lead && lead.nombre && lead.nombre
                                ? lead.nombre
                                : "-"
                        }}
                        {{
                            lead && lead.apellido1 && lead.apellido1
                                ? lead.apellido1
                                : "-"
                        }}
                        {{
                            lead && lead.apellido2 && lead.apellido2
                                ? lead.apellido2
                                : "-"
                        }}
                    </a-descriptions-item>
                    <a-descriptions-item :label="$t('lead.date_birth')">
                        {{
                            lead && lead.fechaNacimiento && lead.fechaNacimiento
                                ? lead.fechaNacimiento
                                : "-"
                        }}
                    </a-descriptions-item>
                    <a-descriptions-item :label="$t('lead.age')">
                        {{
                            lead && lead.edad && lead.edad
                                ? lead.edad
                                : "-"
                        }}
                    </a-descriptions-item>
                </a-descriptions>
                <a-descriptions
                    class="mt-10"
                >
                    <a-descriptions-item :label="$t('lead.base_name')">
                        {{
                            lead && lead.nombreBase && lead.nombreBase
                                ? lead.nombreBase
                                : "-"
                        }}
                    </a-descriptions-item>
                    <a-descriptions-item :label="$t('lead.nationality')">
                        {{
                            lead && lead.nacionalidad && lead.nacionalidad
                                ? lead.nacionalidad
                                : "-"
                        }}
                    </a-descriptions-item>
                    <a-descriptions-item :label="$t('lead.phone') + ' ' + '1'">
                        {{
                            lead && lead.tel1 && lead.tel1
                                ? lead.tel1
                                : "-"
                        }}
                    </a-descriptions-item>
                </a-descriptions>
            </a-tab-pane>
            <a-tab-pane v-if="tipo === 'lead_note'" key="notes">
                <template #tab>
                    <span>
                        <FileTextOutlined />
                        {{ $t("menu.lead_notes") }}
                    </span>
                </template>
                <LeadNotesTable
                    :soloVer="soloVer"
                    :leadId="lead.xid"
                    :showAddButton="false"
                    @success="setNotesUrl"
                />
            </a-tab-pane>
            
        </a-tabs>
    </a-drawer>
</template>
<script>
import { defineComponent,computed, ref, onMounted, watch } from "vue";
import { useI18n } from "vue-i18n";
import {
    UnorderedListOutlined,
    PhoneOutlined,
    FileTextOutlined,
} from "@ant-design/icons-vue";
import common from "../../../../common/composable/common";
import LeadLogTable from "../../../components/lead-logs/index.vue";
import LeadNotesTable from "../../../components/venta/index.vue";
import VueLeadUphone from "./ViewLeadUphone.vue";
import { forEach } from "lodash-es";
import crud from "../../../../common/composable/crud";
import fields from "./fields";

export default defineComponent({
    props: {
        visible: {
            default: false,
        },
        soloVer: {
            type: Boolean,
            default: true
        },
        tipo: {
            default: "",
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
        const tipo = ref("");

        const extraFilters = ref({
            lead_id: "",
        });

        onMounted(() => {
            tipo.value = props.tipo;
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
                    //fetchUphoneCallData();
                }

            }
        );

        return {
            tipo,
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
