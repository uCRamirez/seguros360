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
                <a-col v-if="!formData.acciones || addEditType === 'add'" :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('notes_typification.parent_id')"
                        name="parent_id"
                        :help="rules.parent_id ? rules.parent_id.message : null"
                        :validateStatus="rules.parent_id ? 'error' : null"
                    >
                        <a-tree-select
                            v-model:value="formData.parent_id"
                            show-search
                            style="width: 100%"
                            :dropdown-style="{ maxHeight: '400px', overflow: 'auto' }"
                            :placeholder="
                                $t('common.select_default_text', [
                                    $t('notes_typification.parent_id'),
                                ])
                            "
                            :tree-data="allTypifications"
                            allow-clear
                            tree-default-expand-all
                        />
                        <small class="small-text-message">
                            {{ $t("messages.leave_blank_to_create_notes_typification") }}
                        </small>
                    </a-form-item>
                </a-col>
            </a-row>
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('notes_typification.name')"
                        name="name"
                        :help="rules.name ? rules.name.message : null"
                        :validateStatus="rules.name ? 'error' : null"
                        class="required"
                    >
                        <a-input
                            v-model:value="formData.name"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('notes_typification.name'),
                                ])
                            "
                        />
                    </a-form-item>
                </a-col>
            </a-row>
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('menu.actions')"
                        name="acciones"
                        v-if="formData.acciones"
                    >
                        <a-checkbox style="margin-left: 10px;" v-model:checked="formData.sale">
                            {{ $t('lead_notes.sale') }}
                        </a-checkbox>

                        <a-checkbox  v-model:checked="formData.schedule">
                            {{ $t('common.scheduled') }}
                        </a-checkbox>

                    </a-form-item>
                </a-col>
            </a-row>
        </a-form>
        <template #footer>
            <a-button key="submit" type="primary" :loading="loading" @click="onSubmit">
                <template #icon>
                    <SaveOutlined />
                </template>
                {{ addEditType == "add" ? $t("common.create") : $t("common.update") }}
            </a-button>
            <a-button key="back" @click="onClose">
                {{ $t("common.cancel") }}
            </a-button>
        </template>
    </a-modal>
</template>
<script>
import { defineComponent, ref, computed, watch } from "vue";
import { PlusOutlined, LoadingOutlined, SaveOutlined } from "@ant-design/icons-vue";
import { useI18n } from "vue-i18n";
import apiAdmin from "../../../common/composable/apiAdmin";
import common from "../../../common/composable/common";
import { forEach } from "lodash-es";

export default defineComponent({
    props: ["formData", "data", "visible", "url", "addEditType"],
    components: {
        PlusOutlined,
        LoadingOutlined,
        SaveOutlined,
    },
    setup(props, { emit }) {
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const { getRecursiveTypifications } = common();
        const { t } = useI18n();
        const allTypifications = ref([]);

        const getTypifications = (xid = null) => {
            var url =
                "notes-typifications?fields=id,xid,name,parent_id,x_parent_id,sale,schedule,status&filters=status eq 1&limit=10000";
            if (xid != null) {
                url += `&filters=id ne ${xid}&hashable=${xid}`;
            }

            axiosAdmin.get(url).then((response) => {
                allTypifications.value = getRecursiveTypifications(response, xid);

                // if (allTypifications.value.length >= 3) {
                //     allTypifications.value[2].disabled = true;
                // }
            });
        };

        const pageTitle = computed(() => {
            return props.addEditType == "add"
                ? t("notes_typification.add")
                : t("notes_typification.edit");
        });

        const onSubmit = () => {
            var addEditData = { parent_id: null, ...props.formData };
            const successMessage =
                props.addEditType == "add"
                    ? t("notes_typification.created")
                    : t("notes_typification.updated");

            addEditRequestAdmin({
                url: props.url,
                data: addEditData,
                successMessage,
                success: (res) => {
                    getTypifications();
                    emit("addEditSuccess", res.xid);
                },
            });
        };

        const onClose = () => {
            props.formData.value = [];
            rules.value = {};
            emit("closed");
        };

        watch(props, (newVal, oldVal) => {
            if (newVal.addEditType == "add") {
                props.formData.acciones = true;
                getTypifications();
            } else {
                getTypifications(newVal.data.xid);
            }
        });

        return {
            loading,
            rules,
            onClose,
            onSubmit,
            pageTitle,
            allTypifications,
        };
    },
});
</script>
