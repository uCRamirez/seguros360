<template>
    <a-modal
        width="50%"
        :open="visible"
        :closable="false"
        :centered="true"
        :title="pageTitle"
        @ok="onSubmit"
    >
        <a-form layout="vertical">
            <template v-if="formData.length > 0">
                <a-row
                    :gutter="16"
                    v-for="(data, index) in formData"
                    :key="data.id"
                    style="display: flex; align-items: center"
                >
                    <a-col :xs="24" :sm="24" :md="7" :lg="7">
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
                        :md="7"
                        :lg="7"
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
                        :md="7"
                        :lg="7"
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
                        :md="7"
                        :lg="7"
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

                    <a-col :span="1" style="margin-top: 6px">
                        <MinusSquareOutlined @click="removeFormField(data)" />
                    </a-col>
                </a-row>
            </template>
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="7" :lg="7">
                    <a-form-item>
                        <a-button
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
    props: ["data", "visible", "pageTitle", "addEditType"],
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
            },
        ]);

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
            // onlyValidateErrors.value = {};

            // forEach(formData.value, (formField) => {
            //     nameFields.value = {};
            //     let index = formData.value.indexOf(formField);
            //     nameFields.value = formField;

            // var alltypification = filter(formData.value, (form) => {
            //     return form.typification_1 != "" && form.typification_2 != "" && form.typification_3 != "";
            // });

            var newFormData = {
                notes: formData.value,
                // removed_name: removedName.value,
            };

            addEditRequestAdmin({
                url: "multiple-typifications",
                data: newFormData,
                successMessage: t("notes_typification.typification_add"),
                success: (response) => {
                    // onlyValidateErrors.value = {};
                    setMultipleUrl();
                    emit("close");
                },

                //     error: (err) => {
                //         var errorRules = {};
                //         // console.log(err.error.details);
                //         var keys = Object.keys(err.error.details);
                //         for (var i = 0; i < keys.length; i++) {
                //             // Escape dot that comes with error in array fields
                //             var key = keys[i].replace(".", "\\.");

                //             errorRules[key] = {
                //                 required: true,
                //                 message: err.error.details[keys[i]][0],
                //             };
                //         }
                //         onlyValidateErrors.value[index] = errorRules;
                //     },
                // });
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
                formData.value = [
                    { typification_1: "", typification_2: "", typification_3: "",  typification_4: "" },
                ];
            }
        );

        return {
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
