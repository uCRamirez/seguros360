<template>
    <a-tooltip :title="$t('lead.send_email')">
        <a-button type="primary" @click="sendEmail">
            <template #icon>
                <MailOutlined />
            </template>
        </a-button>
    </a-tooltip>

    <a-drawer
        :title="$t('lead.send_email')"
        :width="drawerWidth"
        :open="visible"
        :body-style="{ paddingBottom: '80px' }"
        :footer-style="{ textAlign: 'right' }"
        :maskClosable="false"
        @close="hideModal"
    >
        <a-form layout="vertical">
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('email_template.email_provider')"
                        name="email_provider_id"
                        :help="
                            rules.email_provider_id
                                ? rules.email_provider_id.message
                                : null
                        "
                        :validateStatus="rules.email_provider_id ? 'error' : null"
                        class="required"
                    >
                        <span style="display: flex">
                            <a-select
                                v-model:value="formData.email_provider_id"
                                :placeholder="
                                    $t('common.select_default_text', [
                                        $t('email_template.email_provider'),
                                    ])
                                "
                            >
                                <a-select-option
                                    v-for="emailProvider in emailProviders"
                                    :key="emailProvider.xid"
                                    :value="emailProvider.xid"
                                >
                                    {{ emailProvider.name }}
                                </a-select-option>
                            </a-select>
                            <EmailProviderAddButton @onAddSuccess="emailProviderAdded" />
                        </span>
                    </a-form-item>
                </a-col>
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('lead.campaign')"
                        name="campaign"
                        :help="rules.campaign ? rules.campaign.message : null"
                        :validateStatus="rules.campaign ? 'error' : null"
                        class="required"
                    >
                        <span style="display: flex">
                            <a-select
                                v-model:value="formData.campaign"
                                :placeholder="
                                    $t('common.select_default_text', [
                                        $t('lead.campaign'),
                                    ])
                                "
                                :allowClear="true"
                            >
                                <a-select-option
                                    v-for="campaign in campaigns"
                                    :value="campaign"
                                >
                                    {{ campaign }}
                                </a-select-option>
                            </a-select>
                        </span>
                    </a-form-item>
                </a-col>
            </a-row>

            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('campaign.email_template')"
                        name="email_template_id"
                        :help="
                            rules.email_template_id
                                ? rules.email_template_id.message
                                : null
                        "
                        :validateStatus="rules.email_template_id ? 'error' : null"
                        class="required"
                    >
                        <span style="display: flex">
                            <a-select
                                v-model:value="formData.email_template_id"
                                :placeholder="
                                    $t('common.select_default_text', [
                                        $t('campaign.email_template'),
                                    ])
                                "
                                @change="emailTemplateChanged"
                            >
                                <a-select-option
                                    v-for="allEmailTemplate in allEmailTemplates"
                                    :key="allEmailTemplate.xid"
                                    :value="allEmailTemplate.xid"
                                >
                                    {{ allEmailTemplate.name }}
                                </a-select-option>
                            </a-select>
                            <EmailTemplateAddButton @onAddSuccess="emailTemplateAdded" />
                        </span>
                    </a-form-item>
                </a-col>
            </a-row>

            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('common.email')"
                        name="email"
                        :help="rules.email ? rules.email.message : null"
                        :validateStatus="rules.email ? 'error' : null"
                        class="required"
                    >
                        <a-input
                            v-model:value="formData.email"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('common.email'),
                                ])
                            "
                        />
                    </a-form-item>
                </a-col>
            </a-row>
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('email_template.subject')"
                        name="subject"
                        :help="rules.subject ? rules.subject.message : null"
                        :validateStatus="rules.subject ? 'error' : null"
                        class="required"
                    >
                        <a-input
                            v-model:value="formData.subject"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('email_template.subject'),
                                ])
                            "
                        />
                    </a-form-item>
                </a-col>
            </a-row>
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('email_template.message')"
                        name="message"
                        :help="rules.message ? rules.message.message : null"
                        :validateStatus="rules.message ? 'error' : null"
                        class="required"
                    >
                        <QuillEditor
                            ref="textEditor"
                            v-model:content="formData.message"
                            :content="formData.message"
                            contentType="html"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('email_template.message'),
                                ])
                            "
                            style="height: 200px"
                        />
                    </a-form-item>
                </a-col>
            </a-row>
            <a-row
                :gutter="16"
                v-if="
                    lead &&
                    lead.campaign &&
                    lead.campaign.form &&
                    lead.campaign.form.form_fields &&
                    lead.campaign.form.form_fields.length > 0
                "
                class="mb-20"
            >
                <a-col
                    :xs="8"
                    :sm="8"
                    :md="6"
                    :lg="4"
                    v-for="selectedFormField in lead.campaign.form.form_fields"
                    :key="selectedFormField.id"
                >
                    <a-button
                        @click="insertFormText(selectedFormField.name)"
                        type="link"
                        style="padding: 0px"
                    >
                        {{ selectedFormField.name }}
                    </a-button>
                </a-col>
            </a-row>

            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item>
                        <a-checkbox
                            style="width: 75%"
                            v-model:checked="formData.is_schedule"
                            >{{ $t("email_template.is_schedule") }}</a-checkbox
                        >
                    </a-form-item>
                </a-col>
            </a-row>

            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24" v-if="formData.is_schedule">
                    <a-form-item
                        :label="$t('email_template.date_time')"
                        name="date_time"
                        :help="rules.date_time ? rules.date_time.message : null"
                        :validateStatus="rules.date_time ? 'error' : null"
                        class="required"
                    >
                        <DateTimePicker
                            :dateTime="formData.date_time"
                            @dateTimeChanged="
                                (changedDateTime) =>
                                    (formData.date_time = changedDateTime)
                            "
                            :isFutureDateDisabled="false"
                        />
                    </a-form-item>
                </a-col>
            </a-row>
        </a-form>
        <template #footer>
            <a-space>
                <a-button
                    key="submit"
                    type="primary"
                    :loading="loading"
                    @click="onSubmit"
                >
                    <template #icon>
                        <SendOutlined />
                    </template>
                    {{ $t("common.send") }}
                </a-button>
                <a-button key="back" @click="hideModal">
                    {{ $t("common.cancel") }}
                </a-button>
            </a-space>
        </template>
    </a-drawer>
