<template>
    <a-modal
        :open="visible"
        :closable="false"
        :centered="true"
        :title="pageTitle"
        @ok="onSubmit"
    >
        <a-form layout="vertical">
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('lead_notes.message_provider')"
                        name="message_provider_id"
                        :help="
                            rules.message_provider_id
                                ? rules.message_provider_id.message
                                : null
                        "
                        :validateStatus="rules.message_provider_id ? 'error' : null"
                        :allowClear="true"
                        class="required"
                    >
                        <span style="display: flex">
                            <a-select
                                v-model:value="formData.message_provider_id"
                                :placeholder="
                                    $t('common.select_default_text', [
                                        $t('lead_notes.message_provider'),
                                    ])
                                "
                                :allowClear="true"
                            >
                                <a-select-option
                                    v-for="messageProvider in messageProviders"
                                    :key="messageProvider.xid"
                                    :title="messageProvider.name"
                                    :value="messageProvider.xid"
                                >
                                    {{ messageProvider.name }}
                                </a-select-option>
                            </a-select>
                            <MessageProviderAddButton
                                @onAddSuccess="messageProviderAdded"
                            />
                        </span>
                    </a-form-item>
                </a-col>
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('message_template.name')"
                        name="name"
                        :help="rules.name ? rules.name.message : null"
                        :validateStatus="rules.name ? 'error' : null"
                        class="required"
                    >
                        <a-input
                            v-model:value="formData.name"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('message_template.name'),
                                ])
                            "
                        />
                    </a-form-item>
                </a-col>
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('message_template.code')"
                        name="code"
                        :help="rules.code ? rules.code.message : null"
                        :validateStatus="rules.code ? 'error' : null"
                        class="required"
                    >
                        <a-textarea
                            v-model:value="formData.code"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('message_template.code'),
                                ])
                            "
                            :auto-size="{ minRows: 2, maxRows: 3 }"
                        />
                    </a-form-item>
                </a-col>
                <!-- <a-col :xs="24" :sm="12" :md="8" :lg="8">
                    <a-form-item
                        name="sharable"
                        :help="rules.sharable ? rules.sharable.message : null"
                        :validateStatus="rules.sharable ? 'error' : null"
                    >
                        <template #label>
                            <a-space>
                                {{ $t("message_template.sharable") }}
                                <a-tooltip>
                                    <template #title>
                                        {{ $t("message_template.sharable_message") }}
                                    </template>
                                    <InfoCircleOutlined />
                                </a-tooltip>
                            </a-space>
                        </template>
                        <a-radio-group
                            v-model:value="formData.sharable"
                            button-style="solid"
                            size="small"
                        >
                            <a-radio-button :value="1">
                                {{ $t("common.yes") }}
                            </a-radio-button>
                            <a-radio-button :value="0">
                                {{ $t("common.no") }}
                            </a-radio-button>
                        </a-radio-group>
                    </a-form-item>
                </a-col> -->
            </a-row>
            <!-- <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('message_template.subject')"
                        name="subject"
                        :help="rules.subject ? rules.subject.message : null"
                        :validateStatus="rules.subject ? 'error' : null"
                        class="required"
                    >
                        <a-input
                            v-model:value="formData.subject"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('message_template.subject'),
                                ])
                            "
                        />
                    </a-form-item>
                </a-col>
            </a-row> -->
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('message_template.message')"
                        name="message"
                        :help="rules.message ? rules.message.message : null"
                        :validateStatus="rules.message ? 'error' : null"
                        class="required"
                    >
                        <a-textarea
                            v-model:value="formData.message"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('message_template.message'),
                                ])
                            "
                            :auto-size="{ minRows: 5, maxRows: 8 }"
                        />
                        <!-- <QuillEditor
                            ref="textEditor"
                            v-model:content="formData.body"
                            :content="formData.body"
                            contentType="html"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('message_template.body'),
                                ])
                            "
                            style="height: 200px"
                        /> -->
                    </a-form-item>
                </a-col>
            </a-row>

            <!-- <a-row :gutter="16">
                <a-col :span="24">
                    <a-form-item :label="$t('form.form')" name="form_id">
                        <span style="display: flex">
                            <a-select
                                v-model:value="selectedFormId"
                                :placeholder="
                                    $t('common.select_default_text', [$t('form.form')])
                                "
                                :allowClear="true"
                                optionFilterProp="label"
                                show-search
                                @change="formSelected"
                            >
                                <a-select-option
                                    v-for="allForm in allForms"
                                    :key="allForm.xid"
                                    :value="allForm.xid"
                                    :label="allForm.name"
                                    :form="allForm"
                                >
                                    {{ allForm.name }}
                                </a-select-option>
                            </a-select>
                            <FormAddButton @onAddSuccess="formAdded" />
                        </span>
                    </a-form-item>
                </a-col>
            </a-row> -->

            <!-- <a-row
                :gutter="16"
                v-if="
                    selectedForm &&
                    selectedForm.form_fields &&
                    selectedForm.form_fields.length > 0
                "
                class="mb-20"
            >
                <a-col
                    :xs="8"
                    :sm="8"
                    :md="6"
                    :lg="4"
                    v-for="selectedFormField in selectedForm.form_fields"
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

            <a-row :gutter="16" class="mt-10">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('message_template.status')"
                        name="status"
                        :help="rules.status ? rules.status.message : null"
                        :validateStatus="rules.status ? 'error' : null"
                    >
                        <a-radio-group
                            v-model:value="formData.status"
                            button-style="solid"
                            size="small"
                        >
                            <a-radio-button :value="1">
                                {{ $t("common.active") }}
                            </a-radio-button>
                            <a-radio-button :value="0">
                                {{ $t("common.inactive") }}
                            </a-radio-button>
                        </a-radio-group>
                    </a-form-item>
                </a-col>
            </a-row> -->
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
                        <SaveOutlined />
                    </template>
                    {{ addEditType == "add" ? $t("common.create") : $t("common.update") }}
                </a-button>
                <a-button key="back" @click="onClose">
                    {{ $t("common.cancel") }}
                </a-button>
            </a-space>
        </template>
    </a-modal>
