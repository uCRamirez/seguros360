<template>
    <a-drawer
        :title="pageTitle"
        :width="drawerWidth"
        :open="visible"
        :body-style="{ paddingBottom: '80px' }"
        :footer-style="{ textAlign: 'right' }"
        :maskClosable="false"
        @close="onClose"
    >
        <a-form layout="vertical">
            <a-row>
                <a-col :xs="24" :sm="24" :md="6" :lg="6">
                    <a-form-item
                        :label="$t('user.profile_image')"
                        name="profile_image"
                        :help="
                            rules.profile_image
                                ? rules.profile_image.message
                                : null
                        "
                        :validateStatus="rules.profile_image ? 'error' : null"
                    >
                        <Upload
                            :formData="formData"
                            folder="user"
                            imageField="profile_image"
                            @onFileUploaded="
                                (file) => {
                                    formData.profile_image = file.file;
                                    formData.profile_image_url = file.file_url;
                                }
                            "
                        />
                    </a-form-item>
                </a-col>
                <a-col :xs="24" :sm="24" :md="18" :lg="18">
                    <a-row :gutter="16" v-if="permsArray.includes('admin')">
                        <a-col :xs="24" :sm="24" :md="12" :lg="12">
                            <a-form-item
                                :label="$t('user.role')"
                                name="role_id"
                                :help="
                                    rules.role_id ? rules.role_id.message : null
                                "
                                :validateStatus="rules.role_id ? 'error' : null"
                                class="required"
                            >
                                <span style="display: flex">
                                    <a-select
                                        v-model:value="formData.role_id"
                                        :placeholder="
                                            $t('common.select_default_text', [
                                                $t('user.role'),
                                            ])
                                        "
                                        :allowClear="true"
                                    >
                                        <a-select-option
                                            v-for="role in roles"
                                            :key="role.xid"
                                            :value="role.xid"
                                        >
                                            {{ role.display_name }}
                                        </a-select-option>
                                    </a-select>
                                    <RoleAddButton @onAddSuccess="roleAdded" />
                                </span>
                            </a-form-item>
                        </a-col>

                        <a-col :xs="24" :sm="24" :md="12" :lg="12">
                            <a-form-item
                                :label="$t('user.ucontact')"
                                name="ucontact"
                                :help="
                                    rules.ucontact
                                        ? rules.ucontact.message
                                        : null
                                "
                                :validateStatus="
                                    rules.ucontact ? 'error' : null
                                "
                            >
                                <a-radio-group
                                    v-model:value="formData.ucontact"
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
                        </a-col>
                    </a-row>

                    <a-row :gutter="16" v-if="formData.ucontact == 1">
                        <a-col :xs="24" :sm="24" :md="12" :lg="12">
                            <a-form-item
                                :label="$t('user.ucontact_user')"
                                name="ucontact_user"
                                :help="
                                    rules.ucontact_user
                                        ? rules.ucontact_user.message
                                        : null
                                "
                                :validateStatus="
                                    rules.ucontact_user ? 'error' : null
                                "
                                class="required"
                            >
                                <a-input
                                    v-model:value="formData.ucontact_user"
                                    :placeholder="
                                        $t('common.placeholder_default_text', [
                                            $t('user.ucontact_user'),
                                        ])
                                    "
                                />
                            </a-form-item>
                        </a-col>
                        <a-col :xs="24" :sm="24" :md="12" :lg="12">
                            <a-form-item
                                :label="$t('user.ucontact_password')"
                                name="ucontact_password"
                                :help="
                                    rules.ucontact_password
                                        ? rules.ucontact_password.message
                                        : null
                                "
                                :validateStatus="
                                    rules.ucontact_password ? 'error' : null
                                "
                                class="required"
                            >
                                <a-input
                                    v-model:value="formData.ucontact_password"
                                    :placeholder="
                                        $t('common.placeholder_default_text', [
                                            $t('user.ucontact_password'),
                                        ])
                                    "
                                />
                            </a-form-item>
                        </a-col>
                    </a-row>

                    <a-row :gutter="16">
                        <a-col :xs="24" :sm="24" :md="12" :lg="12">
                            <a-form-item
                                :label="$t('user.name')"
                                name="name"
                                :help="rules.name ? rules.name.message : null"
                                :validateStatus="rules.name ? 'error' : null"
                            >
                                <a-input
                                    v-model:value="formData.name"
                                    :placeholder="
                                        $t('common.placeholder_default_text', [
                                            $t('user.name'),
                                        ])
                                    "
                                />
                            </a-form-item>
                        </a-col>
                        <a-col :xs="24" :sm="24" :md="12" :lg="12">
                            <a-form-item
                                :label="$t('user.phone')"
                                name="phone"
                                :help="rules.phone ? rules.phone.message : null"
                                :validateStatus="rules.phone ? 'error' : null"
                                class="required"
                            >
                                <a-input
                                    v-model:value="formData.phone"
                                    :placeholder="
                                        $t('common.placeholder_default_text', [
                                            $t('user.phone'),
                                        ])
                                    "
                                />
                            </a-form-item>
                        </a-col>
                    </a-row>
                    <a-row>
                        <a-col :span="24">
                            <a-form-item
                                :label="$t('user.user')"
                                name="user"
                                :help="
                                    rules.user
                                        ? rules.user.message
                                        : null
                                "
                                :validateStatus="
                                    rules.user ? 'error' : null
                                "
                                class="required"
                            >
                                <a-input
                                    v-model:value="formData.user"
                                    :placeholder="
                                        $t('common.placeholder_default_text', [
                                            $t('user.user'),
                                        ])
                                    "
                                />
                            </a-form-item>
                        </a-col>
                    </a-row>
                    <a-row>
                        <a-col :span="24">
                            <a-form-item
                                :label="$t('user.password')"
                                name="password"
                                :help="
                                    rules.password
                                        ? rules.password.message
                                        : null
                                "
                                :validateStatus="
                                    rules.password ? 'error' : null
                                "
                                class="required"
                            >
                                <a-input-password
                                    v-model:value="formData.password"
                                    :placeholder="
                                        $t('common.placeholder_default_text', [
                                            $t('user.password'),
                                        ])
                                    "
                                />
                            </a-form-item>
                        </a-col>
                    </a-row>
                    <a-row :gutter="16">
                        <a-col :xs="24" :sm="24" :md="12" :lg="12">
                            <a-form-item
                                :label="$t('user.email')"
                                name="email"
                                :help="rules.email ? rules.email.message : null"
                                :validateStatus="rules.email ? 'error' : null"
                                class="required"
                            >
                                <a-input
                                    v-model:value="formData.email"
                                    :placeholder="
                                        $t('common.placeholder_default_text', [
                                            $t('user.email'),
                                        ])
                                    "
                                />
                            </a-form-item>
                        </a-col>
                        <a-col :xs="24" :sm="24" :md="12" :lg="12">
                            <a-form-item
                                :label="$t('user.status')"
                                name="status"
                                :help="
                                    rules.status ? rules.status.message : null
                                "
                                :validateStatus="rules.status ? 'error' : null"
                                class="required"
                            >
                                <a-select
                                    v-model:value="formData.status"
                                    :placeholder="
                                        $t('common.select_default_text', [
                                            $t('user.status'),
                                        ])
                                    "
                                    :allowClear="true"
                                >
                                    <a-select-option value="enabled"
                                        >Enabled</a-select-option
                                    >
                                    <a-select-option value="disabled"
                                        >Disabled</a-select-option
                                    >
                                </a-select>
                            </a-form-item>
                        </a-col>
                    </a-row>
                </a-col>
            </a-row>
            <!-- <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('user.is_sellers')"
                        name="is_sellers"
                        :help="
                            rules.is_sellers ? rules.is_sellers.message : null
                        "
                        :validateStatus="rules.is_sellers ? 'error' : null"
                    >
                        <a-radio-group
                            v-model:value="formData.is_sellers"
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
                </a-col>
            </a-row> -->
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('user.address')"
                        name="address"
                        :help="rules.address ? rules.address.message : null"
                        :validateStatus="rules.address ? 'error' : null"
                    >
                        <a-textarea
                            v-model:value="formData.address"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('user.address'),
                                ])
                            "
                            :auto-size="{ minRows: 2, maxRows: 3 }"
                        />
                    </a-form-item>
                </a-col>
            </a-row>
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('user.notes')"
                        name="notes"
                        :help="rules.notes ? rules.notes.message : null"
                        :validateStatus="rules.notes ? 'error' : null"
                    >
                        <a-textarea
                            v-model:value="formData.notes"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('user.notes'),
                                ])
                            "
                            :auto-size="{ minRows: 2, maxRows: 3 }"
                        />
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
            >
                <template #icon> <SaveOutlined /> </template>
                {{
                    addEditType == "add"
                        ? $t("common.create")
                        : $t("common.update")
                }}
            </a-button>
            <a-button @click="onClose">
                {{ $t("common.cancel") }}
            </a-button>
        </template>
    </a-drawer>
