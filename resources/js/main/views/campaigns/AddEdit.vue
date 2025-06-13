<template>
    <a-drawer :title="pageTitle" :width="drawerWidth" :open="visible" :body-style="{ paddingBottom: '80px' }"
        :footer-style="{ textAlign: 'right' }" :maskClosable="false" @close="onClose">
        <a-form layout="vertical">
            <a-steps :current="currentStep" type="navigation">
                <a-step :title="$t('campaign.basic_settings')" :description="$t('campaign.basic_settings_description')">
                    <template #icon>
                        <FileTextOutlined />
                    </template>
                </a-step>
                <a-step :title="$t('campaign.about_campaign')" :description="$t('campaign.about_campaign_description')">
                    <template #icon>
                        <OrderedListOutlined />
                    </template>
                </a-step>
                <a-step :title="$t('campaign.import_leads')" :description="$t('campaign.import_leads_description')" />
            </a-steps>

            <a-divider />

            <template v-if="currentStep == 0">
                <a-row :gutter="16" class="mt-20">
                    <a-col :xs="24" :sm="24" :md="24" :lg="24">
                        <a-form-item
                            :label="$t('product.image')"
                            name="image"
                            :help="rules.image ? rules.image.message : null"
                            :validateStatus="rules.image ? 'error' : null"
                        >
                            <Upload
                                :formData="formData"
                                folder="campaign"
                                imageField="image"
                                @onFileUploaded="
                                    (file) => {
                                        formData.image = file.file;
                                        formData.image_url = file.file_url;
                                    }
                                "
                            />
                        </a-form-item>
                    </a-col>
                    <a-col :xs="24" :sm="24" :md="24" :lg="24">
                        <a-form-item :label="$t('campaign.name')" name="name"
                            :help="rules.name ? rules.name.message : null" :validateStatus="rules.name ? 'error' : null"
                            class="required">
                            <a-input v-model:value="formData.name" :placeholder="$t('common.placeholder_default_text', [
                                $t('campaign.name'),
                            ])
                                " />
                        </a-form-item>
                    </a-col>
                </a-row>

                <a-row :gutter="16">
                    <a-col :xs="24" :sm="24" :md="24" :lg="24">
                        <a-form-item :label="$t('campaign.members')" name="user_id"
                            :help="rules.user_id ? rules.user_id.message : null"
                            :validateStatus="rules.user_id ? 'error' : null" class="required">
                            <span style="display: flex">
                                <a-select v-model:value="formData.user_id" mode="multiple" :placeholder="$t('common.select_default_text', [
                                    $t('campaign.members'),
                                ])
                                    " :allowClear="true">
                                    <a-select-option v-for="allStaffMember in allStaffMembers" :key="allStaffMember.xid"
                                        :value="allStaffMember.xid">
                                        {{ allStaffMember.name }}
                                    </a-select-option>
                                </a-select>
                                <StaffMemberAddButton @onAddSuccess="staffMemberAdded" />
                            </span>
                        </a-form-item>
                    </a-col>
                </a-row>

                <a-row :gutter="16">
                    <a-col :xs="24" :sm="24" :md="24" :lg="24">
                        <a-form-item :label="$t('menu.quality')" 
                            name="plantilla_calidad_id"
                            :help="rules.plantilla_calidad_id ? rules.plantilla_calidad_id.message : null"
                            :validateStatus="rules.plantilla_calidad_id ? 'error' : null">
                            <span style="display: flex">
                                <a-select v-model:value="formData.plantilla_calidad_id" :placeholder="$t('common.select_default_text', [
                                    $t('common.template'),
                                ])
                                    " :allowClear="true">
                                    <a-select-option v-for="calidadTemplate in allCalidadTemplates" 
                                        :key="calidadTemplate.xid"
                                        :value="calidadTemplate.xid">
                                        {{ calidadTemplate.nombre }} - <small>{{ calidadTemplate.descripcion }}</small>
                                    </a-select-option>
                                </a-select>
                            </span>
                        </a-form-item>
                    </a-col>
                </a-row>
            </template>

            <template v-if="currentStep == 1">
                <a-alert v-if="
                    !formData.detail_fields ||
                    (formData.detail_fields && formData.detail_fields.length == 0)
                " class="mt-20" :description="$t('campaign.add_detail_field_description')" type="info" show-icon />

                <a-alert v-if="
                    formData.detail_fields &&
                    formData.detail_fields.length > 0 &&
                    addFieldsButtonStatus
                " class="mt-20" :description="$t('campaign.add_detail_field_error')" type="error" show-icon />

                <div class="mt-20">
                    <a-row :gutter="16" v-for="(detailField, index) in formData.detail_fields" :key="detailField.id">
                        <a-col :span="7">
                            <a-form-item :name="['detail_fields', index, 'field_name']">
                                <a-input v-model:value="detailField.field_name"
                                    :placeholder="$t('campaign.field_name')" />
                            </a-form-item>
                        </a-col>
                        <a-col :span="7">
                            <a-form-item :name="['detail_fields', index, 'field_value']">
                                <a-input v-model:value="detailField.field_value"
                                    :placeholder="$t('campaign.field_value')" />
                            </a-form-item>
                        </a-col>
                        <a-col :span="7">
                            <a-form-item :name="['detail_fields', index, 'field_type']">
                                <a-select v-model:value="detailField.field_type" :placeholder="$t('common.select_default_text', [
                                    $t('campaign.field_type'),
                                ])
                                    " :allowClear="true">
                                    <a-select-option value="text">
                                        {{ $t("common.text") }}
                                    </a-select-option>
                                    <a-select-option value="link">
                                        {{ $t("common.link") }}
                                    </a-select-option>
                                </a-select>
                            </a-form-item>
                        </a-col>
                        <a-col :span="3">
                            <MinusCircleOutlined @click="removeDetailField(detailField)" />
                        </a-col>
                    </a-row>
                </div>

                <a-row :gutter="16">
                    <a-col :xs="24" :sm="24" :md="24" :lg="24">
                        <a-form-item>
                            <a-button type="dashed" block @click="addDetailField" :disabled="addFieldsButtonStatus">
                                <PlusOutlined />
                                {{ $t("campaign.add_detail_field") }}
                            </a-button>
                        </a-form-item>
                    </a-col>
                </a-row>
            </template>

            <template v-if="currentStep == 2">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('bases.base') + ' ' + $t('bases.stage')"
                        name="stage"
                        :help="
                            rules.stage
                                ? rules.stage.message
                                : null
                        "
                        :validateStatus="
                            rules.stage ? 'error' : null
                        "
                        class="required"
                    >
                        <a-select
                            v-model:value="formData.etapa"
                            :placeholder="$t(
                            'common.select_default_text',
                            [ $t('bases.base') + ' ' + $t('bases.stage') ]
                            )" 
                        >
                            <a-select-option
                                key="nueva"
                                value="nueva"
                            >
                                {{ $t("bases.new") }}
                            </a-select-option>
                            <a-select-option
                                key="reproceso"
                                value="reproceso"
                            >
                                {{ $t("bases.reprocessing") }}
                            </a-select-option>
                            <a-select-option
                                key="na"
                                value="na"
                            >
                                {{ $t("bases.na") }}
                            </a-select-option>
                        </a-select>
                    </a-form-item>
                </a-col>
                <ImportLeads v-if="permsArray.includes('bases_view') || permsArray.includes('admin')" 
                    acceptFormat=".csv" :allFields="selectedFormFields" @fileUploaded="leadFileUploaded"
                    @leadColumnChanged="leadColumnChanged" />
                <h4 class="text-center" v-else>{{ $t('bases.not_permission') }}</h4>
            </template>
        </a-form>
        <template #footer>
            <a-space>
                <a-button v-if="currentStep != 0" key="submit" type="primary" :loading="loading" @click="goBack">
                    <DoubleLeftOutlined />
                    {{ $t("campaign.previous_step") }}
                </a-button>
                <a-button v-if="currentStep != 2" key="submit" type="primary" :loading="loading" @click="onSubmit"
                    :disabled="currentStep == 1 && addFieldsButtonStatus">
                    {{ $t("campaign.next_step") }}
                    <DoubleRightOutlined />
                </a-button>
                <a-button v-if="currentStep == 2" key="submit" type="primary" :loading="loading" @click="submitForm">
                    <template #icon>
                        <SaveOutlined />
                    </template>
                    {{ addEditType == "add" ? $t("common.create") : $t("common.update") }}
                </a-button>
                <a-button key="back" @click="onClose">
                    {{ $t("common.cancel") }}
                </a-button>
            </a-space>
        </template>
    </a-drawer>
