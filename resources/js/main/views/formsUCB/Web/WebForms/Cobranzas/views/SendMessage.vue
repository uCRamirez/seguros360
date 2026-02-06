<template>
    <a-tooltip :title="$t('lead.send_message')">
        <a-button type="primary" @click="sendMessage" danger>
            <template #icon>
                <CommentOutlined />
            </template>
        </a-button>
    </a-tooltip>

    <a-modal
        :open="visible"
        :closable="false"
        :centered="true"
        :title="`${$t('lead.send_message')} (${fullPhoneNumber})`"
        @ok="hideModal"
    >
        <a-form layout="vertical">
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('lead.phone_number')"
                        name="phone_number"
                        :help="rules.phone_number ? rules.phone_number.message : null"
                        :validateStatus="rules.phone_number ? 'error' : null"
                        class="required"
                    >
                        <a-input-group compact>
                            <a-select
                                style="width: 25%"
                                v-model:value="formData.country_code"
                                :placeholder="
                                    $t('common.select_default_text', [
                                        $t('lead.country_code'),
                                    ])
                                "
                                optionFilterProp="value"
                                show-search
                                :allowClear="true"
                            >
                                <a-select-option
                                    v-for="country in countryCodes"
                                    :key="country.code"
                                    :value="country.code"
                                >
                                    {{ country.code }}
                                </a-select-option>
                            </a-select>
                            <a-input
                                style="width: 75%"
                                v-model:value="formData.phone"
                                :placeholder="
                                    $t('common.placeholder_default_text', [
                                        $t('common.phone_number'),
                                    ])
                                "
                            />
                        </a-input-group>
                    </a-form-item>
                </a-col>
            </a-row>

            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('campaign.message_provider')"
                        name="provider_id"
                        :help="rules.provider_id ? rules.provider_id.message : null"
                        :validateStatus="rules.provider_id ? 'error' : null"
                        class="required"
                    >
                        <span style="display: flex">
                            <a-select
                                v-model:value="formData.provider_id"
                                :placeholder="
                                    $t('common.select_default_text', [
                                        $t('campaign.message_provider'),
                                    ])
                                "
                                @change="getMessageTemplate(formData.provider_id)"
                            >
                                <a-select-option
                                    v-for="allProvider in allProviders"
                                    :key="allProvider.xid"
                                    :value="allProvider.xid"
                                >
                                    {{ allProvider.name }}
                                </a-select-option>
                            </a-select>
                        </span>
                    </a-form-item>
                </a-col>
            </a-row>

            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24" v-if="formData.provider_id">
                    <a-form-item
                        :label="$t('campaign.message_template')"
                        name="message_template_id"
                        :help="
                            rules.message_template_id
                                ? rules.message_template_id.message
                                : null
                        "
                        :validateStatus="rules.message_template_id ? 'error' : null"
                        class="required"
                    >
                        <span style="display: flex">
                            <a-select
                                v-model:value="formData.message_template_id"
                                :placeholder="
                                    $t('common.select_default_text', [
                                        $t('campaign.message_template'),
                                    ])
                                "
                                @change="messageTemplateChanged"
                            >
                                <a-select-option
                                    v-for="allMessageTemplate in messageTemplate"
                                    :key="allMessageTemplate.xid"
                                    :value="allMessageTemplate.xid"
                                >
                                    {{ allMessageTemplate.name }}
                                </a-select-option>
                            </a-select>
                        </span>
                    </a-form-item>
                </a-col>
            </a-row>

            <!-- <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('common.phone')"
                        name="phone"
                        :help="rules.phone ? rules.phone.message : null"
                        :validateStatus="rules.phone ? 'error' : null"
                        class="required"
                    >
                        <a-input
                            v-model:value="formData.phone"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('common.phone'),
                                ])
                            "
                        />
                    </a-form-item>
                </a-col>
            </a-row> -->
            <!-- <a-col :xs="24" :sm="24" :md="24" :lg="24"> -->
            <div v-if="dynamicVariables.length > 0">
                <strong v-if="createSabValues">{{
                    $t("message_template.variables")
                }}</strong>
                <div v-for="(variable, index) in createSabValues" :key="variable.id">
                    <a-row :gutter="16">
                        <a-col :xs="24" :sm="24" :md="12" :lg="12">
                            <li>
                                <a-input
                                    disabled
                                    v-model:value="variable.target"
                                    :placeholder="`Enter value for {{ variable }}`"
                                    style="margin-bottom: 16px"
                                /><br />
                            </li>
                        </a-col>
                        <a-col :xs="24" :sm="24" :md="12" :lg="12">
                            <a-input
                                v-model:value="variable.name"
                                :placeholder="$t('message_template.enter_subvalues')"
                                @change="replaceMessageString"
                            ></a-input>
                        </a-col>
                    </a-row>
                </div>
            </div>

            <!-- </a-col> -->
            <a-row :gutter="16">
                <a-col
                    :xs="24"
                    :sm="24"
                    :md="24"
                    :lg="24"
                    v-if="formData.message_template_id"
                >
                    <a-form-item
                        :label="$t('message_template.message')"
                        name="message"
                        :help="rules.message ? rules.message.message : null"
                        :validateStatus="rules.message ? 'error' : null"
                    >
                        <!-- <QuillEditor
                            ref="textEditor"
                            v-model:content="formData.message"
                            :content="formData.message"
                            contentType="html"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('message_template.message'),
                                ])
                            "
                            style="height: 200px"
                        /> -->

                        {{ messageString }}
                    </a-form-item>
                </a-col>
            </a-row>

            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item>
                        <a-checkbox
                            style="width: 75%"
                            v-model:checked="formData.is_schedule"
                            >{{ $t("campaign.is_schedule") }}</a-checkbox
                        >
                    </a-form-item>
                </a-col>
            </a-row>

            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24" v-if="formData.is_schedule">
                    <a-form-item
                        :label="$t('campaign.date_time')"
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
    </a-modal>
