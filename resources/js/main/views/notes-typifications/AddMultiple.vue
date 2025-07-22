<template>
    <a-modal
        width="80%"
        :open="visible"
        :closable="false"
        :centered="true"
        :title="pageTitle"
        @ok="onSubmit"
    >
        <a-form layout="vertical">
            <template v-if="formData.length > 0">
                <a-row :gutter="16">
                    <a-col :xs="24" :sm="24" :md="6" :lg="6">
                        <a-form-item class="required" :label="$t('lead.campaign')" name="campaign_id" :help="rules.campaign_id ? rules.campaign_id.message : null" :validateStatus="rules.campaign_id ? 'error' : null">
                            <a-select v-model:value="campaignId" :placeholder="$t('common.select_default_text', [
                                $t('lead.campaign'),
                            ])
                                " :allowClear="true" style="width: 100%" optionFilterProp="title"
                                show-search @change="campaignChanged">
                                <a-select-option v-for="allCampaign in allCampaigns.filter(s => s.active === 1)"
                                    :key="allCampaign.id" :title="allCampaign.name"
                                    :value="allCampaign.id" :campaign="allCampaign">
                                    {{ allCampaign.name }}
                                </a-select-option>
                            </a-select>
                        </a-form-item>
                    </a-col>
                </a-row>
                <a-row
                    :gutter="16"
                    v-for="(data, index) in formData"
                    :key="data.id"
                    style="display: flex; align-items: center"
                >
                    <a-col :xs="24" :sm="24" :md="4" :lg="4">
                        <a-form-item
                            :label="$t('lead_notes.typification_1')"
                            :name="['form_data', index, 'typification_1']"
                            :help="
                                rules.typification_1 ? rules.typification_1.message : null
                            "
                            :validateStatus="rules.typification_1 ? 'error' : null"
                        >
                            <a-input
                                v-model:value="data.typification_1"
                                :placeholder="
                                    $t('common.placeholder_default_text', [
                                        $t('lead_notes.typification_1'),
                                    ])
                                "
                            />
                        </a-form-item>
                    </a-col>

                    <a-col
                        v-show="data.typification_1 != ''"
                        :xs="24"
                        :sm="24"
                        :md="4"
                        :lg="4"
                    >
                        <a-form-item
                            :label="$t('lead_notes.typification_2')"
                            :name="['form_data', index, 'typification_2']"
                            :help="
                                rules.typification_2 ? rules.typification_2.message : null
                            "
                            :validateStatus="rules.typification_2 ? 'error' : null"
                        >
                            <a-input
                                v-model:value="data.typification_2"
                                :placeholder="
                                    $t('common.placeholder_default_text', [
                                        $t('lead_notes.typification_2'),
                                    ])
                                "
                            />
                        </a-form-item>
                    </a-col>

                    <a-col
                        v-show="data.typification_1 != '' && data.typification_2 != ''"
                        :xs="24"
                        :sm="24"
                        :md="4"
                        :lg="4"
                    >
                        <a-form-item
                            :label="$t('lead_notes.typification_3')"
                            :name="['form_data', index, 'typification_3']"
                            :help="
                                rules.typification_3 ? rules.typification_3.message : null
                            "
                            :validateStatus="rules.typification_3 ? 'error' : null"
                        >
                            <a-input
                                v-model:value="data.typification_3"
                                :placeholder="
                                    $t('common.placeholder_default_text', [
                                        $t('lead_notes.typification_3'),
                                    ])
                                "
                            />
                        </a-form-item>
                    </a-col>

                    <a-col
                        v-show="data.typification_1 != '' && data.typification_2 != '' && data.typification_3 != ''"
                        :xs="24"
                        :sm="24"
                        :md="4"
                        :lg="4"
                    >
                        <a-form-item
                            :label="$t('lead_notes.typification_4')"
                            :name="['form_data', index, 'typification_4']"
                            :help="
                                rules.typification_4 ? rules.typification_4.message : null
                            "
                            :validateStatus="rules.typification_4 ? 'error' : null"
                        >
                            <a-input
                                v-model:value="data.typification_4"
                                :placeholder="
                                    $t('common.placeholder_default_text', [
                                        $t('lead_notes.typification_4'),
                                    ])
                                "
                            />
                        </a-form-item>
                    </a-col>

                    <a-col :xs="24"
                        :sm="24"
                        :md="6"
                        :lg="6" :span="1" style="margin-top: 6px;display: inline-flex;">
                        <MinusSquareOutlined @click="removeFormField(data)" danger/>

                        <a-checkbox style="margin-left: 10px;" v-model:checked="data.sale">
                            {{ $t('lead_notes.sale') }}
                        </a-checkbox>

                        <a-checkbox  v-model:checked="data.schedule">
                            {{ $t('common.scheduled') }}
                        </a-checkbox>

                        <a-checkbox  v-model:checked="data.no_contact">
                            {{ $t('common.no_contact') }}
                        </a-checkbox>
                    </a-col>
                </a-row>
            </template>
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="7" :lg="7">
                    <a-form-item>
                        <a-button
                            style="width: fit-content;"
                            type="dashed"
                            block
                            @click="addMultipleTypification()"
                            :disabled="addFormButtonStatus"
                        >
                            <PlusOutlined />
                            {{ $t("notes_typification.add_multiple_typification") }}
                        </a-button>
                    </a-form-item>
                </a-col>
            </a-row>
        </a-form>

        <template #footer>
            <a-button
                type="primary"
                @click="onSubmit"
                style="margin-right: 8px"
                :loading="loading"
                :disabled="formData.length == 0"
            >
                <template #icon>
                    <SaveOutlined />
                </template>
                {{ $t("common.create") }}
            </a-button>
            <a-button @click="onClose">
                {{ $t("common.cancel") }}
            </a-button>
        </template>
    </a-modal>
