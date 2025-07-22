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
                    <a-form-item class="required" :label="$t('lead.campaign')" name="campaign_id" :help="rules.campaign_id ? rules.campaign_id.message : null" :validateStatus="rules.campaign_id ? 'error' : null">
                        <a-select :disabled="(addEditType === 'edit' && formData.parent_id) || (addEditType === 'add' && formData.parent_id)" v-model:value="formData.campaign_id" :placeholder="$t('common.select_default_text', [
                            $t('lead.campaign'),
                        ])
                            " :allowClear="true" style="width: 100%" optionFilterProp="title"
                            show-search @change="campaignChanged">
                            <a-select-option v-for="allCampaign in allCampaigns.filter(s => s.active === 1)"
                                :key="allCampaign.xid" :title="allCampaign.name"
                                :value="allCampaign.xid" :campaign="allCampaign">
                                {{ allCampaign.name }}
                            </a-select-option>
                        </a-select>
                    </a-form-item>
                </a-col>
            </a-row>
            <a-row :gutter="16">
                <a-col v-if="!formData.acciones || addEditType === 'add'" :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('notes_typification.parent_id')"
                        name="parent_id"
                        :help="rules.parent_id ? rules.parent_id.message : null"
                        :validateStatus="rules.parent_id ? 'error' : null"
                    >
                        <a-tree-select
                            :disabled="addEditType === 'edit' && !formData.parent_id"
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
                    >
                        <a-checkbox style="margin-left: 10px;" v-model:checked="formData.sale">
                            {{ $t('lead_notes.sale') }}
                        </a-checkbox>

                        <a-checkbox  v-model:checked="formData.schedule">
                            {{ $t('common.scheduled') }}
                        </a-checkbox>

                        <a-checkbox  v-model:checked="formData.no_contact" :disabled="currentLevel !== 3">
                            {{ $t('common.no_contact') }}
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
    props: ["formData", "data", "visible", "url", "addEditType","allCampaigns"],
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
                "notes-typifications?fields=id,xid,name,parent_id,x_parent_id,sale,schedule,status,campaign{id,xid,name}&filters=status eq 1&limit=10000";
            if (xid != null) {
                url += `&filters=id ne ${xid}&hashable=${xid}`;
            }

            axiosAdmin.get(url).then((response) => {
                allTypifications.value = getRecursiveTypifications(response, xid);
                flatTypificationMap.value = flattenTypifications(allTypifications.value);
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
        

        const flatTypificationMap = ref({});

        const flattenTypifications = (list) => {
            const map = {};
            const recurse = (nodes) => {
                nodes.forEach((node) => {
                    map[node.xid] = node;
                    if (node.children) recurse(node.children);
                });
            };
            recurse(list);
            return map;
        };

        const getTypificationLevel = (parentId) => {
            let level = 1;
            let current = flatTypificationMap.value[parentId];

            while (current && current.x_parent_id) {
                level++;
                current = flatTypificationMap.value[current.x_parent_id];
            }

            return level;
        };

        const currentLevel = computed(() => {
            return props.formData.parent_id ? getTypificationLevel(props.formData.parent_id) + 1 : 1;
        });


        watch(currentLevel, (val) => {
            if (val !== 3 && props.formData.no_contact) {
                props.formData.no_contact = false;
            }
        });


        return {
            currentLevel,
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