</template>

<script>
import { defineComponent, ref, watch, computed, onMounted } from "vue";
import {
    PlusOutlined,
    LoadingOutlined,
    SaveOutlined,
    InfoCircleOutlined,
} from "@ant-design/icons-vue";
import { QuillEditor } from "@vueup/vue-quill";
import "@vueup/vue-quill/dist/vue-quill.snow.css";
import apiAdmin from "../../../../common/composable/apiAdmin";
import FormAddButton from "../../forms/forms/AddButton.vue";
import MessageProviderAddButton from "../../settings/message-providers/AddButton.vue";

export default defineComponent({
    props: [
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
        InfoCircleOutlined,
        QuillEditor,
        FormAddButton,
        MessageProviderAddButton,
    },
    setup(props, { emit }) {
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const formUrl = "forms/all";
        const textEditor = ref(null);
        const allForms = ref([]);
        const selectedFormId = ref(undefined);
        const selectedForm = ref({});
        const messageProviders = ref([]);
        const messageProviderUrl = "message-providers?limit=10000";

        onMounted(() => {
            const formsPromise = axiosAdmin.get(formUrl);
            const messageProvidersPromise = axiosAdmin.get(messageProviderUrl);

            Promise.all([formsPromise, messageProvidersPromise]).then(
                ([formsResponse, messageProviderResponse]) => {
                    allForms.value = formsResponse.data.data;
                    messageProviders.value = messageProviderResponse.data;
                }
            );
        });

        const onSubmit = () => {
            addEditRequestAdmin({
                url: props.url,
                data: props.formData,
                successMessage: props.successMessage,
                success: (res) => {
                    emit("addEditSuccess", res.xid);
                },
            });
        };

        const insertFormText = (mergeFieldText) => {
            const selectedPointArray = editor.value.getSelection(true);
            var newFieldTextValue =
                selectedPointArray.length > 0
                    ? `##${mergeFieldText}##`
                    : ` ##${mergeFieldText}## `;

            editor.value.deleteText(selectedPointArray.index, selectedPointArray.length);
            editor.value.insertText(selectedPointArray.index, newFieldTextValue);
        };

        const onClose = () => {
            rules.value = {};
            emit("closed");
        };

        const editor = computed(() => {
            return textEditor.value.getQuill();
        });

        const formAdded = () => {
            axiosAdmin.get(formUrl).then((response) => {
                allForms.value = response.data.data;
            });
        };

        const messageProviderAdded = () => {
            axiosAdmin.get(messageProviderUrl).then((response) => {
                messageProviders.value = response.data;
            });
        };

        const formSelected = (value, option) => {
            selectedForm.value = option.form;
        };

        watch(
            () => props.visible,
            (newVal, oldVal) => {
                if (newVal == true && textEditor.value) {
                    if (props.addEditType == "edit") {
                        textEditor.value.setHTML(props.formData.message);
                    } else {
                        textEditor.value.setHTML("");
                    }
                }

                selectedFormId.value = undefined;
                selectedForm.value = {};
            }
        );

        return {
            loading,
            rules,
            onClose,
            onSubmit,

            insertFormText,
            textEditor,
            allForms,
            selectedFormId,
            formAdded,
            formSelected,
            selectedForm,
            messageProviders,
            messageProviderAdded,
            drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
        };
    },
});
</script>
