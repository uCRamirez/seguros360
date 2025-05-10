<template>
    <div
        class="login-main-container"
        :style="{
            backgroundImage: `url(${loginBackgroundImage})`,
        }"
    >
        <a-row class="main-container-div">
            <a-col :xs="3" :sm="3" :md="8" :lg="8" class="opacity"></a-col>
            <a-col :xs="18" :sm="18" :md="8" :lg="8" class="card_opacity">
                <a-row class="login-left-div">
                    <a-col
                        :xs="{ span: 24 }"
                        :sm="{ span: 24 }"
                        :md="{ span: 16, offset: 4 }"
                        :lg="{ span: 16, offset: 4 }"
                    >
                        <a-card
                            :title="null"
                            class="login-div"
                            :bordered="innerWidth <= 768 ? true : false"
                            :style="{
                                backgroundColor: loginCardColor,
                            }"
                        >
                            <a-form layout="vertical">
                                <div class="login-logo mb-30">
                                    <img
                                        class="login-img-logo"
                                        :src="globalSetting.light_logo_url"
                                    />
                                </div>
                                <a-alert
                                    v-if="onRequestSend.error != ''"
                                    :message="onRequestSend.error"
                                    type="error"
                                    show-icon
                                    class="mb-20 mt-10"
                                />
                                <a-alert
                                    v-if="onRequestSend.success"
                                    :message="$t('messages.login_success')"
                                    type="success"
                                    show-icon
                                    class="mb-20 mt-10"
                                />
                                <a-form-item
                                    name="email"
                                    :help="rules.email ? rules.email.message : null"
                                    :validateStatus="rules.email ? 'error' : null"
                                >
                                    <template #label>
                                        <span style="color: white; opacity: 1">
                                            {{ $t("user.email_phone") }}</span
                                        >
                                    </template>
                                    <a-input
                                        v-model:value="credentials.email"
                                        @pressEnter="onSubmit"
                                        :placeholder="
                                            $t('common.placeholder_default_text', [
                                                $t('user.email_phone'),
                                            ])
                                        "
                                    />
                                </a-form-item>

                                <a-form-item
                                    name="password"
                                    :help="rules.password ? rules.password.message : null"
                                    :validateStatus="rules.password ? 'error' : null"
                                    class="color"
                                >
                                    <template #label>
                                        <span style="color: white">
                                            {{ $t("user.password") }}</span
                                        >
                                    </template>
                                    <a-input-password
                                        v-model:value="credentials.password"
                                        @pressEnter="onSubmit"
                                        :placeholder="
                                            $t('common.placeholder_default_text', [
                                                $t('user.password'),
                                            ])
                                        "
                                    />
                                </a-form-item>

                                <a-form-item class="mt-30">
                                    <a-button
                                        :loading="loading"
                                        @click="onSubmit"
                                        class="login-btn"
                                        block
                                    >
                                        {{ $t("menu.login") }}
                                    </a-button>
                                </a-form-item>
                            </a-form>
                            <div style="float: right; padding: 1px">
                                <a-dropdown
                                    :placement="
                                        appSetting.rtl ? 'bottomLeft' : 'bottomRight'
                                    "
                                >
                                    <a class="ant-dropdown-link" @click.prevent>
                                        <span style="color: white">
                                            {{ selectedLang }} <DownOutlined
                                        /></span>
                                    </a>
                                    <template #overlay>
                                        <a-menu>
                                            <a-menu-item
                                                v-for="lang in langs"
                                                :key="lang.key"
                                                @click="langSelected(lang.key)"
                                            >
                                                <a-space>
                                                    <a-avatar
                                                        shape="square"
                                                        size="small"
                                                        :src="lang.image_url"
                                                    />
                                                    {{ lang.name }}
                                                </a-space>
                                            </a-menu-item>
                                        </a-menu>
                                    </template>
                                </a-dropdown>
                            </div>
                            <DemoCredentials
                                :credentials="credentials"
                                :backgroundColor="loginCardColor"
                            />
                        </a-card>
                    </a-col>
                </a-row>
            </a-col>
            <a-col :xs="3" :sm="3" :md="8" :lg="8" class="opacity"></a-col>
        </a-row>
    </div>
</template>

