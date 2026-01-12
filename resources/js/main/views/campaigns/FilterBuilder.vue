<template>
    <a-row gutter="5">
        <template v-for="(filter, index) in filtersLocal" :key="filter.id">
            <a-col :xs="24" :sm="24" :md="7" :lg="7">
                <a-form-item>
                    <a-select v-model:value="filter.field" show-search option-filter-prop="title"
                        :placeholder="t('common.select_default_text')" @change="onFieldChange(index)">
                        <a-select-option v-for="col in columns" :title="col.label" :key="col.value" :value="col.value">
                            {{ col.label }}
                        </a-select-option>
                    </a-select>
                </a-form-item>
            </a-col>

            <a-col :xs="24" :sm="24" :md="6" :lg="6">
                <a-form-item>
                    <a-select v-model:value="filter.operator" :placeholder="t('common.select_default_text')" show-search
                        option-filter-prop="title">
                        <a-select-option v-for="op in operators" :title="op.label" :key="op.value" :value="op.value">
                            {{ op.label }}
                        </a-select-option>
                    </a-select>
                </a-form-item>
            </a-col>

            <a-col :xs="24" :sm="24" :md="7" :lg="7">
                <a-form-item v-if="['provincia', 'provincia_voto'].includes(filter.field)">
                    <a-select v-model:value="filter.value" show-search :allowClear="true"
                        :placeholder="$t('common.select_default_text')">
                        <a-select-option v-for="prov in provincias" :key="prov" :value="prov">
                            {{ prov }}
                        </a-select-option>
                    </a-select>
                </a-form-item>

                <a-form-item v-else-if="filter.field === 'canton'">
                    <a-select v-model:value="filter.value" show-search :allowClear="true"
                        :placeholder="$t('common.select_default_text')">
                        <a-select-option v-for="canton in cantones" :key="canton" :value="canton">
                            {{ canton }}
                        </a-select-option>
                    </a-select>
                </a-form-item>

                <a-form-item v-else-if="filter.field === 'distrito'">
                    <a-select v-model:value="filter.value" show-search :allowClear="true"
                        :placeholder="$t('common.select_default_text')">
                        <a-select-option v-for="distrito in distritos" :key="distrito" :value="distrito">
                            {{ distrito }}
                        </a-select-option>
                    </a-select>
                </a-form-item>

                <a-form-item v-else-if="filter.field === 'etapa'">
                    <a-select v-model:value="filter.value" show-search :allowClear="true"
                        :placeholder="$t('common.select_default_text')">
                        <a-select-option v-for="etapa in etapas" :key="etapa" :value="etapa">
                            {{ etapa }}
                        </a-select-option>
                    </a-select>
                </a-form-item>

                <a-form-item v-else-if="filter.field === 'typification_1_name'">
                    <a-select v-model:value="filter.value" show-search :allowClear="true"
                        :placeholder="$t('common.select_default_text')">
                        <a-select-option v-for="res in res1" :key="res" :value="res?.name">
                            {{ res?.name }}
                        </a-select-option>
                    </a-select>
                </a-form-item>

                <a-form-item v-else-if="filter.field === 'typification_2_name'">
                    <a-select v-model:value="filter.value" show-search :allowClear="true"
                        :placeholder="$t('common.select_default_text')">
                        <a-select-option v-for="res in res2" :key="res" :value="res?.name">
                            {{ res?.name }}
                        </a-select-option>
                    </a-select>
                </a-form-item>

                <a-form-item v-else-if="filter.field === 'typification_3_name'">
                    <a-select v-model:value="filter.value" show-search :allowClear="true"
                        :placeholder="$t('common.select_default_text')">
                        <a-select-option v-for="res in res3" :key="res" :value="res?.name">
                            {{ res?.name }}
                        </a-select-option>
                    </a-select>
                </a-form-item>

                <a-form-item v-else>
                    <a-input v-model:value="filter.value" :placeholder="t('common.value')" />
                </a-form-item>
            </a-col>

            <a-col :xs="24" :sm="24" :md="4" :lg="4" style="display: flex; justify-content: end; margin-bottom: 1%;">
                <a-button :disabled="filtersLocal.length >= 10" type="primary" @click="addFilter">
                    <template #icon>
                        <PlusOutlined />
                    </template>
                </a-button>
                <a-button :disabled="filtersLocal.length <= 1" danger @click="removeFilter(index)"
                    style="margin-left: 8px">
                    <template #icon>
                        <MinusOutlined />
                    </template>
                </a-button>
            </a-col>
        </template>
    </a-row>
</template>

