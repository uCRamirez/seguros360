<template>
    <div v-if="permsArray.includes('leads_create') || permsArray.includes('admin')">
        <a-tooltip :title="$t('lead.add')">
            <a-button :type="btnType" :class="btnClass" @click="showAdd">
                <template #icon>
                    <slot name="icon"></slot>
                </template>
                <slot></slot>
            </a-button>
        </a-tooltip>

        <a-drawer
            :title="$t('lead.add')"
            :width="drawerWidth"
            :open="visible"
            :body-style="{ paddingBottom: '80px' }"
            :footer-style="{ textAlign: 'right' }"
            :maskClosable="false"
            @close="onClose"
        >
            <a-form layout="vertical">

                <!-- asignar y cedula -->
                <a-row :gutter="16">
                    <a-col :xs="24" :sm="24" :md="12" :lg="12">
                        <a-form-item
                            :label="$t('lead.assign_to')"
                            name="assign_to"
                            :help="rules.assign_to ? $t('lead.assign_to') : null"
                            :validateStatus="rules.assign_to ? 'error' : null"
                            class="required"
                        >
                            <span style="display: flex">
                                <a-select
                                    v-model:value="formData.assign_to"
                                    :placeholder="
                                        $t('common.select_default_text', [
                                            $t('lead.assign_to'),
                                        ])
                                    "
                                    :allowClear="true"
                                >
                                    <a-select-option
                                        v-for="user in users"
                                        :key="user.xid"
                                        :value="user.xid"
                                    >
                                        {{ user.name }}
                                    </a-select-option>
                                </a-select>
                            </span>
                        </a-form-item>
                    </a-col>
                    <a-col :xs="24" :sm="24" :md="12" :lg="12">
                        <a-form-item
                            :label="$t('lead.document')"
                            name="cedula"
                            :help="rules.cedula ? $t('lead.document'): null"
                            :validateStatus="rules.cedula ? 'error' : null"
                            class="required"
                        >
                        <a-input v-model:value="formData.cedula"
                                :placeholder="$t('common.placeholder_default_text',[$t('lead.document'),])" />
                        </a-form-item>
                    </a-col>
                </a-row>
                <!-- Nombre 1 y 2 -->
                <a-row :gutter="16">
                    <a-col :xs="24" :sm="24" :md="12" :lg="12">
                        <a-form-item
                            :label="$t('lead.name')"
                            name="name"
                            :help="rules.name ? $t('lead.name') : null"
                            :validateStatus="rules.name ? 'error' : null"
                        >
                        <a-input v-model:value="formData.nombre"
                                :placeholder="$t('common.placeholder_default_text',[$t('lead.name'),])" />
                        </a-form-item>
                    </a-col>
                    <a-col :xs="24" :sm="24" :md="12" :lg="12">
                        <a-form-item
                            :label="$t('lead.middle_name')"
                            name="segundo_nombre"
                            :help="rules.segundo_nombre ? $t('lead.middle_name') : null"
                            :validateStatus="rules.segundo_nombre ? 'error' : null"
                        >
                        <a-input v-model:value="formData.segundo_nombre"
                                :placeholder="$t('common.placeholder_default_text',[$t('lead.middle_name'),])" />
                        </a-form-item>
                    </a-col>
                </a-row>
                <!-- apellido 1 y 2 -->
                <a-row :gutter="16">
                    <a-col :xs="24" :sm="24" :md="12" :lg="12">
                        <a-form-item
                            :label="$t('lead.first_last_name')"
                            name="first_last_name"
                            :help="rules.first_last_name ? $t('lead.first_last_name') : null"
                            :validateStatus="rules.first_last_name ? 'error' : null"
                        >
                        <a-input v-model:value="formData.apellido1"
                                :placeholder="$t('common.placeholder_default_text',[$t('lead.first_last_name'),])" />
                        </a-form-item>
                    </a-col>
                    <a-col :xs="24" :sm="24" :md="12" :lg="12">
                        <a-form-item
                            :label="$t('lead.second_last_name')"
                            name="second_last_name"
                            :help="rules.second_last_name ? $t('lead.second_last_name') : null"
                            :validateStatus="rules.second_last_name ? 'error' : null"
                        >
                        <a-input v-model:value="formData.apellido2"
                                :placeholder="$t('common.placeholder_default_text',[$t('lead.second_last_name'),])" />
                        </a-form-item>
                    </a-col>
                </a-row>
                <!-- tipo plan y fecha de vencimiento -->
                <a-row :gutter="16">
                    <a-col :xs="24" :sm="24" :md="12" :lg="12">
                        <a-form-item
                            :label="$t('lead.plan_type')"
                            name="tipo_plan"
                            :help="rules.tipo_plan ? $t('lead.plan_type') : null"
                            :validateStatus="rules.tipo_plan ? 'error' : null"
                        >
                        <a-input v-model:value="formData.tipo_plan"
                                :placeholder="$t('common.placeholder_default_text',[$t('lead.plan_type'),])" />
                        </a-form-item>
                    </a-col>
                    <a-config-provider :locale="antdLocale">
                        <a-col :xs="24" :sm="24" :md="12" :lg="12">
                            <a-form-item
                                :label="$t('lead.expiration_date')"
                                name="second_last_name"
                                :help="rules.second_last_name ? $t('lead.expiration_date') : null"
                                :validateStatus="rules.second_last_name ? 'error' : null"
                            >
                            <a-date-picker
                                format="YYYY-MM-DD" value-format="YYYY-MM-DD"
                                v-model:value="formData.fechaVencimiento"
                                style="width: 100%" />
                            </a-form-item>
                        </a-col>
                    </a-config-provider>
                </a-row>
                <!-- tarjeta, tipo tarjeta y emisor -->
                <a-row :gutter="16">
                    <a-col :xs="24" :sm="24" :md="8" :lg="8">
                        <a-form-item
                            :label="$t('lead.card')"
                            name="tarjeta"
                            :help="rules.tarjeta ? $t('lead.card') : null"
                            :validateStatus="rules.tarjeta ? 'error' : null"
                        >
                        <a-input v-model:value="formData.tarjeta"
                                :placeholder="$t('common.placeholder_default_text',[$t('lead.card'),])" />
                        </a-form-item>
                    </a-col>
                    <a-col :xs="24" :sm="24" :md="8" :lg="8">
                        <a-form-item
                            :label="$t('lead.card_type')"
                            name="tipo_tarjeta"
                            :help="rules.tipo_tarjeta ? $t('lead.card_type') : null"
                            :validateStatus="rules.tipo_tarjeta ? 'error' : null"
                        >
                        <a-input v-model:value="formData.tipo_tarjeta"
                                :placeholder="$t('common.placeholder_default_text',[$t('lead.card_type'),])" />
                        </a-form-item>
                    </a-col>
                    <a-col :xs="24" :sm="24" :md="8" :lg="8">
                        <a-form-item
                            :label="$t('lead.transmitter')"
                            name="emisor"
                            :help="rules.emisor ? $t('lead.transmitter') : null"
                            :validateStatus="rules.emisor ? 'error' : null"
                        >
                        <a-input v-model:value="formData.emisor"
                                :placeholder="$t('common.placeholder_default_text',[$t('lead.transmitter'),])" />
                        </a-form-item>
                    </a-col>
                </a-row>
                <!-- ultimos digitos y mes y anno de carga -->
                <a-row :gutter="16">
                    <a-col :xs="24" :sm="24" :md="8" :lg="8">
                        <a-form-item
                            :label="$t('lead.last_digits')"
                            name="ultimos_digitos"
                            :help="rules.ultimos_digitos ? $t('lead.last_digits') : null"
                            :validateStatus="rules.ultimos_digitos ? 'error' : null"
                        >
                        <a-input v-model:value="formData.ultimos_digitos"
                                :placeholder="$t('common.placeholder_default_text',[$t('lead.last_digits'),])" />
                        </a-form-item>
                    </a-col>
                    <a-col :xs="24" :sm="24" :md="8" :lg="8">
                        <a-form-item
                            :label="$t('lead.month_charge')"
                            name="emisor"
                            :help="rules.emisor ? $t('lead.month_charge') : null"
                            :validateStatus="rules.emisor ? 'error' : null"
                        >
                            <a-select  v-model:value="formData.mes_carga"
                                :placeholder="$t('common.select_default_text', [$t('lead.month_charge')])">
                                <a-select-option value="1">{{ $t('common.january') }}</a-select-option>
                                <a-select-option value="2">{{ $t('common.february') }}</a-select-option>
                                <a-select-option value="3">{{ $t('common.march') }}</a-select-option>
                                <a-select-option value="4">{{ $t('common.april') }}</a-select-option>
                                <a-select-option value="5">{{ $t('common.may') }}</a-select-option>
                                <a-select-option value="6">{{ $t('common.june') }}</a-select-option>
                                <a-select-option value="7">{{ $t('common.july') }}</a-select-option>
                                <a-select-option value="8">{{ $t('common.august') }}</a-select-option>
                                <a-select-option value="9">{{ $t('common.september') }}</a-select-option>
                                <a-select-option value="10">{{ $t('common.october') }}</a-select-option>
                                <a-select-option value="11">{{ $t('common.november') }}</a-select-option>
                                <a-select-option value="12">{{ $t('common.december') }}</a-select-option>
                            </a-select>
                        </a-form-item>
                    </a-col>
                    <a-config-provider :locale="antdLocale">
                        <a-col :xs="24" :sm="24" :md="8" :lg="8">
                            <a-form-item
                                :label="$t('lead.year_charge')"
                                name="anno_carga"
                                :help="rules.anno_carga ? $t('lead.year_charge') : null"
                                :validateStatus="rules.anno_carga ? 'error' : null"
                            >
                                <a-date-picker format="YYYY"
                                    value-format="YYYY" v-model:value="formData.anno_carga"
                                    style="width: 100%" />
                            </a-form-item>
                        </a-col>
                    </a-config-provider>
                </a-row>
                <!-- provincia de voto y foco venta -->
                <a-row :gutter="16">
                    <a-col :xs="24" :sm="24" :md="12" :lg="12">
                        <a-form-item
                            :label="$t('lead.electoral_province')"
                            name="provincia_voto"
                            :help="rules.provincia_voto ? $t('lead.electoral_province') : null"
                            :validateStatus="rules.provincia_voto ? 'error' : null"
                        >
                            <a-input v-model:value="formData.provincia_voto"
                                :placeholder="$t('common.placeholder_default_text',[$t('lead.electoral_province'),])" />
                        </a-form-item>
                    </a-col>
                    <a-col :xs="24" :sm="24" :md="12" :lg="12">
                        <a-form-item
                            :label="$t('lead.sales_focus')"
                            name="foco_venta"
                            :help="rules.foco_venta ? $t('lead.sales_focus') : null"
                            :validateStatus="rules.foco_venta ? 'error' : null"
                        >
                            <a-input v-model:value="formData.foco_venta"
                                :placeholder="$t('common.placeholder_default_text',[$t('lead.sales_focus'),])" />
                        </a-form-item>
                    </a-col>
                </a-row>
                <!-- hijos y fecha de nacimiento y edad -->
                <a-row :gutter="16">
                    <a-col :xs="24" :sm="24" :md="8" :lg="8">
                        <a-form-item
                            :label="$t('lead.children')"
                            name="hijos"
                            :help="rules.hijos ? $t('lead.children') : null"
                            :validateStatus="rules.hijos ? 'error' : null"
                        >
                        <a-select v-model:value="formData.hijos" style="width: 100%"
                            :placeholder="$t('common.select_default_text')">
                            <a-select-option :value="0">
                                {{ $t('common.no') }}
                            </a-select-option>
                            <a-select-option :value="1">
                                {{ $t('common.yes') }}
                            </a-select-option>
                        </a-select>
                        </a-form-item>
                    </a-col>
                    <a-config-provider :locale="antdLocale">
                        <a-col :xs="24" :sm="24" :md="8" :lg="8">
                            <a-form-item
                                :label="$t('lead.date_birth')"
                                name="fechaNacimiento"
                                :help="rules.fechaNacimiento ? $t('lead.date_birth') : null"
                                :validateStatus="rules.fechaNacimiento ? 'error' : null"
                            >
                                <a-date-picker
                                    format="YYYY-MM-DD" value-format="YYYY-MM-DD"
                                    @change="calcularEdad"
                                    v-model:value="formData.fechaNacimiento"
                                    style="width: 100%" />
                            </a-form-item>
                        </a-col>
                    </a-config-provider>
                    <a-col :xs="24" :sm="24" :md="8" :lg="8">
                        <a-form-item
                            :label="$t('lead.age')"
                            name="edad"
                            :help="rules.edad ? $t('lead.age') : null"
                            :validateStatus="rules.edad ? 'error' : null"
                        >
                            <a-input-number v-model:value="formData.edad"
                                :placeholder="$t('common.placeholder_default_text',[$t('lead.age'),])" style="width: 100%"/>
                        </a-form-item>
                    </a-col>
                </a-row>
                <!-- genero y nacionalidad -->
                <a-row :gutter="16">
                    <a-col :xs="24" :sm="24" :md="12" :lg="12">
                        <a-form-item
                            :label="$t('lead.gender')"
                            name="genero"
                            :help="rules.genero ? $t('lead.gender') : null"
                            :validateStatus="rules.genero ? 'error' : null"
                        >
                            <a-input v-model:value="formData.genero"
                                :placeholder="$t('common.placeholder_default_text',[$t('lead.gender'),])" />
                        </a-form-item>
                    </a-col>
                    <a-col :xs="24" :sm="24" :md="12" :lg="12">
                        <a-form-item
                            :label="$t('lead.nationality')"
                            name="nacionalidad"
                            :help="rules.nacionalidad ? $t('lead.nationality') : null"
                            :validateStatus="rules.nacionalidad ? 'error' : null"
                        >
                            <a-input v-model:value="formData.nacionalidad"
                                :placeholder="$t('common.placeholder_default_text',[$t('lead.nationality'),])" />
                        </a-form-item>
                    </a-col>
                </a-row>
                <!-- tel 1 tel 2 tel 3 -->
                <a-row :gutter="16">
                    <a-col :xs="24" :sm="24" :md="8" :lg="8">
                        <a-form-item
                            :label="`${$t('lead.phone')} 1`"
                            name="phone"
                            :help="rules.tel1 ? rules.tel1.message : null"
                            :validateStatus="rules.tel1 ? 'error' : null"
                            class="required"
                        >
                        <a-input v-model:value="formData.tel1"
                                :placeholder="$t('common.placeholder_default_text',[$t('lead.phone'),])" />
                        </a-form-item>
                    </a-col>
                    <a-col :xs="24" :sm="24" :md="8" :lg="8">
                        <a-form-item
                            :label="`${$t('lead.phone')} 2`"
                            name="phone2"
                        >
                        <a-input v-model:value="formData.tel2"
                                :placeholder="$t('common.placeholder_default_text',[$t('lead.phone'),])" />
                        </a-form-item>
                    </a-col>
                    <a-col :xs="24" :sm="24" :md="8" :lg="8">
                        <a-form-item
                            :label="`${$t('lead.phone')} 3`"
                            name="phone3"
                        >
                        <a-input v-model:value="formData.tel3"
                                :placeholder="$t('common.placeholder_default_text',[$t('lead.phone'),])" />
                        </a-form-item>
                    </a-col>
                </a-row>
                <!-- tel 4 tel 5 tel 6 -->
                <a-row :gutter="16">
                    <a-col :xs="24" :sm="24" :md="8" :lg="8">
                        <a-form-item
                            :label="`${$t('lead.phone')} 4`"
                            name="phone4"
                        >
                        <a-input v-model:value="formData.tel4"
                                :placeholder="$t('common.placeholder_default_text',[$t('lead.phone'),])" />
                        </a-form-item>
                    </a-col>
                    <a-col :xs="24" :sm="24" :md="8" :lg="8">
                        <a-form-item
                            :label="`${$t('lead.phone')} 5`"
                            name="phone5"
                        >
                        <a-input v-model:value="formData.tel5"
                                :placeholder="$t('common.placeholder_default_text',[$t('lead.phone'),])" />
                        </a-form-item>
                    </a-col>
                    <a-col :xs="24" :sm="24" :md="8" :lg="8">
                        <a-form-item
                            :label="`${$t('lead.phone')} 6`"
                            name="phone6"
                        >
                        <a-input v-model:value="formData.tel6"
                                :placeholder="$t('common.placeholder_default_text',[$t('lead.phone'),])" />
                        </a-form-item>
                    </a-col>
                </a-row>
                <a-row :gutter="16">
                    <a-col :xs="24" :sm="24" :md="24" :lg="24">
                        <a-form-item
                            :label="$t('lead.email')"
                            name="email"
                        >
                        <a-input v-model:value="formData.email"
                                :placeholder="$t('common.placeholder_default_text',[$t('lead.email'),])" />
                        </a-form-item>
                    </a-col>
                </a-row>
                
            </a-form>
            <template #footer>
                <a-row justify="center" align="middle">
                    <a-col>
                        <a-space size="middle">
                            <a-button
                            key="submit"
                            type="primary"
                            :loading="loading"
                            @click="onSubmit"
                        >
                            <template #icon><SaveOutlined /></template>
                                {{ $t("common.create") }}
                            </a-button>
                            <a-button key="back" @click="onClose">
                                {{ $t("common.cancel") }}
                            </a-button>
                        </a-space>
                    </a-col>
                </a-row>
            </template>
        </a-drawer>
    </div>
