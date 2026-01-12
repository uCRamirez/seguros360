<template>
    <a-drawer :title="$t('campaign.lead_distribution')" :width="drawerWidth" :open="visible"
        :body-style="{ paddingBottom: '80px' }" :footer-style="{ textAlign: 'right' }" :mask-closable="false"
        :destroyOnClose="true"
        @close="onClose">
        <a-row :gutter="16" class="mt-20">
            <a-col :xs="24" :sm="24" :md="12" :lg="12">
                <a-form-item>
                    <a-transfer v-model:target-keys="targetKeys" v-model:selected-keys="selectedKeys"
                        :data-source="mockData" :one-way="true" :render="item => item.title" :locale="{
                            itemUnit: $t('bases.base'),
                            itemsUnit: $t('bases.bases')
                        }" @change="handleChange" :show-search="true" :filter-option="filterOption" :list-style="{
                            width: '100%',
                            height: '300px',
                        }" />
                </a-form-item>
            </a-col>
            <a-col :xs="24" :sm="24" :md="12" :lg="12">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item>
                        <div style="height: 243px; overflow-y: auto; padding-right: 8px;">
                            <FilterBuilder v-model="filtros"/>
                        </div>
                    </a-form-item>
                </a-col>
                <a-col style="margin-bottom: 10px;display: flex;justify-content: end;gap: 1%;" :xs="24" :sm="24"
                    :md="24" :lg="24">

                    <a-select v-model:value="filtrarAsigandos" :placeholder="$t('campaign.assigned_leads')" :allowClear="true" style="width: 30%;">
                        <a-select-option :value="0">
                            {{ $t('common.no') }}
                        </a-select-option>
                        <a-select-option :value="1">
                            {{ $t('common.yes') }}
                        </a-select-option>
                    </a-select>

                    <a-select v-model:value="filtrarTrabajados" :placeholder="$t('common.leads_worked')" :allowClear="true" style="width: 30%;">
                        <a-select-option :value="0">
                            {{ $t('common.no') }}
                        </a-select-option>
                        <a-select-option :value="1">
                            {{ $t('common.yes') }}
                        </a-select-option>
                    </a-select>

                    <a-select v-model:value="noContacto" :placeholder="$t('common.no_contact')" :allowClear="true" style="width: 30%;">
                        <a-select-option :value="0">
                            {{ $t('common.no') }}
                        </a-select-option>
                        <a-select-option :value="1">
                            {{ $t('common.yes') }}
                        </a-select-option>
                    </a-select>
                    
                    <a-button type="primary" @click="serchInformationClient" :loading="tableClienteSerch.loading">
                        <template #icon>
                            <SearchOutlined />
                        </template>
                    </a-button>
                </a-col>
            </a-col>
        </a-row>

        <a-row>
            <a-col :span="24">
                <div class="table-responsive">
                    <a-table :row-key="record => record.id" :columns="columns" :data-source="tableClienteSerch.data"
                        :pagination="tableClienteSerch.pagination" :loading="tableClienteSerch.loading"
                        @change="handleClientSerchTableChange" bordered size="small">
                        <template #bodyCell="{ column, record }">
                            <template v-if="column.dataIndex === 'cedula'">
                                {{ record.cedula ?? '' }}
                            </template>
                            <template v-if="column.dataIndex === 'nombre'">
                                {{ `${record?.nombre ?? ``} ${record?.apellido1 ?? ``} ${record?.apellido2 ?? ``}` }}
                            </template>
                            <template v-if="column.dataIndex === 'edad'">
                                {{ record.edad ?? '' }}
                            </template>
                            <template v-if="column.dataIndex === 'etapa'">
                                {{
                                    record.etapa === 'Nueva'
                                        ? $t('bases.new')
                                        : record.etapa === 'Reproceso'
                                            ? $t('bases.reprocessing')
                                            : record.etapa === 'No aplica'
                                                ? $t('bases.na')
                                                : ''
                                }}
                            </template>
                            <template v-if="column.dataIndex === 'nombreBase'">
                                {{ record.nombreBase }}
                            </template>
                            <template v-if="column.dataIndex === 'assign_to'">
                                {{ record.assign_to && record.assign_to.user ? record.assign_to.user : '' }}
                            </template>
                        </template>
                        <template #footer>
                            <strong>{{ `${$t('campaign.records')} : ${registrosEncontrados}` }}</strong>
                        </template>
                    </a-table>
                </div>
            </a-col>
        </a-row>

        <a-row :gutter="16" align="bottom" class="mt-20">
            <a-col :xs="24" :sm="24" :md="6" :lg="6">
                <a-form-item>
                    <a-input-number v-model:value="maxRegistros" :min="1" :max="10000">
                        <template #addonBefore>
                            {{ $t('campaign.records') }}
                        </template>
                    </a-input-number>
                </a-form-item>
            </a-col>
            <a-col :xs="24" :sm="24" :md="6" :lg="6">
                <a-form-item :label="$t('campaign.members')" name="user_id" :help="rules.user_id?.message"
                    :validateStatus="rules.user_id ? 'error' : null" class="required">
                    <a-select v-model:value="selectedUserIds" mode="multiple"
                        :placeholder="$t('common.select_default_text', [$t('lead.agent')])" :allowClear="true"
                        style="min-width: 200px;">
                        <a-select-option v-for="member in campaign.campaign_users" :key="member.xid"
                            :value="member.user.id">
                            {{ member.user.name }}
                        </a-select-option>
                    </a-select>
                </a-form-item>
            </a-col>

            <a-col :xs="24" :sm="24" :md="6" :lg="6">
                <a-form-item>
                    <a-input-number :disabled="registrosEncontrados === 0" v-model:value="registrosPorAgente" :min="0" :max="10000">
                        <template #addonBefore>
                            {{ $t('common.records_by_agent') }}
                        </template>
                    </a-input-number>
                </a-form-item>
            </a-col>

            <a-col :xs="24" :sm="24" :md="6" :lg="6">
                <a-form-item>
                    <a-button :disabled="registrosEncontrados === 0 || selectedUserIds.length === 0" type="primary" @click="asignarLeadBuscados" :loading="tableClienteSerch.loading">
                        <template #icon>
                            <AppstoreAddOutlined />
                        </template>
                        {{ $t("lead.assign_users") }}
                    </a-button>
                </a-form-item>
            </a-col>

            <a-col :xs="24" :sm="24" :md="12" :lg="6">
                <a-form-item>
                    <a-checkbox v-model:checked="asignacionProgramada">
                        {{ $t('common.scheduled') }}
                    </a-checkbox>
                </a-form-item>
            </a-col>

            <a-col :xs="24" :sm="24" :md="12" :lg="6">
                <a-form-item>
                    <a-date-picker
                        v-model:value="fechaProgramada"
                        :disabled="!asignacionProgramada"
                        :show-time="true"
                        format="YYYY-MM-DD HH:mm:ss"
                        value-format="YYYY-MM-DD HH:mm:ss"
                        style="width: 100%"
                        :placeholder="$t('common.scheduled')"
                    />
                </a-form-item>
            </a-col>

        </a-row>


        <template #footer>
            <a-button @click="onClose">
                {{ $t('common.cancel') }}
            </a-button>
        </template>
    </a-drawer>