<script>
import { defineComponent, ref, toRaw, watch, onMounted, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { PlusOutlined, MinusOutlined } from '@ant-design/icons-vue';
import isEqual from 'lodash/isEqual';

export default defineComponent({
    name: 'FilterBuilder',
    components: {
        PlusOutlined,
        MinusOutlined,
    },
    props: {
        modelValue: { type: Array, default: () => [] }
    },
    emits: ['update:modelValue'],
    setup(props, { emit }) {
        const { t } = useI18n();
        const localidades = ref([]);
        const etapas = [t('bases.new'), t('bases.reprocessing'), t('bases.na')];
        const provincias = computed(() => {
            return [...new Set(localidades.value.map(l => l.provincia))].sort();
        });

        const cantones = computed(() => {
            return [...new Set(localidades.value.map(l => l.canton))].sort();
        });

        const distritos = computed(() => {
            return [...new Set(localidades.value.map(l => l.distrito))].sort();
        });

        // Columnas disponibles
        const columns = [
            { label: t('lead.document'), value: 'cedula' },
            { label: t('lead.name'), value: 'nombre' },
            { label: t('lead.middle_name'), value: 'segundo_nombre' },
            { label: t('lead.first_last_name'), value: 'apellido1' },
            { label: t('lead.second_last_name'), value: 'apellido2' },
            { label: t('lead.plan_type'), value: 'tipo_plan' },
            { label: t('lead.expiration_date'), value: 'fechaVencimiento' },
            { label: t('lead.card_type'), value: 'tipo_tarjeta' },
            { label: t('lead.transmitter'), value: 'emisor' },
            { label: t('lead.last_digits'), value: 'ultimos_digitos' },
            { label: t('lead.month_charge'), value: 'mes_carga' },
            { label: t('lead.year_charge'), value: 'anno_carga' },
            { label: t('lead.sales_focus'), value: 'foco_venta' },
            // { label: t('lead.marital_status'), value: 'estadoCivil' },
            // { label: t('lead.children'), value: 'hijos' },
            { label: t('lead.date_birth'), value: 'fechaNacimiento' },
            { label: t('lead.age'), value: 'edad' },
            { label: t('lead.gender'), value: 'genero' },
            { label: t('lead.nationality'), value: 'nacionalidad' },
            { label: `${t('lead.phone')} 1`, value: 'tel1' },
            { label: `${t('lead.phone')} 2`, value: 'tel2' },
            { label: `${t('lead.phone')} 3`, value: 'tel3' },
            { label: `${t('lead.phone')} 4`, value: 'tel4' },
            { label: `${t('lead.phone')} 5`, value: 'tel5' },
            { label: `${t('lead.phone')} 6`, value: 'tel6' },
            { label: t('lead.email'), value: 'email' },
            { label: t('lead.electoral_province'), value: 'provincia_voto' },
            { label: t('lead.province'), value: 'provincia' },
            { label: t('lead.canton'), value: 'canton' },
            { label: t('lead.district'), value: 'distrito' },
            { label: t('lead.card'), value: 'tarjeta' },
            { label: t('bases.stage'), value: 'etapa' },
            { label: t('lead_notes.typification_1'), value: 'typification_1_name' },
            { label: t('lead_notes.typification_2'), value: 'typification_2_name' },
            { label: t('lead_notes.typification_3'), value: 'typification_3_name' },
        ];

        // Operadores
        const operators = [
            { label: t('common.operator_equal'), value: '=' },
            { label: t('common.operator_like'), value: 'like' },
            { label: t('common.operator_not_equal'), value: '!=' },
            { label: t('common.operator_greater'), value: '>' },
            { label: t('common.operator_less'), value: '<' },
            { label: t('common.operator_greater_equal'), value: '>=' },
            { label: t('common.operator_less_equal'), value: '<=' },
            { label: t('common.operator_starts_with'), value: 'likeIN' },
        ];

        let nextId = 1;
        function createEmptyFilter() {
            return { id: nextId++, field: null, operator: null, value: '' };
        }

        const fetchLocalidades = async () => {
            try {
                const resp = await axiosAdmin.get(
                    "localidades?fields=provincia,canton,distrito&limit=1000"
                );
                localidades.value = resp.data;

                axiosAdmin.get("notes-typifications?limit=10000").then(res => {
                    notesTypifications.value = res.data;
                    getParentTypification();
                });
            } catch (e) {
                console.error("Error fetching localidades:", e);
            }
        };
        const notesTypifications = ref([]);
        const res1 = ref([]);
        const res2 = ref([]);
        const res3 = ref([]);

        function getParentTypification() {
            res1.value = [];
            res2.value = [];
            res3.value = [];

            const parentIds = new Set();

            const res1Names = new Set();
            const res2Names = new Set();
            const res3Names = new Set();

            notesTypifications.value.forEach(t => {
                if (t.x_parent_id == null && !res1Names.has(t.name)) {
                    res1.value.push(t);
                    res1Names.add(t.name);
                    parentIds.add(t.xid);
                }
            });

            notesTypifications.value.forEach(t => {
                if (parentIds.has(t.x_parent_id)) {
                    if (!res2Names.has(t.name)) {
                        res2.value.push(t);
                        res2Names.add(t.name);
                    }
                } else if (t.x_parent_id != null) {
                    if (!res3Names.has(t.name)) {
                        res3.value.push(t);
                        res3Names.add(t.name);
                    }
                }
            });
        }



        // Estado local inicial
        const filtersLocal = ref([]);
        onMounted(() => {
            // inicializa con modelValue o un filtro vacío
            filtersLocal.value =
                props.modelValue.length
                    ? props.modelValue.map(f => ({ ...f }))
                    : [createEmptyFilter()];
            nextId = filtersLocal.value.length + 1;
            fetchLocalidades();
        });

        // Emitir cambios al padre
        watch(
            filtersLocal,
            newVal => {
                if (!isEqual(newVal, props.modelValue)) {
                    // evita emitir si realmente no cambió
                    emit('update:modelValue', toRaw(newVal));
                }
            },
            { deep: true }
        );

        watch(
            () => props.modelValue,
            (newVal) => {
                if (!isEqual(newVal, filtersLocal.value)) {
                    filtersLocal.value = newVal.length
                        ? newVal.map(f => ({ ...f }))
                        : [createEmptyFilter()];
                    nextId = filtersLocal.value.length + 1;
                }
            },
            { immediate: true }   // ← sin deep
        );


        function addFilter() {
            filtersLocal.value.push(createEmptyFilter());
        }

        function removeFilter(index) {
            filtersLocal.value.splice(index, 1);
        }

        function onFieldChange(index) {
            // lógica adicional si se requiere
        }

        return {
            res1,
            res2,
            res3,
            etapas,
            provincias,
            cantones,
            distritos,
            t,
            columns,
            operators,
            filtersLocal,
            addFilter,
            removeFilter,
            onFieldChange,
            PlusOutlined,
            MinusOutlined
        };
    }
});
</script>