</template>

<script>
import { defineComponent, ref, onMounted, watch } from "vue";
import {
    PlusOutlined,
    LoadingOutlined,
    SaveOutlined,
} from "@ant-design/icons-vue";
import { useStore } from "vuex";
import apiAdmin from "../../../common/composable/apiAdmin";
import Upload from "../../../common/core/ui/file/Upload.vue";
import RoleAddButton from "../settings/roles/AddButton.vue";
import common from "../../../common/composable/common";

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
        Upload,
        RoleAddButton,
    },
    setup(props, { emit }) {
        const { permsArray, user, appSetting } = common();
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const roles = ref([]);
        const roleUrl = "roles?limit=10000";
        const store = useStore();

        onMounted(() => {
            const rolesPromise = axiosAdmin.get(roleUrl);

            Promise.all([rolesPromise]).then(([rolesResponse]) => {
                roles.value = rolesResponse.data;
            });
        });

        const onSubmit = () => {
            addEditRequestAdmin({
                url: props.url,
                data: props.formData,
                successMessage: props.successMessage,
                success: (res) => {
                    emit("addEditSuccess", res.xid);

                    if (user.value.xid == res.xid) {
                        store.dispatch("auth/updateUser");
                    }
                },
            });
        };

        const onClose = () => {
            rules.value = {};
            emit("closed");
        };

        const roleAdded = () => {
            axiosAdmin.get(roleUrl).then((response) => {
                roles.value = response.data;
            });
        };

        return {
            loading,
            rules,
            onClose,
            onSubmit,
            roles,

            roleAdded,
            permsArray,
            appSetting,

            drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
        };
    },
});
</script>