<script>
import { defineComponent, reactive, ref, computed } from "vue";
import { DownOutlined } from "@ant-design/icons-vue";
import { useI18n } from "vue-i18n";
import { useStore } from "vuex";
import { useRouter } from "vue-router";
import common from "../../../common/composable/common";
import apiAdmin from "../../../common/composable/apiAdmin";
import DemoCredentials from "./DemoCredentials.vue";
import { loadLocaleMessages } from "../../../common/i18n";

export default defineComponent({
    components: {
        DemoCredentials,
        DownOutlined,
    },
    emit: ["login"],
    setup(props, { emit }) {
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const { globalSetting, appType, appSetting } = common();
        const loginBackgroundImage = ref(globalSetting.value.login_image_url);
        const store = useStore();
        const router = useRouter();
        const credentials = reactive({
            email: null,
            password: null,
        });
        const onRequestSend = ref({
            error: "",
            success: "",
        });

        const loginCardColor = ref(globalSetting.value.login_card_color);

        const selectedLang = ref(store.state.auth.lang);
        const { locale, t } = useI18n();

        const langSelected = async (lang) => {
            store.commit("auth/updateLang", lang);
            await loadLocaleMessages(i18n, lang);

            selectedLang.value = lang;
            locale.value = lang;
        };

        const onSubmit = () => {
            onRequestSend.value = {
                error: "",
                success: false,
            };

            addEditRequestAdmin({
                url: "auth/login",
                data: credentials,
                success: (response) => {
                    if (response && response.status == "success") {
                        const user = response.user;
                        store.commit("auth/updateUser", user);
                        store.commit("auth/updateToken", response.token);
                        store.commit("auth/updateExpires", response.expires_in);
                        store.commit(
                            "auth/updateVisibleSubscriptionModules",
                            response.visible_subscription_modules
                        );

                        if (appType == "non-saas") {
                            router.push({
                                name: "admin.dashboard.index",
                                params: { success: true },
                            });
                        } else {
                            if (user.is_superadmin && user.user_type == "super_admins") {
                                store.commit("auth/updateApp", response.app);
                                store.commit(
                                    "auth/updateEmailVerifiedSetting",
                                    response.email_setting_verified
                                );
                                router.push({
                                    name: "superadmin.dashboard.index",
                                    params: { success: true },
                                });
                            } else {
                                store.commit("auth/updateApp", response.app);
                                store.commit(
                                    "auth/updateEmailVerifiedSetting",
                                    response.email_setting_verified
                                );
                                store.commit(
                                    "auth/updateAddMenus",
                                    response.shortcut_menus.credentials
                                );
                                router.push({
                                    name: "admin.dashboard.index",
                                    params: { success: true },
                                });
                            }
                        }
                    } else {
                        onRequestSend.value = {
                            error: response.message ? response.message : "",
                            success: false,
                        };
                    }
                },
                error: (err) => {},
            });
        };

        return {
            loading,
            rules,
            credentials,
            onSubmit,
            onRequestSend,
            globalSetting,
            loginBackgroundImage,
            langSelected,
            selectedLang,
            langs: computed(() => store.state.auth.allLangs),
            appSetting,
            loginCardColor,

            innerWidth: window.innerWidth,
        };
    },
});
</script>

<style lang="less">
.login-main-container {
    background: #fff;
    height: 100vh;
    background-size: cover;
    background-position: center;
}

.main-container-div {
    height: 100%;
}
.opacity {
    opacity: 0.2;
}
.card_opacity {
    opacity: 0.8;
}

.login-left-div {
    height: 100%;
    align-items: center;
}

.login-logo {
    text-align: center;
}

.login-img-logo {
    width: 150px;
}

.container-content {
    margin-top: 100px;
}

.login-div {
    border-radius: 10px;
}

.outer-div {
    margin: 0;
}

.right-login-div {
    background: #f8f8ff;
    height: 100%;
    display: flex;
    align-items: center;
}

.right-image {
    width: 100%;
    display: block;
    margin: 0 auto;
}

.login-btn,
.login-btn:hover,
.login-btn:active {
    background: #5254cf !important;
    border-color: #5254cf !important;
    border-radius: 5px;
    color: #fff !important;
}
</style>
