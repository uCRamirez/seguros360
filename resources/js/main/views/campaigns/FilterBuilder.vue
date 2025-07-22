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
import { defineComponent, ref, watch, onMounted, computed} from 'vue';
import { useI18n } from 'vue-i18n';
import { PlusOutlined, MinusOutlined } from '@ant-design/icons-vue';

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
        const etapas = ['Nueva','Reproceso','N/A'];
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
        ];

        // Operadores
        const operators = [
            { label: 'Igual', value: '=' },
            { label: 'Contiene', value: 'like' },
            { label: 'Distinto', value: '!=' },
            { label: 'Mayor', value: '>' },
            { label: 'Menor', value: '<' },
            { label: 'Mayor o igual', value: '>=' },
            { label: 'Menor o igual', value: '<=' }
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
            } catch (e) {
                console.error("Error fetching localidades:", e);
            }
        };

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
            (newVal) => emit('update:modelValue', newVal),
            { deep: true }
        );

        watch(
        () => props.modelValue,
            (newVal) => {
                filtersLocal.value = newVal.length
                ? newVal.map(f => ({ ...f }))
                : [createEmptyFilter()];
                nextId = filtersLocal.value.length + 1;
            },
        { immediate: true, deep: true }
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
