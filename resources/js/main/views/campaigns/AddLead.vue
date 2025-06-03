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
                <a-row :gutter="16">
                    <a-col :xs="24" :sm="24" :md="24" :lg="24">
                        <a-form-item
                            :label="$t('lead.assign_to')"
                            name="assign_to"
                            :help="rules.assign_to ? rules.assign_to.message : null"
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
                </a-row>
                <a-row :gutter="16">
                    <a-col :xs="24" :sm="24" :md="24" :lg="24">
                        <a-form-item
                            :label="$t('lead.document')"
                            name="cedula"
                            :help="rules.cedula ? rules.cedula.message : null"
                            :validateStatus="rules.cedula ? 'error' : null"
                            class="required"
                        >
                        <a-input v-model:value="formData.cedula"
                                :placeholder="$t('common.placeholder_default_text',[$t('lead.document'),])" />
                        </a-form-item>
                    </a-col>
                </a-row>
                <!-- Nombre completo -->
                <a-row :gutter="16">
                    <a-col :xs="24" :sm="24" :md="8" :lg="8">
                        <a-form-item
                            :label="$t('lead.name')"
                            name="name"
                            :help="rules.name ? rules.name.message : null"
                            :validateStatus="rules.name ? 'error' : null"
                        >
                        <a-input v-model:value="formData.nombre"
                                :placeholder="$t('common.placeholder_default_text',[$t('lead.name'),])" />
                        </a-form-item>
                    </a-col>
                    <a-col :xs="24" :sm="24" :md="8" :lg="8">
                        <a-form-item
                            :label="$t('lead.first_last_name')"
                            name="first_last_name"
                            :help="rules.first_last_name ? rules.first_last_name.message : null"
                            :validateStatus="rules.first_last_name ? 'error' : null"
                        >
                        <a-input v-model:value="formData.apellido1"
                                :placeholder="$t('common.placeholder_default_text',[$t('lead.first_last_name'),])" />
                        </a-form-item>
                    </a-col>
                    <a-col :xs="24" :sm="24" :md="8" :lg="8">
                        <a-form-item
                            :label="$t('lead.second_last_name')"
                            name="second_last_name"
                            :help="rules.second_last_name ? rules.second_last_name.message : null"
                            :validateStatus="rules.second_last_name ? 'error' : null"
                        >
                        <a-input v-model:value="formData.apellido2"
                                :placeholder="$t('common.placeholder_default_text',[$t('lead.second_last_name'),])" />
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
                        {{ $t("common.create") }}
                    </a-button>
                    <a-button key="back" @click="onClose">
                        {{ $t("common.cancel") }}
                    </a-button>
                </a-space>
            </template>
        </a-drawer>
    </div>
</template>
<script>
import { defineComponent, ref, onMounted, watch } from "vue";
import { SaveOutlined } from "@ant-design/icons-vue";
import { forEach } from "lodash-es";
import { useI18n } from "vue-i18n";
import common from "../../../common/composable/common";
import apiAdmin from "../../../common/composable/apiAdmin";

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

        const { t } = useI18n();

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

        return {
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

            drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
        };
    },
});
</script>