</template>
<script>
import { defineComponent, ref, onMounted, computed, watch } from "vue";
import {
    PlusOutlined,
    LoadingOutlined,
    SaveOutlined,
    FileTextOutlined,
    OrderedListOutlined,
    DoubleRightOutlined,
    DoubleLeftOutlined,
    MinusCircleOutlined,
} from "@ant-design/icons-vue";
import { some } from "lodash-es";
import apiAdmin from "../../../common/composable/apiAdmin";
import StaffMemberAddButton from "../users/StaffAddButton.vue";
import FormAddButton from "../forms/forms/AddButton.vue";
import EmailTemplateAddButton from "../messaging/email-templates/AddButton.vue";
import ImportLeads from "./ImportLeads.vue";
import { useI18n } from "vue-i18n";
import Upload from "../../../common/core/ui/file/Upload.vue";


export default defineComponent({
    props: [
        "permsArray",
        "formData",
        "data",
        "visible",
        "url",
        "addEditType",
        "pageTitle",
        "successMessage",
    ],
    components: {
        PlusOutlined,
        LoadingOutlined,
        SaveOutlined,
        FileTextOutlined,
        OrderedListOutlined,
        DoubleRightOutlined,
        DoubleLeftOutlined,
        MinusCircleOutlined,
        Upload,

        ImportLeads,
        StaffMemberAddButton,
        FormAddButton,
        EmailTemplateAddButton,
    },
    setup(props, { emit }) {
        const { t } = useI18n();
        const currentStep = ref(0);
        const {
            addEditFileRequestAdmin,
            addEditRequestAdmin,
            loading,
            rules,
        } = apiAdmin();
        const formUrl = "forms?fields=xid,name,form_fields&limit=10000";
        const emailTemplateUrl = "email-templates/all";
        const allForms = ref([]);
        const allStaffMembers = ref([]);
        const allCalidadTemplates = ref([]);
        const all_uc_campaigns = ref([]); // Todas las de uC
        const all_use_uc_campaigns = ref([]); // Todas las que estan en uso en leadProd
        const allEmailTemplates = ref([]);
        const staffMembersUrl = "users?limit=10000";
        const urlCalidadTemplates = "plantillas-calidad?filters=activo eq 1&limit=1000";
        const leadFile = ref(undefined);
        const importLeadColumns = ref(undefined);
        const selectedFormFields = ref([]);
        const isChecked = ref(false);
        const none_uc_campaigns = ref({}); 

        onMounted(() => {
            props.formData.etapa = 'nueva';
            const staffMemberPromise = axiosAdmin.get(staffMembersUrl);
            const calidadTemplatesPromise = axiosAdmin.get(urlCalidadTemplates);
            // const all_uc_campaignsPromise = axiosAdmin.post('campaigns/uc-campaigns');
            // const all_use_uc_campaignsPromise = axiosAdmin.post('campaigns/use-uc-campaigns');
            // const formsPromise = axiosAdmin.get(formUrl);
            // const emailTemplatesPromise = axiosAdmin.get(emailTemplateUrl);

            Promise.all([staffMemberPromise, calidadTemplatesPromise]).then(//formsPromise, all_uc_campaignsPromise, all_use_uc_campaignsPromise, emailTemplatesPromise
                ([staffMemberResponse, calidadTemplatesResponse]) => {//formsResponse, all_uc_campaignsResponse, all_use_uc_campaignsResponse, emailTemplatesResponse
                    allStaffMembers.value = staffMemberResponse.data;
                    allCalidadTemplates.value = calidadTemplatesResponse.data;
                    // allForms.value = formsResponse.data;
                    // allEmailTemplates.value = emailTemplatesResponse.data.email_templates;
                    // all_uc_campaigns.value = all_uc_campaignsResponse.message.campaigns;
                    // all_use_uc_campaigns.value = all_use_uc_campaignsResponse.message.campaigns;
                }
            );
        });

        const uc_campaignsArray = computed({
            get() {
                if (!props.data.uc_campaigns) return [];
                return props.data.uc_campaigns
                    .split(",")
                    .map(item => item.trim());

            },
            set(newValues) {
                props.data.uc_campaigns = newValues.join(",");
                props.formData.uc_campaigns = newValues.join(",");
            }
        });


        const isCampaignInUse = () => {
            none_uc_campaigns.value = {};

            const allUsedUCs = new Set(); // Para recolectar todas las campañas usadas
            const namedUCs = new Set();   // Para evitar duplicados en Inabilitar

            if(all_use_uc_campaigns.value){
               all_use_uc_campaigns.value.forEach(c => {
                    const ucList = c.uc_campaigns
                        ? c.uc_campaigns.split(',').map(s => s.trim())
                        : [];

                    // Guardamos por nombre
                    none_uc_campaigns.value[c.name] = ucList;

                    // Recolectamos para Inabilitar
                    ucList.forEach(uc => {
                        allUsedUCs.add(uc);
                        namedUCs.add(`${c.name}:${uc}`); // combinación única para filtrar luego
                    });
                }); 
            }
        };

        // para indicar en el select de campanas uc cuales quedan habilitado y cuales no
        const isDisabledCampaign = (queueNameLeadPro, queueNameUC) => {
            // Obtener la lista permitida para la clave dueña actual.
            const allowedList = none_uc_campaigns.value[queueNameLeadPro] || [];
            // Si el valor está en la lista del dueño, no se deshabilita.
            if (allowedList.includes(queueNameUC)) {
                return false;
            }
            // Si no está en la lista del dueño, revisamos las demás claves.
            for (const [key, list] of Object.entries(none_uc_campaigns.value)) {
                if (key !== queueNameLeadPro && list.includes(queueNameUC)) {
                    return true; // pertenece a otra clave: deshabilitar.
                }
            }
            // Si no aparece en ninguna parte, se deja habilitado.
            return false;
        };





        const removeDetailField = (item) => {
            let index = props.formData.detail_fields.indexOf(item);
            if (index !== -1) {
                props.formData.detail_fields.splice(index, 1);
            }
        };

        const addDetailField = () => {
            props.formData.detail_fields.push({
                field_name: "",
                field_value: "",
                field_type: "text",
                id: Math.random().toString(36).slice(2),
            });
        };

        const onSubmit = () => {
            if (currentStep.value == 0) {
                if (
                    props.formData.name == ""
                ) {
                    submitForm();
                } else {
                    rules.value = {};
                    setStep(1);
                }
            } else {
                setStep(currentStep.value + 1);
            }
        };

        const submitForm = () => {
            var newFormData = { ...props.formData };

            if (
                newFormData.user_id == undefined ||
                (newFormData.user_id != undefined &&
                    typeof newFormData.user_id == "object" &&
                    newFormData.user_id.length == 0)
            ) {
                newFormData = { ...props.formData, user_id: undefined };
            }

            if (leadFile.value != undefined) {
                newFormData.file = leadFile.value;
            }

            newFormData.import_lead_fields = importLeadColumns.value;

            let isCSV = newFormData.import_lead_fields ? { 
                    data: newFormData.import_lead_fields, 
                    file: newFormData.file.name,
                    campaign_id: props.data.id,
                    company_id: JSON.parse(localStorage.getItem('global_settings') || '{}').xid || '',
                    myId:  JSON.parse(localStorage.getItem("auth_user") || "{}").id,
                    etapa: newFormData.etapa ? newFormData.etapa : 'nueva',
                } 
                : false;

            addEditFileRequestAdmin({
                url: props.url,
                fieldTypes: {
                    json: ["detail_fields", "user_id", "import_lead_fields"],
                    file: ["file"],
                },
                data: newFormData,
                successMessage: props.successMessage,
                success: (res) => {
                    emit("addEditSuccess", res.xid);
                },
            });

            if (isCSV) {
                addEditRequestAdmin({
                    url: 'leadcsv/push',
                    data: isCSV,
                    successMessage: t('campaign.imported_leads'),
                    success: (res) => {
                        console.log(res);
                    },
                });
            }

            
        };

        const goBack = () => {
            currentStep.value = currentStep.value - 1;
            props.formData.current_step = currentStep.value;
        };

        const onClose = () => {
            rules.value = {};
            emit("closed");
        };

        const staffMemberAdded = () => {
            axiosAdmin.get(staffMembersUrl).then((response) => {
                allStaffMembers.value = response.data;
            });
        };

        const formAdded = () => {
            axiosAdmin.get(formUrl).then((response) => {
                allForms.value = response.data;
            });
        };

        const emailTemplateAdded = () => {
            axiosAdmin.get(emailTemplateUrl).then((response) => {
                allEmailTemplates.value = response.data.email_templates;
            });
        };

        const setStep = (stepNumber) => {
            currentStep.value = stepNumber;
            props.formData.current_step = stepNumber;
        };

        const addFieldsButtonStatus = computed(() => {
            if (
                props.formData.detail_fields &&
                props.formData.detail_fields.length == 0
            ) {
                return false;
            } else {
                return (
                    some(props.formData.detail_fields, { field_name: "" }) ||
                    some(props.formData.detail_fields, { field_value: "" })
                );
            }
        });

        const leadFileUploaded = (file) => {
            leadFile.value = file;
        };

        const leadColumnChanged = (allColumns) => {
            importLeadColumns.value = allColumns;
        };

        const formSelected = () => {
            axiosAdmin.get('columns/leads_aux')
                .then(response => {
                // AxiosAdmin parece devolverte el body ya parseado en `response`
                const payload = response.message ?? response.data ?? response;
                // convierto a array si es objeto
                const allFields = Array.isArray(payload)
                    ? payload
                    : Object.values(payload);
                selectedFormFields.value = allFields;
                })
                .catch(err => {
                console.error('Error cargando columnas', err);
                selectedFormFields.value = [];
            });
        };


        watch(() => props.visible,
            (newVal, oldVal) => {
                // For campaign members
                if (
                    newVal &&
                    props.addEditType == "edit" &&
                    props.data &&
                    props.data.campaign_users &&
                    props.data.campaign_users.length > 0
                ) {
                    props.formData.user_id = props.data.campaign_users.map(function (el) {
                        return el.x_user_id;
                    });
                    props.formData.plantilla_calidad_id = props.data.plantilla_calidad ? props.data.plantilla_calidad.xid : null;

                } else {
                    props.formData.user_id = undefined;
                    props.formData.plantilla_calidad_id = null;
                }

                // For Form Fields
                formSelected();
                props.formData.etapa = 'nueva';

                isChecked.value = uc_campaignsArray.value.length !== 0 ? true : false;

                // se consulta nuevamente las campanas en uso para poder actualizar el front del addEdit
                // axiosAdmin.post('campaigns/use-uc-campaigns').then((response) => {
                //     all_use_uc_campaigns.value = response.message.campaigns;
                //     isCampaignInUse();
                // });

                // se consulta nuevamente las campanas en uso para poder actualizar el front del addEdit
               

                setStep(0);
            }
        );

        return {
            isDisabledCampaign,
            none_uc_campaigns,
            uc_campaignsArray,
            isChecked,
            currentStep,
            loading,
            rules,
            allStaffMembers,
            allCalidadTemplates,
            all_uc_campaigns,
            allForms,
            allEmailTemplates,
            onClose,
            onSubmit,
            staffMemberAdded,
            formAdded,
            emailTemplateAdded,
            goBack,
            submitForm,

            removeDetailField,
            addDetailField,
            addFieldsButtonStatus,

            leadFileUploaded,
            leadColumnChanged,
            selectedFormFields,
            // formSelected,

            drawerWidth: window.innerWidth <= 991 ? "90%" : "60%",
        };
    },
});
</script>
