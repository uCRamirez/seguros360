<template>
    <TakeLeadAction/>
</template>

<script>
import { onMounted, ref, createVNode } from "vue";
import {
    PlusCircleOutlined,
    StopOutlined,
    PlayCircleOutlined,
    SyncOutlined,
    ExclamationCircleOutlined,
} from "@ant-design/icons-vue";
import { Modal, notification } from "ant-design-vue";
import { useI18n } from "vue-i18n";
import { useRoute } from "vue-router";
import CampaignMembers from "../../../../campaigns/CampaignMembers.vue";
import CampaignProgress from "../../../../campaigns/CampaignProgress.vue";
import CampaignAddButton from "../../../../campaigns/AddButton.vue";
import common from "../../../../../../common/composable/common";
import apiAdmin from "../../../../../../common/composable/apiAdmin";
import TakeLeadAction from "./views/TakeLeadAction.vue";
// import CallManagerIndex from '../../../../call-manager/index.vue';

export default {
    components: {
        CampaignMembers,
        CampaignProgress,
        CampaignAddButton,
        TakeLeadAction,

        PlusCircleOutlined,
        StopOutlined,
        PlayCircleOutlined,
        SyncOutlined,
    },
    setup() {
        const { formatDateTime, permsArray } = common();
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const allCampaigns = ref(undefined);
        const { t } = useI18n();
        const campaignsUrl =
            "call-managers?fields=id,xid,name,reference_prefix,allow_reference_prefix,remaining_leads,total_leads,campaignUsers{id,xid,user_id,x_user_id,campaign_id,x_campaign_id},campaignUsers:user{id,xid,name,profile_image,profile_image_url},form_id,x_form_id,form{id,xid,name,form_fields},detail_fields,last_action_by,x_last_action_by,lastActioner{id,xid,name},completed_by,x_completed_by,completedBy{id,xid,name},started_on,completed_on,upcoming_lead_action,managed_data";
        const router = useRoute();


        onMounted(() => {
            fetchCampaigns();
        });

        const fetchCampaigns = () => {
            const campaignsPromise = axiosAdmin.get(campaignsUrl);

            Promise.all([campaignsPromise]).then(([campaignsResponse]) => {
                allCampaigns.value = campaignsResponse.data;
            });
        };

        const campaignAdded = () => {
            axiosAdmin.get(campaignsUrl).then((response) => {
                allCampaigns.value = response.data;
            });
        };

        const getLeaActionColor = (actionType) => {
            if (actionType == "start_and_new_lead") {
                return "#faad14";
            } else if (actionType == "resume") {
                return "#52c41a";
            } else {
                return "";
            }
        };

        const stopCampaign = (campaignXId) => {
            Modal.confirm({
                title: t("campaign.stop_campaign") + "?",
                icon: createVNode(ExclamationCircleOutlined),
                content: t("campaign.are_you_want_to_stop_campaign"),
                centered: true,
                okText: t("common.yes"),
                okType: "danger",
                cancelText: t("common.no"),
                onOk() {
                    addEditRequestAdmin({
                        url: "campaigns/stop",
                        data: { x_campaign_id: campaignXId },
                        successMessage: t("campaign.campaign_stopped_successfully"),
                        success: (res) => {
                            fetchCampaigns();
                        },
                    });
                },
                onCancel() { },
            });
        };

        const takeAction = (campaign) => {
            Modal.confirm({
                title: t("common.are_you_sure") + "?",
                icon: createVNode(ExclamationCircleOutlined),
                content: t("campaign.are_you_want_to_create_new_lead"),
                centered: true,
                okText: t("common.yes"),
                okType: "danger",
                cancelText: t("common.no"),
                onOk() {
                    addEditRequestAdmin({
                        url: "campaigns/take-action",
                        data: { x_campaign_id: campaign.xid },
                        successMessage: t("campaign.new_lead_added"),
                        success: (res) => {
                            router.push({
                                name: "admin.call_manager.take_action",
                                params: { id: res.x_lead_id },
                            });
                        },
                    });
                },
                onCancel() { },
            });
        };

        return {
            permsArray,
            formatDateTime,
            allCampaigns,
            fetchCampaigns,

            campaignAdded,
            stopCampaign,
            getLeaActionColor,
            takeAction,
            router,
        };
    },
};
</script>