</template>

<script>
import { defineComponent, ref, reactive, watch, onMounted } from "vue";
import {
    MinusSquareOutlined,
    SaveOutlined,
    UserAddOutlined,
    CloudDownloadOutlined,
    UserDeleteOutlined,
    AppstoreAddOutlined,
    SearchOutlined,
} from "@ant-design/icons-vue";
import apiAdmin from "../../../common/composable/apiAdmin";
import { useI18n } from "vue-i18n";
import FilterBuilder from "./FilterBuilder.vue";
import { message } from 'ant-design-vue';

export default defineComponent({
    name: "RecycleLead",
    props: {
        visible: {
            default: false,
        },
        visible: {
            default: "",
        },
        disabled: {
            default: "",
        },
        campaign: { 
            default: null 
        }
    },
    emits: ["close"],
    components: {
        MinusSquareOutlined,
        SaveOutlined,
        UserAddOutlined,
        CloudDownloadOutlined,
        UserDeleteOutlined,
        AppstoreAddOutlined,
        FilterBuilder,
        SearchOutlined,
    },
    setup(props, { emit }) {
        const { t } = useI18n();
        const { loading, rules } = apiAdmin();

        const mockData = ref([]);
        const targetKeys = ref([]);
        const selectedKeys = ref([]);
        var basesSeleccionadas = [];
        const filtros = ref([]);
        const maxRegistros = ref(500);
        const registrosPorAgente = ref(0);
        const selectedUserIds = ref([]);
        const registrosEncontrados = ref(0);
        const filtrarAsigandos = ref(null);
        const filtrarTrabajados = ref(null);
        const noContacto = ref(null);
        const asignacionProgramada = ref(false);
        const fechaProgramada = ref(null);

        const tableClienteSerch = reactive({
            data: [],
            pagination: {
                current: 1,
                pageSize: 10,
                total: 0,
                showSizeChanger: false
            },
            loading: false,
            selectedRowKeys: [],
        });

        const columns = [
            { title: t("lead.document"), dataIndex: "cedula", key: "cedula" },
            { title: t("lead.name"), dataIndex: "nombre", key: "nombre" },
            { title: t("lead.age"), dataIndex: "edad", key: "edad" },
            { title: t("lead.base_name"), dataIndex: "nombreBase", key: "nombreBase" },
            { title: t("bases.stage"), dataIndex: "etapa", key: "etapa" },
            { title: t("lead.agent"), dataIndex: "assign_to", key: "assign_to" },
        ];

        const handleChange = (nextTargetKeys, direction, moveKeys) => {
            const movedItems = mockData.value.filter(item => moveKeys.includes(item.key));
            const movedLabels = movedItems.map(item => item.title);
            if (direction === "right") {
                movedLabels.forEach(label => {
                    if (!basesSeleccionadas.includes(label)) {
                        basesSeleccionadas.push(label);
                    }
                });
            } else {
                basesSeleccionadas = basesSeleccionadas.filter(label => !movedLabels.includes(label));
            }
        };

        const handleClientSerchTableChange = (pagination) => {
            tableClienteSerch.pagination.current = pagination.current;
        };
        const filterOption = (inputValue, item) => {
            return item.title.toLowerCase().includes(inputValue.toLowerCase());
        };

        const serchInformationClient = async () => {

            if (basesSeleccionadas.length <= 0) {
                message.info(t('common.minimum_base'));
                return;
            }
            tableClienteSerch.loading = true;
            try {
                const resp = await axiosAdmin.post(
                    'leads/find-distribution',
                    {
                        bases: basesSeleccionadas,
                        filtros: filtros.value,
                        campaign_id: props.campaign.id,
                        filtrarAsigandos: filtrarAsigandos.value,
                        filtrarTrabajados: filtrarTrabajados.value,
                        noContacto: noContacto.value,
                        maxRegistros: maxRegistros.value,
                    }
                );
                fillSearchTable(resp.data.leads);
            } catch (err) {
                fillSearchTable([]);
                console.error('Error al buscar leads con expansiones:', err);
            }
        };

        function distribuirLeads(leads, agents) {
            const totalLeads = leads.length;
            const totalAgents = agents.length;
            const perAgent = Math.floor(totalLeads / totalAgents);
            const remainder = totalLeads % totalAgents;

            const assignments = [];
            let cursor = 0;

            agents.forEach((agentId, idx) => {
                const count = perAgent + (idx < remainder ? 1 : 0);
                const slice = leads.slice(cursor, cursor + count);
                assignments.push({
                agent_id: agentId,
                lead_ids: slice.map(l => l.id)
                });
                cursor += count;
            });

            return assignments;
        }

        const asignarLeadBuscados = async () => {
            if (selectedUserIds.value.length <= 0) {
                message.info(t('common.select_default_text', [t('lead.agent')]));
                return;
            }
            if (tableClienteSerch.data.length === 0) {
                message.info(t('common.no_leads_found'));
                return;
            }

            tableClienteSerch.loading = true;
            try {

                const assignments = distribuirLeads(tableClienteSerch.data, selectedUserIds.value);
                await axiosAdmin.post('leads/assign', {
                    campaign_id: props.campaign.id,
                    assignments,
                    scheduled: asignacionProgramada.value ? 1 : 0,
                    scheduled_at: asignacionProgramada.value ? fechaProgramada.value : null
                });

                serchInformationClient();
                tableClienteSerch.loading = false;
                message.success(t('common.updated'));
                emit('close');

            } catch (err) {
                tableClienteSerch.loading = false;
                console.error('Error al asignar leads:', err);
                message.error(t('common.error'));
            }
        };

        const onRowSelectChange = (newSelectedRowKeys) => {
            tableClienteSerch.selectedRowKeys = newSelectedRowKeys;
        };

        const setUrlData = async () => {
            mockData.value = [];
            selectedKeys.value = []; 
            try {
                const resp = await axiosAdmin.get(`bases/${props.campaign.xid}`);
                fillSearchBases(resp);
            } catch (error) {
                if (error.response) {
                    console.error("Error 500 al obtener bases:", error.response.data);
                    this.$message.error("Ocurrió un error interno al cargar las bases.");
                } else {
                    console.error("Error de red o de configuración:", error);
                    this.$message.error("No se pudo conectar al servidor.");
                }
            }
        };

        const fillSearchBases = (data) => {
            const seenTitles = new Set();

            mockData.value = data
                .filter(base => {
                    if (seenTitles.has(base.nombreBase)) return false;
                        seenTitles.add(base.nombreBase);
                        return true;
                    })
                .map(base => ({
                    key: base.id,
                    title: base.nombreBase
            }));
        };

        const fillSearchTable = (data) => {
            registrosPorAgente.value = 0;
            if (data.length >= 1) {
                tableClienteSerch.data = data;
                tableClienteSerch.pagination.total = data.length;
                tableClienteSerch.selectedRowKeys = [];
                registrosEncontrados.value = data.length;
            } else {
                tableClienteSerch.data = [];
                tableClienteSerch.pagination.total = 0;
                tableClienteSerch.selectedRowKeys = [];
                registrosEncontrados.value = 0;
            }
            tableClienteSerch.loading = false;
        };

        const onClose = () => {
            rules.value = {};
            emit("close");
        };

        watch(
            () => props.visible,
            (newVal) => {
                if (newVal) {
                    filtros.value = [];
                    asignacionProgramada.value = false;
                    fechaProgramada.value = null;
                    filtrarAsigandos.value = null;
                    filtrarTrabajados.value = null;
                    noContacto.value = null;
                    maxRegistros.value = 500;
                    registrosEncontrados.value = 0;
                    registrosPorAgente.value = 0;
                    mockData.value = [];
                    targetKeys.value = [];
                    selectedKeys.value = [];
                    basesSeleccionadas = [];
                    selectedUserIds.value = [];
                    fillSearchTable([]);
                    setUrlData();
                }
            }
        );

        const drawerWidth = window.innerWidth <= 991 ? "90%" : "70%";

        return {
            fechaProgramada,
            asignacionProgramada,
            filtrarAsigandos,
            filtrarTrabajados,
            noContacto,
            asignarLeadBuscados,
            selectedUserIds,
            registrosEncontrados,
            registrosPorAgente,
            maxRegistros,
            filtros,
            filterOption,
            mockData,
            targetKeys,
            selectedKeys,
            tableClienteSerch,
            columns,
            loading,
            rules,
            onClose,
            handleChange,
            handleClientSerchTableChange,
            serchInformationClient,
            onRowSelectChange,
            fillSearchBases,
            fillSearchTable,
            drawerWidth
        };
    }
});
</script>