</template>
<script>
import { defineComponent, ref, onMounted, watch,computed } from "vue";
import { SaveOutlined } from "@ant-design/icons-vue";
import { forEach } from "lodash-es";
import { useI18n } from "vue-i18n";
import common from "../../../common/composable/common";
import apiAdmin from "../../../common/composable/apiAdmin";
import esES from 'ant-design-vue/es/locale/es_ES';
import enUS from 'ant-design-vue/es/locale/en_US';
import dayjs from 'dayjs';

export default defineComponent({
    props: {
        btnType: {
            default: "default",
        },
        btnClass: {
            default: "",
        },
        tooltip: {
            default: true,
        },
        campaign: {
            default: {},
        },
    },
    emits: ["success"],
    components: {
        SaveOutlined,
    },
    setup(props, { emit }) {
        const { permsArray } = common();
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const visible = ref(false);
        const formData = ref({});
        const users = ref([]);
        const campaignUser = ref([]);

        const { t,locale } = useI18n();

        const antdLocale = computed(() =>
            locale.value === 'en' ? enUS : esES
        );
        watch(locale, (newLang) => {
            dayjs.locale(newLang);
        });

        onMounted(() => {
            resetFormData();
        });

        const resetFormData = () => {
            var newLeadDataArray = [];

            if (
                props.campaign &&
                props.campaign.form &&
                props.campaign.form.form_fields
            ) {
                forEach(props.campaign.form.form_fields, (fieldValue) => {
                    newLeadDataArray.push({
                        id: Math.random().toString(36).slice(2),
                        field_name: fieldValue.name,
                        field_value: "",
                    });
                });
            }
            users.value = [];
            campaignUser.value = props.campaign.campaign_users;
            forEach(campaignUser.value, (campaign) => {
                if (campaign.user) {
                    users.value.push(campaign.user);
                }
            });

            formData.value = {
                campaign_id: props.campaign.xid,
                lead_data: newLeadDataArray,
                assign_to: null,
            };
        };

        const showAdd = () => {
            visible.value = true;
        };

        const onSubmit = async () => {
            addEditRequestAdmin({
                url: "leads/create-lead",
                data: formData.value,
                successMessage: t("lead.created"),
                success: (res) => {
                    emit("success");
                    onClose();
                },
            });
        };

        const onClose = () => {
            resetFormData();
            visible.value = false;
        };

        function calcularEdad() {
            const fechaNacimiento = formData.value.fechaNacimiento;
            if (!fechaNacimiento) return null;

            const today = dayjs();
            const birthDate = dayjs(fechaNacimiento);
            let edad = today.year() - birthDate.year();

            if (
                today.month() < birthDate.month() ||
                (today.month() === birthDate.month() && today.date() < birthDate.date())
            ) {
                edad--;
            }

            formData.value.edad = edad;
        }

        return {
            antdLocale,
            calcularEdad,
            permsArray,
            visible,
            formData,
            loading,
            rules,
            users,
            campaignUser,
            onSubmit,
            onClose,
            showAdd,

            drawerWidth: window.innerWidth <= 991 ? "90%" : "50%",
        };
    },
});
</script>
