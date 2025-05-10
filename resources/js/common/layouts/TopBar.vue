<template>
    <a-layout-header
        :style="{
            padding: '0 16px',
            background: themeMode == 'dark' ? '#141414' : '#fff',
        }"
    >
        <a-row>
            <a-col :span="4">
                <a-space>
                    <MenuOutlined class="trigger" @click="showHideMenu" />
                </a-space>
            </a-col>

            <a-col :span="20">
                <HeaderRightIcons>
                    <a-space>
                        <a-button @click="openRightSidebar" style="border: none"
                            ><span v-if="rightSidebarValue == false"
                                ><StepBackwardOutlined />
                            </span>

                            <span v-else><StepForwardOutlined /></span>
                        </a-button>
                        <ThemeModeChanger />
                        <a-divider type="vertical" />
                        <a-dropdown
                            :placement="appSetting.rtl ? 'bottomLeft' : 'bottomRight'"
                        >
                            <a class="ant-dropdown-link" @click.prevent>
                                {{ selectedLang }}
                                <DownOutlined />
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
                        <a-divider type="vertical" />
                        <a-button
                            type="link"
                            @click="
                                () => {
                                    $router.push({
                                        name: 'admin.settings.profile.index',
                                    });
                                }
                            "
                            class="p-0"
                        >
                            <a-avatar size="small" :src="user.profile_image_url" />
                        </a-button>
                    </a-space>
                </HeaderRightIcons>
            </a-col>
        </a-row>
    </a-layout-header>
</template>

<script>
import { ref, reactive, computed, onMounted } from "vue";
import { useStore } from "vuex";
import {
    MenuOutlined,
    DownOutlined,
    ScheduleOutlined,
    StepBackwardOutlined,
    StepForwardOutlined,
} from "@ant-design/icons-vue";
import { useI18n } from "vue-i18n";
import { loadLocaleMessages } from "../i18n";
import { HeaderRightIcons } from "./style";
import common from "../../common/composable/common";
import MenuMode from "./MenuMode.vue";
import AffixButton from "./AffixButton.vue";
import ThemeModeChanger from "./ThemeModeChanger.vue";

export default {
    components: {
        MenuOutlined,
        DownOutlined,
        ScheduleOutlined,
        HeaderRightIcons,
        MenuMode,
        AffixButton,
        ThemeModeChanger,
        StepBackwardOutlined,
        StepForwardOutlined,
    },
    setup(props, { emit }) {
        const {
            user,
            appSetting,
            permsArray,
            menuCollapsed,
            themeMode,
            rightSidebarValue,
        } = common();
        const store = useStore();
        const selectedLang = ref(store.state.auth.lang);
        const { locale, t } = useI18n();

        const langSelected = async (lang) => {
            store.commit("auth/updateLang", lang);
            await loadLocaleMessages(i18n, lang);

            selectedLang.value = lang;
            locale.value = lang;
        };

        const showHideMenu = () => {
            store.commit("auth/updateMenuCollapsed", !menuCollapsed.value);
        };

        const openRightSidebar = () => {
            store.commit("auth/updateRightSidebarValue", !rightSidebarValue.value);
        };

        const logout = () => {
            store.dispatch("auth/logout");
        };

        return {
            permsArray,
            appSetting,
            logout,
            showHideMenu,
            langSelected,
            selectedLang,
            langs: computed(() => store.state.auth.allLangs),

            user,
            themeMode,
            rightSidebarValue,
            openRightSidebar,
        };
    },
};
</script>

<style lang="less">
.trigger {
    font-size: 18px;
    line-height: 64px;
    padding-top: 4px;
    cursor: pointer;
    transition: color 0.3s;
}

.trigger:hover {
    color: #1890ff;
}
</style>
