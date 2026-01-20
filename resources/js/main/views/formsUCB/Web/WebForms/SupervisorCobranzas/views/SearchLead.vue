<template>
    <a-tooltip :title="$t('lead.search_lead')">
        <a-button type="primary" @click="sendCallNumber">
            <template #icon>
                <SearchOutlined />
            </template>
        </a-button>
    </a-tooltip>

    <a-modal
        :open="visible"
        :closable="false"
        :centered="true"
        :title="`${$t('lead.make_call')} (${fullPhoneNumber})`"
        @ok="hideModal"
    >
        <a-form layout="vertical">
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('lead.phone_number')"
                        name="phone_number"
                        :help="rules.phone_number ? rules.phone_number.message : null"
                        :validateStatus="rules.phone_number ? 'error' : null"
                        class="required"
                    >
                        <a-input-group compact>
                            <a-select
                                style="width: 25%"
                                v-model:value="formData.country_code"
                                :placeholder="
                                    $t('common.select_default_text', [
                                        $t('lead.country_code'),
                                    ])
                                "
                                optionFilterProp="value"
                                show-search
                                :allowClear="true"
                            >
                                <a-select-option
                                    v-for="country in countryCodes"
                                    :key="country.code"
                                    :value="country.code"
                                >
                                    {{ country.code }}
                                </a-select-option>
                            </a-select>
                            <a-input
                                style="width: 75%"
                                v-model:value="formData.phone"
                                :placeholder="
                                    $t('common.placeholder_default_text', [
                                        $t('common.phone_number'),
                                    ])
                                "
                            />
                        </a-input-group>
                    </a-form-item>
                </a-col>
            </a-row>
            <a-row :gutter="16" class="mt-17">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('lead.campaign')"
                        name="campaign"
                        :help="rules.campaign ? rules.campaign.message : null"
                        :validateStatus="rules.campaign ? 'error' : null"
                        class="required"
                    >
                        <span style="display: flex">
                            <a-select
                                v-model:value="formData.campaign"
                                :placeholder="
                                    $t('common.select_default_text', [
                                        $t('lead.campaign'),
                                    ])
                                "
                                :allowClear="true"
                            >
                                <a-select-option
                                    v-for="campaign in allOutboundCampaigns"
                                    :value="campaign"
                                >
                                    {{ campaign }}
                                </a-select-option>
                            </a-select>
                        </span>
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
                        <SearchOutlined />
                    </template>
                    {{ $t("common.call") }}
                </a-button>
                <a-button key="back" @click="hideModal">
                    {{ $t("common.cancel") }}
                </a-button>
            </a-space>
        </template>
    </a-modal>
</template>

<script>
import { defineComponent, ref, onMounted, watch } from "vue";
import { SendOutlined, SearchOutlined, PhoneTwoTone } from "@ant-design/icons-vue";
import { useI18n } from "vue-i18n";
import { useStore } from "vuex";
import apiAdmin from "../../../../../../../common/composable/apiAdmin";
import { computed } from "vue";
import common from "../../../../../../../common/composable/common";

export default defineComponent({
    props: {
        lead: {
            default: {},
        },
        campaigns: {
            default: [],
        },
        phone: {
            default: null,
        },
    },
    emits: ["success"],
    components: {
        SendOutlined,
        SearchOutlined,
    },
    setup(props, { emit }) {
        const { rightSidebarValue, countryCodes, extractCountryCodeAndNumber } = common();
        const store = useStore();
        const { t } = useI18n();
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const visible = ref(false);
        const fullPhoneNumber = computed(() => {
            return `${formData.value.country_code}${formData.value.phone}`;
        });

        const allOutboundCampaigns = computed(() => {
            return props.campaigns.filter((item) => item.endsWith("->"));
        });

        const formData = ref({
            campaign: undefined,
            phone: props.phone,
            country_code: undefined,
        });

        const sendCallNumber = () => {
            const { remainingNumber, countryCode } = extractCountryCodeAndNumber(
                props.phone
            );

            formData.value = {
                ...formData.value,
                campaign: undefined,
                phone: remainingNumber,
                country_code: countryCode,
            };

            visible.value = true;

            if (!rightSidebarValue.value) {
                store.commit("auth/updateRightSidebarValue", !rightSidebarValue.value);
            }
        };

        const onSubmit = () => {
            addEditRequestAdmin({
                url: "leads/make-call-request",
                data: formData.value,
                success: (res) => {
                    //Get the iframe window
                    var w = document.getElementsByTagName("iframe")[0].contentWindow;

                    const call = {
                        action: "makecall",
                        params: {
                            number: fullPhoneNumber.value,
                            campaign: formData.value.campaign,
                        },
                    };
                    //Send message
                    w.postMessage(call, "*");

                    hideModal();
                },
            });
        };

        const hideModal = () => {
            visible.value = false;
            loading.value = false;
            rules.value = {};
        };

        return {
            loading,
            visible,
            rules,
            formData,
            sendCallNumber,
            onSubmit,
            hideModal,
            countryCodes,
            fullPhoneNumber,
            allOutboundCampaigns,
            drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
        };
    },
});
</script>