</template>

<script>
import { defineComponent, ref, onMounted, computed, watch } from "vue";
import { SendOutlined, MailOutlined } from "@ant-design/icons-vue";
import { useI18n } from "vue-i18n";
import { notification } from "ant-design-vue";
import { find, forEach, replace, filter } from "lodash-es";
import { QuillEditor } from "@vueup/vue-quill";
import "@vueup/vue-quill/dist/vue-quill.snow.css";
import apiAdmin from "../../../../../../../common/composable/apiAdmin";
import EmailTemplateAddButton from "../../../../../messaging/email-templates/AddButton.vue";
import EmailProviderAddButton from "../../../../../settings/email-providers/AddButton.vue";
import DateTimePicker from "../../../../../../../common/components/common/calendar/DateTimePicker.vue";

export default defineComponent({
    props: {
        lead: {
            default: {},
        },
        leadFormData: {
            default: {},
        },
        email: {
            default: null,
        },
        campaigns: {
            default: [],
        },
    },
    emits: ["success"],
    components: {
        SendOutlined,
        MailOutlined,

        EmailTemplateAddButton,
        EmailProviderAddButton,
        QuillEditor,
        DateTimePicker,
    },
    setup(props, { emit }) {
        const { t } = useI18n();
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const visible = ref(false);
        const formData = ref({
            lead_id: props.lead.xid,
            email_template_id: undefined,
            email: props.email,
            subject: "",
            message: "",
            email_provider_id: undefined,
            campaign: undefined,
            date_time: undefined,
            is_schedule: false,
        });
        const emailTemplateUrl = "email-templates/all";
        const allEmailTemplates = ref([]);
        const emailProviders = ref([]);
        const emailProviderUrl = "email-providers?limit=10000";
        const textEditor = ref(null);

        onMounted(() => {
            const emailTemplatesPromise = axiosAdmin.get(emailTemplateUrl);
            const emailProviderPromise = axiosAdmin.get(emailProviderUrl);

            Promise.all([emailTemplatesPromise, emailProviderPromise]).then(
                ([emailTemplatesResponse, emailProviderResponse]) => {
                    allEmailTemplates.value = emailTemplatesResponse.data.email_templates;
                    emailProviders.value = emailProviderResponse.data;
                    resetToCampaignEmailTemplate();
                    emailTemplateChanged();
                }
            );
        });

        const editor = computed(() => {
            return textEditor.value.getQuill();
        });

        const insertFormText = (mergeFieldText) => {
            const selectedPointArray = editor.value.getSelection(true);

            var leadDataInleadDataField = find(props.leadFormData.lead_data, [
                "field_name",
                mergeFieldText,
            ]);

            if (
                leadDataInleadDataField != undefined &&
                leadDataInleadDataField.field_value != undefined &&
                leadDataInleadDataField.field_value != ""
            ) {
                var newFieldTextValue =
                    selectedPointArray.length > 0
                        ? `${leadDataInleadDataField.field_value}`
                        : `${leadDataInleadDataField.field_value}`;
            } else {
                var newFieldTextValue =
                    selectedPointArray.length > 0
                        ? `##${insertedTextField}##`
                        : `##${insertedTextField}##`;
            }

            editor.value.deleteText(selectedPointArray.index, selectedPointArray.length);
            editor.value.insertText(selectedPointArray.index, newFieldTextValue);
        };

        const resetToCampaignEmailTemplate = () => {
            if (
                props.lead &&
                props.lead.campaign &&
                props.lead.campaign.email_template &&
                props.lead.campaign.email_template.xid
            ) {
                formData.value = {
                    ...formData.value,
                    email_template_id: props.lead.campaign.email_template.xid,
                };
            }
        };

        const emailTemplateChanged = () => {
            const selectedEmailTemplate = find(allEmailTemplates.value, [
                "xid",
                formData.value.email_template_id,
            ]);

            if (selectedEmailTemplate && selectedEmailTemplate.body) {
                var bodyMessage = selectedEmailTemplate.body;

                forEach(props.leadFormData.lead_data, (leadDataValue, leadDataKey) => {
                    if (
                        leadDataValue.field_value != undefined &&
                        leadDataValue.field_value != ""
                    ) {
                        bodyMessage = replace(
                            bodyMessage,
                            `##${leadDataValue.field_name}##`,
                            leadDataValue.field_value
                        );
                    }
                });

                formData.value = {
                    ...formData.value,
                    message: bodyMessage,
                    subject: selectedEmailTemplate.subject,
                };

                // Not execute at onMounted
                if (textEditor.value != undefined) {
                    textEditor.value.setHTML(bodyMessage);
                }
            }
        };

        const emailTemplateAdded = () => {
            axiosAdmin.get(emailTemplateUrl).then((response) => {
                allEmailTemplates.value = response.data.email_templates;
            });
        };

        const emailProviderAdded = () => {
            axiosAdmin.get(emailProviderUrl).then((response) => {
                emailProviders.value = response.data;
            });
        };

        const sendEmail = () => {
            formData.value = {
                ...formData.value,
                email: props.email,
            };

            emailTemplateChanged();

            visible.value = true;
        };

        const onSubmit = () => {
            addEditRequestAdmin({
                url: "leads/send-email",
                data: formData.value,
                success: (res) => {
                    if (res && res.success) {
                        visible.value = false;
                        notification.success({
                            message: t("common.success"),
                            description: t("email_template.mail_send_successfully"),
                            placement: "bottomRight",
                        });
                        emit("success");
                        formData.value = {
                            email_provider_id: undefined,
                            campaign: undefined,
                            date_time: undefined,
                        };
                    } else {
                        notification.error({
                            message: t("common.error"),
                            description: t("email_template.mail_send_error_message"),
                            placement: "bottomRight",
                        });
                    }
                },
            });
        };

        const hideModal = () => {
            visible.value = false;
            loading.value = false;
            rules.value = {};

            resetToCampaignEmailTemplate();
        };

        watch(
            () => visible.value,
            (isVisible) => {
                if (isVisible) {
                    formData.value.lead_id = props.lead.xid;
                }
            }
        );

        return {
            loading,
            visible,
            rules,
            formData,
            emailProviders,
            sendEmail,
            onSubmit,
            hideModal,

            textEditor,
            allEmailTemplates,
            emailTemplateAdded,
            emailTemplateChanged,
            insertFormText,
            emailProviderAdded,
            drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
        };
    },
});
</script>