</template>

<script>
import { defineComponent, ref, onMounted, computed, watch } from "vue";
import { SendOutlined, CommentOutlined } from "@ant-design/icons-vue";
import { useI18n } from "vue-i18n";
import { notification } from "ant-design-vue";
import { find, forEach, replace, reduce } from "lodash-es";
import { QuillEditor } from "@vueup/vue-quill";
import "@vueup/vue-quill/dist/vue-quill.snow.css";
import apiAdmin from "../../../../../../../common/composable/apiAdmin";
import EmailTemplateAddButton from "../../../../../messaging/message-templates/AddButton.vue";
import common from "../../../../../../../common/composable/common";
import DateTimePicker from "../../../../../../../common/components/common/calendar/DateTimePicker.vue";

export default defineComponent({
    props: {
        lead: {
            default: {},
        },
        leadFormData: {
            default: {},
        },
        phone: {
            default: null,
        },
    },
    emits: ["success"],
    components: {
        SendOutlined,
        CommentOutlined,

        EmailTemplateAddButton,
        QuillEditor,
        DateTimePicker,
    },
    setup(props, { emit }) {
        const { t } = useI18n();
        const { countryCodes, extractCountryCodeAndNumber } = common();
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const visible = ref(false);
        const formData = ref({
            lead_id: props.lead.xid,
            message_template_id: undefined,
            provider_id: undefined,
            message: "",
            code: "",
            country_code: undefined,
            date_time: undefined,
            is_schedule: false,
        });
        const messageTemplateUrl =
            "message-templates/all?fields=id,xid,name,message_provider_id,x_message_provider_id,code";
        const allMessageTemplates = ref([]);
        const allProviders = ref([]);
        const allProviderUrl = "message-providers/all";
        const textEditor = ref(null);
        const messageTemplate = ref([]);
        const createSabValues = ref([]);
        const messageString = ref("");
        const selectedTempalteMessageString = ref("");
        const selectedTempalteCodeString = ref("");
        const fullPhoneNumber = computed(() => {
            return `${formData.value.country_code}${formData.value.phone}`;
        });

        onMounted(() => {
            const messageTemplatesPromise = axiosAdmin.get(messageTemplateUrl);
            const allProviderPromise = axiosAdmin.get(allProviderUrl);

            Promise.all([messageTemplatesPromise, allProviderPromise]).then(
                ([messageTemplatesResponse, allProviderResponse]) => {
                    allMessageTemplates.value =
                        messageTemplatesResponse.data.message_templates;
                    allProviders.value = allProviderResponse.data.providers;

                    resetToCampaignMessageTemplate();
                    messageTemplateChanged();
                    getMessageTemplate();
                }
            );
        });

        const getMessageTemplate = (providerId) => {
            formData.value.message_template_id = undefined;
            createSabValues.value = undefined;
            messageTemplate.value = [];

            forEach(allMessageTemplates.value, (template) => {
                if (template.x_message_provider_id == providerId) {
                    messageTemplate.value.push(template);
                }
            });
        };

        function extractDynamicVariables(text) {
            // Regular expression to match {{number}} pattern
            const regex = /\{\{(\d+)\}\}/g;
            const variables = [];
            let match;

            // Iterate over all matches in the input text
            while ((match = regex.exec(text)) !== null) {
                // match[1] contains the number inside {{number}}
                variables.push(match[1]);
            }

            return variables;
        }

        // Computed property to extract dynamic variables from the message
        const dynamicVariables = computed(() => {
            return extractDynamicVariables(formData.value.message);
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

        const resetToCampaignMessageTemplate = () => {
            if (
                props.lead &&
                props.lead.campaign &&
                props.lead.campaign.message_template &&
                props.lead.campaign.message_template.xid
            ) {
                formData.value = {
                    ...formData.value,
                    provider_id: props.lead.campaign.message_template.xid,
                };
            }
        };

        const replacePlaceholders = (template, replacements) => {
            return reduce(
                replacements,
                (result, item) => {
                    return result.replace(item.target, item.name);
                },
                template
            );
        };

        const replaceMessageString = () => {
            messageString.value = replacePlaceholders(
                selectedTempalteMessageString.value,
                createSabValues.value
            );
        };

        const messageTemplateChanged = () => {
            const selectedMessageTemplate = find(allMessageTemplates.value, [
                "xid",
                formData.value.message_template_id,
            ]);

            if (selectedMessageTemplate && selectedMessageTemplate.message) {
                selectedTempalteMessageString.value = selectedMessageTemplate.message;
                selectedTempalteCodeString.value = selectedMessageTemplate.code;

                const extractedVars = extractDynamicVariables(
                    selectedMessageTemplate.message
                );
                createSabValues.value = [];

                forEach(extractedVars, (obj) => {
                    createSabValues.value.push({
                        target: `{{${obj}}}`,
                        name: "",
                    });
                });

                replaceMessageString();
            }

            if (selectedMessageTemplate && selectedMessageTemplate.message) {
                var bodyMessage = selectedMessageTemplate.message;

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
                };

                // Not execute at onMounted
                if (textEditor.value != undefined) {
                    textEditor.value.setHTML(bodyMessage);
                }
            }
        };

        const messageTemplateAdded = () => {
            axiosAdmin.get(messageTemplateUrl).then((response) => {
                allMessageTemplates.value = response.data.message_templates;
            });
        };

        const sendMessage = () => {
            const { remainingNumber, countryCode } = extractCountryCodeAndNumber(
                props.phone
            );

            formData.value = {
                ...formData.value,
                message_template_id: undefined,
                provider_id: undefined,
                message: "",
                phone: remainingNumber,
                country_code: countryCode,
                is_schedule: false,
                date_time: undefined,
            };

            messageTemplateChanged();

            visible.value = true;
        };

        const onSubmit = () => {
            var newFormData = {
                ...formData.value,
                message: messageString.value,
                code: replacePlaceholders(
                    selectedTempalteCodeString.value,
                    createSabValues.value
                ),
                full_number: fullPhoneNumber.value,
            };
            addEditRequestAdmin({
                url: "leads/send-message",
                data: newFormData,
                success: (res) => {
                    if (res && res.success) {
                        visible.value = false;
                        notification.success({
                            message: t("common.success"),
                            description: t("message_template.message_send_successfully"),
                            placement: "bottomRight",
                        });
                        emit("success");
                    } else {
                        notification.error({
                            message: t("common.error"),
                            description: t("message_template.message_send_error_message"),
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

            resetToCampaignMessageTemplate();
        };

        watch(
            () => formData.value.provider_id,
            (newProviderId) => {
                if (newProviderId) {
                    getMessageTemplate(newProviderId);
                    createSabValues.value;
                }
            }
        );

        return {
            loading,
            visible,
            rules,
            formData,
            sendMessage,
            onSubmit,
            hideModal,

            textEditor,
            allMessageTemplates,
            messageTemplateAdded,
            messageTemplateChanged,
            insertFormText,

            drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
            getMessageTemplate,
            allProviders,
            messageTemplate,
            extractDynamicVariables,
            dynamicVariables,
            createSabValues,
            // finalMessage,
            // userInputs,
            // extractedVariables,
            // inputSubvalueChange,

            messageString,
            replaceMessageString,
            countryCodes,
            fullPhoneNumber,
        };
    },
});
</script>
