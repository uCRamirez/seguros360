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
                    <a-form-item
                        :label="$t('lead.campaign')"
                        name="campaign"
                        :help="rules.campaign ? rules.campaign.message : null"
                        :validateStatus="rules.campaign ? 'error' : null"
                    >
                        <span style="display: flex">
                            <a-select
                                v-model:value="campaignName"
                                :placeholder="
                                    $t('common.select_default_text', [
                                        $t('lead.campaign'),
                                    ])
                                "
                                :allowClear="true"
                            >
                                <a-select-option
                                    v-for="campaign in campaigns"
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
            <a-button key="submit" type="primary" :loading="loading" @click="onSubmit">
                <template #icon>
                    <SaveOutlined />
                </template>
                {{ $t("common.call") }}
            </a-button>
            <a-button key="back" @click="onClose">
                {{ $t("common.cancel") }}
            </a-button>
        </template>
    </a-modal>
</template>
<script>
import { defineComponent, ref, watch } from "vue";
import { PlusOutlined, LoadingOutlined, SaveOutlined } from "@ant-design/icons-vue";
import apiAdmin from "../../../common/composable/apiAdmin";
import { forEach, find } from "lodash-es";
import { useI18n } from "vue-i18n";

export default defineComponent({
    props: ["data", "visible", "pageTitle"],
    components: {
        PlusOutlined,
        LoadingOutlined,
        SaveOutlined,
    },
    setup(props, { emit }) {
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const campaigns = ref([]);
        const campaignName = ref("");
        const { t } = useI18n();

        const onSubmit = () => {
            console.log("yes");

            var windowProxy;

            windowProxy = new Porthole.WindowProxy(
                window.location.protocol + "//" + window.location.hostname
            );
            windowProxy.addEventListener(onResponse);
            windowProxy.post({
                action: "callWithPhone",
                destination: "917737829720",
                campaign: "TestCRMCR->",
            });

            // var formData = {
            //     campaign_name: campaignName.value,
            // };
            // addEditRequestAdmin({
            //     url: "leads/uphone-campaigns",
            //     data: formData,
            //     successMessage: t("lead.send_call_create"),
            //     success: (res) => {
            //         emit("close");
            //     },
            // });
        };

        const onResponse = (messageEvent) => {
            var response = messageEvent.data.guid;
            alert(response);
        };

        const onClose = () => {
            rules.value = {};
            emit("close");
        };

        watch(
            () => props.visible,
            (newVal) => {
                if (newVal) {
                    campaigns.value = [];
                    campaignName.value = [];
                    forEach(props.data, (uPhoneCampaign) => {
                        campaigns.value = uPhoneCampaign;
                    });
                }
            }
        );

        return {
            loading,
            rules,
            onClose,
            onSubmit,
            campaigns,
            campaignName,
            drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
        };
    },
});
</script>