</template>

<script>
import { defineComponent, ref, onMounted, computed, watch } from "vue";
import { PlusOutlined, MinusSquareOutlined, SaveOutlined } from "@ant-design/icons-vue";
import common from "../../../common/composable/common";
import apiAdmin from "../../../common/composable/apiAdmin";
import { some, forEach, filter } from "lodash-es";
import { useI18n } from "vue-i18n";

export default defineComponent({
    props: ["data", "visible", "pageTitle", "addEditType","allCampaigns"],
    components: {
        PlusOutlined,
        MinusSquareOutlined,
        SaveOutlined,
    },
    setup(props, { emit }) {
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const { t } = useI18n();
        const formData = ref([
            {
                typification_1: "",
                typification_2: "",
                typification_3: "",
                typification_4: "",
                sale: false,
                schedule: false,
                no_contact: false,
            },
        ]);

        const campaignId = ref(null);

        const removedName = ref([]);
        const onlyValidateErrors = ref({});

        const addMultipleTypification = () => {
            formData.value.push({
                typification_1: "",
                typification_2: "",
                typification_3: "",
                typification_4: "",
            });
        };

        const onSubmit = () => {

            var newFormData = {
                campaign_id: campaignId.value,
                notes: formData.value,
            };
            
            addEditRequestAdmin({
                url: "multiple-typifications",
                data: newFormData,
                successMessage: t("notes_typification.typification_add"),
                success: (response) => {
                    setMultipleUrl();
                    emit("close");
                },
            });
        };

        const addFormButtonStatus = computed(() => {
            if (formData.value.length == 0) {
                return false;
            } else {
                return (
                    some(formData.value, { typification_1: "" }) ||
                    some(formData.value, { typification_2: "" }) ||
                    some(formData.value, { typification_3: "" }) ||
                    some(formData.value, { typification_4: "" })
                );
            }
        });

        const removeFormField = (item) => {
            let index = formData.value.indexOf(item);
            if (index !== -1) {
                formData.value.splice(index, 1);
            }

            if (item.id != "") {
                removedName.value.push(item.id);
            }
        };

        const onClose = () => {
            rules.value = {};
            emit("close");
        };

        const setMultipleUrl = () => {
            emit("setMultiple");
        };

        watch(
            () => props.visible,
            (newVal, oldVal) => {
                onlyValidateErrors.value = {};
                campaignId.value = null;
                formData.value = [
                    { typification_1: "", typification_2: "", typification_3: "",  typification_4: "" },
                ];
            }
        );

        return {
            campaignId,
            rules,
            loading,
            // formatDateTime,
            onClose,
            onSubmit,
            formData,
            addMultipleTypification,
            addFormButtonStatus,
            removeFormField,
            setMultipleUrl,
            // onlyValidateErrors,
        };
    },
});
</script>
