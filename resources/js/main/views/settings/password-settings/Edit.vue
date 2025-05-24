<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.profile`)" class="p-0">
                <template #extra>
                    <a-button type="primary" @click="onSubmit">
                        <template #icon> <SaveOutlined /> </template>
                        {{ $t("common.update") }}
                    </a-button>
                </template>
            </a-page-header>
        </template>
        <template #breadcrumb>
            <a-breadcrumb separator="-" style="font-size: 12px">
                <a-breadcrumb-item>
                    <router-link :to="{ name: 'admin.dashboard.index' }">
                        {{ $t(`menu.dashboard`) }}
                    </router-link>
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t("menu.settings") }}
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t("menu.profile") }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </AdminPageHeader>

    <a-row>
        <a-col
            :xs="24"
            :sm="24"
            :md="24"
            :lg="4"
            :xl="4"
            :style="{
                background: themeMode == 'dark' ? '#141414' : '#fff',
                borderRight:
                    themeMode == 'dark'
                        ? '1px solid #303030'
                        : '1px solid #f0f0f0',
            }"
        >
            <SettingSidebar />
        </a-col>
        <a-col :xs="24" :sm="24" :md="24" :lg="20" :xl="20">
            <admin-page-table-content>
                <a-card class="page-content-container mt-20 mb-20">
                    <a-form layout="vertical">
                        <a-row :gutter="16">
                            <a-col :xs="24" :sm="24" :md="12" :lg="12">
                                <a-form-item
                                    :label="
                                        $t(
                                            'password_setting.password_expiration'
                                        )
                                    "
                                    name="password_expiration"
                                    :help="
                                        rules.password_expiration
                                            ? rules.password_expiration.message
                                            : null
                                    "
                                    :validateStatus="
                                        rules.password_expiration
                                            ? 'error'
                                            : null
                                    "
                                    class="required"
                                >
                                    <a-input-number
                                        v-model:value="
                                            formData.password_expiration
                                        "
                                        style="width: 100%"
                                        :placeholder="
                                            $t(
                                                'common.placeholder_default_text',
                                                [
                                                    $t(
                                                        'password_setting.password_expiration'
                                                    ),
                                                ]
                                            )
                                        "
                                    ></a-input-number>
                                </a-form-item>
                            </a-col>
                            <a-col :xs="24" :sm="24" :md="12" :lg="12">
                                <a-form-item
                                    :label="
                                        $t(
                                            'password_setting.login_failed_attempts'
                                        )
                                    "
                                    name="login_failed_attempts"
                                    :help="
                                        rules.login_failed_attempts
                                            ? rules.login_failed_attempts
                                                  .message
                                            : null
                                    "
                                    :validateStatus="
                                        rules.login_failed_attempts
                                            ? 'error'
                                            : null
                                    "
                                    class="required"
                                >
                                    <a-input-number
                                        v-model:value="
                                            formData.login_failed_attempts
                                        "
                                        style="width: 100%"
                                        :placeholder="
                                            $t(
                                                'common.placeholder_default_text',
                                                [
                                                    $t(
                                                        'password_setting.login_failed_attempts'
                                                    ),
                                                ]
                                            )
                                        "
                                    ></a-input-number>
                                </a-form-item>
                            </a-col>
                        </a-row>
                        <a-row :gutter="16">
                            <a-col :xs="24" :sm="24" :md="12" :lg="12">
                                <a-form-item
                                    :label="
                                        $t(
                                            'password_setting.last_used_passwords'
                                        )
                                    "
                                    name="last_used_passwords"
                                    :help="
                                        rules.last_used_passwords
                                            ? rules.last_used_passwords.message
                                            : null
                                    "
                                    :validateStatus="
                                        rules.last_used_passwords
                                            ? 'error'
                                            : null
                                    "
                                    class="required"
                                >
                                    <a-input-number
                                        v-model:value="
                                            formData.last_used_passwords
                                        "
                                        style="width: 100%"
                                        :placeholder="
                                            $t(
                                                'common.placeholder_default_text',
                                                [
                                                    $t(
                                                        'password_setting.last_used_passwords'
                                                    ),
                                                ]
                                            )
                                        "
                                    ></a-input-number>
                                </a-form-item>
                            </a-col>
                            <a-col :xs="24" :sm="24" :md="12" :lg="12">
                                <a-form-item
                                    :label="
                                        $t(
                                            'password_setting.disable_inactive_user'
                                        )
                                    "
                                    name="disable_inactive_user"
                                    :help="
                                        rules.disable_inactive_user
                                            ? rules.disable_inactive_user
                                                  .message
                                            : null
                                    "
                                    :validateStatus="
                                        rules.disable_inactive_user
                                            ? 'error'
                                            : null
                                    "
                                    class="required"
                                >
                                    <a-input-number
                                        v-model:value="
                                            formData.disable_inactive_user
                                        "
                                        style="width: 100%"
                                        :placeholder="
                                            $t(
                                                'common.placeholder_default_text',
                                                [
                                                    $t(
                                                        'password_setting.disable_inactive_user'
                                                    ),
                                                ]
                                            )
                                        "
                                    ></a-input-number>
                                </a-form-item>
                            </a-col>
                        </a-row>
                        <FormItemHeading>
                            {{ $t("password_setting.password_formats") }}
                        </FormItemHeading>
                        <a-row :gutter="16">
                            <a-col :xs="24" :sm="24" :md="8" :lg="8">
                                <a-form-item
                                    :label="$t('password_setting.lower_case')"
                                    name="lower_case"
                                    :help="
                                        rules.lower_case
                                            ? rules.lower_case.message
                                            : null
                                    "
                                    :validateStatus="
                                        rules.lower_case ? 'error' : null
                                    "
                                >
                                    <a-radio-group
                                        v-model:value="formData.lower_case"
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

                            <a-col :xs="24" :sm="24" :md="8" :lg="8">
                                <a-form-item
                                    :label="$t('password_setting.upper_case')"
                                    name="upper_case"
                                    :help="
                                        rules.upper_case
                                            ? rules.upper_case.message
                                            : null
                                    "
                                    :validateStatus="
                                        rules.upper_case ? 'error' : null
                                    "
                                >
                                    <a-radio-group
                                        v-model:value="formData.upper_case"
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
                        <a-row :gutter="16">
                            <a-col :xs="24" :sm="24" :md="8" :lg="8">
                                <a-form-item
                                    :label="
                                        $t('password_setting.special_character')
                                    "
                                    name="special_character"
                                    :help="
                                        rules.special_character
                                            ? rules.special_character.message
                                            : null
                                    "
                                    :validateStatus="
                                        rules.special_character ? 'error' : null
                                    "
                                >
                                    <a-radio-group
                                        v-model:value="
                                            formData.special_character
                                        "
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

                            <a-col :xs="24" :sm="24" :md="8" :lg="8">
                                <a-form-item
                                    :label="$t('password_setting.number')"
                                    name="number"
                                    :help="
                                        rules.number
                                            ? rules.number.message
                                            : null
                                    "
                                    :validateStatus="
                                        rules.number ? 'error' : null
                                    "
                                >
                                    <a-radio-group
                                        v-model:value="formData.number"
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
                        <a-row>
                            <a-col :xs="24" :sm="24" :md="12" :lg="12">
                                <a-form-item
                                    :label="
                                        $t('password_setting.password_length')
                                    "
                                    name="password_length"
                                    :help="
                                        rules.password_length
                                            ? rules.password_length.message
                                            : null
                                    "
                                    :validateStatus="
                                        rules.password_length ? 'error' : null
                                    "
                                    class="required"
                                >
                                    <a-input-number
                                        v-model:value="formData.password_length"
                                        style="width: 100%"
                                        :placeholder="
                                            $t(
                                                'common.placeholder_default_text',
                                                [
                                                    $t(
                                                        'password_setting.password_length'
                                                    ),
                                                ]
                                            )
                                        "
                                    ></a-input-number>
                                </a-form-item>
                            </a-col>
                        </a-row>
                        <a-row :gutter="16">
                            <a-col :xs="24" :sm="24" :md="24" :lg="24">
                                <a-form-item >
                                    <a-button
                                        v-if="
                                            permsArray.includes('password_settings_edit') ||
                                            permsArray.includes('admin')
                                        "
                                        type="primary"
                                        @click="onSubmit"
                                        :loading="loading"
                                    >
                                        <template #icon>
                                            <SaveOutlined />
                                        </template>
                                        {{ $t("common.update") }}
                                    </a-button>
                                </a-form-item>
                            </a-col>
                        </a-row>
                    </a-form>
                </a-card>
            </admin-page-table-content>
        </a-col>
    </a-row>
</template>
<script>
import { onMounted, ref } from "vue";
import {
    EyeOutlined,
    PlusOutlined,
    EditOutlined,
    DeleteOutlined,
    ExclamationCircleOutlined,
    SaveOutlined,
} from "@ant-design/icons-vue";
import { useI18n } from "vue-i18n";
import apiAdmin from "../../../../common/composable/apiAdmin";
import SettingSidebar from "../SettingSidebar.vue";
import AdminPageHeader from "../../../../common/layouts/AdminPageHeader.vue";
import FormItemHeading from "../../../../common/components/common/typography/FormItemHeading.vue";
import { forEach } from "lodash-es";
import common from "../../../../common/composable/common";

export default {
    components: {
        EyeOutlined,
        PlusOutlined,
        EditOutlined,
        DeleteOutlined,
        ExclamationCircleOutlined,
        SaveOutlined,
        FormItemHeading,

        SettingSidebar,
        AdminPageHeader,
    },
    setup() {
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const { t } = useI18n();
        const formData = ref({});
        const passwordSetting = ref({});
        const passwordSettingUrl =
            "password-settings?fields=id,xid,upper_case,lower_case,special_character,number,password_length,password_expiration,login_failed_attempts,last_used_passwords,disable_inactive_user";
        const xid = ref("");
        const { themeMode, permsArray } = common();

        onMounted(() => {
            const passwordSettingPromise = axiosAdmin.get(passwordSettingUrl);

            Promise.all([passwordSettingPromise]).then(
                ([passwordSettingResponse]) => {
                    passwordSetting.value = passwordSettingResponse.data;
                    forEach(passwordSetting.value, (passwordSetting) => {
                        formData.value.upper_case = passwordSetting.upper_case;
                        formData.value.lower_case = passwordSetting.lower_case;
                        formData.value.special_character =
                            passwordSetting.special_character;
                        formData.value.number = passwordSetting.number;
                        formData.value.password_length =
                            passwordSetting.password_length;
                        formData.value.password_expiration =
                            passwordSetting.password_expiration;
                        formData.value.disable_inactive_user =
                            passwordSetting.disable_inactive_user;
                        formData.value.login_failed_attempts =
                            passwordSetting.login_failed_attempts;
                        formData.value.last_used_passwords =
                            passwordSetting.last_used_passwords;
                        xid.value = passwordSetting.xid;
                    });
                }
            );
        });
        const onSubmit = () => {
            addEditRequestAdmin({
                url: `password-settings/${xid.value}`,
                data: {
                    ...formData.value,
                    _method: "PUT",
                },
                successMessage: t("password_setting.updated"),
                success: (res) => {},
            });
        };

        return {
            loading,
            rules,
            formData,
            onSubmit,
            themeMode,
            permsArray,
        };
    },
};
</script>
