import { ref, onMounted } from "vue";
import { useI18n } from "vue-i18n";
import { forEach } from "lodash-es";
import common from "../../../../common/composable/common";

const fields = () => {
    const { convertStringToKey, getCampaignUrl, getCampaignStatsUrl } = common();
    const url =
        "leads?fields=id,xid,cedula,nombre,tel1,lead_data,started,campaign_id,x_campaign_id,campaign{id,xid,name,status},time_taken,first_action_by,x_first_action_by,firstActioner{id,xid,name},last_action_by,x_last_action_by,lastActioner{id,xid,name},notes_count,campaignUsers{id,xid,user_id,x_user_id},campaignUsers:user{id,xid,name},assign_to,x_assign_to,assignUsers{id,xid,name},leadStatus{id,xid,name,color,type}";
    const addEditUrl = "leads";
    const hashableColumns = ["campaign_id", "lead_status_id"];
    const { t } = useI18n();
    const allFormFieldNames = ref([]);
    const allCampaigns = ref([]);
    const formFieldNamesUrl = "form-field-names/all";
    const campaignStats = ref({});
    const userCampaigns = ref([]);
    const viewType = ref("self");
    const activeCampaignType = ref("active");

    const initData = {
        reference_number: "",
    };

    const columns = ref([]);

    const filterableColumns = [
        {
            key: "cedula",
            value: t("lead.cedula"),
        },
        {
            key: "nombre",
            value: t("lead.name"),
        },
        {
            key: "tel1",
            value: t("lead.phone"),
        },
    ];

    const uphoneColumn = ref([
        // {
        //     title: t("uphone_calls.lead_id"),
        //     dataIndex: "lead_id",
        // },
        {
            title: t("uphone_calls.number"),
            dataIndex: "number",
        },
        // {
        //     title: t("uphone_calls.campaign_id"),
        //     dataIndex: "campaign_id",
        // },
        // {
        //     title: t("uphone_calls.campaign"),
        //     dataIndex: "campaign",
        // },
        // {
        //     title: t("uphone_calls.client_id"),
        //     dataIndex: "client_id",
        // },

        {
            title: t("uphone_calls.direction"),
            dataIndex: "direction",
        },
        {
            title: t("uphone_calls.duration"),
            dataIndex: "duration",
        },
        {
            title: t("uphone_calls.guid"),
            dataIndex: "guid",
        },
        // {
        //     title: t("uphone_calls.discriptions"),
        //     dataIndex: "discriptions",
        // },
        {
            title: t("uphone_calls.date"),
            dataIndex: "date",
        },
    ]);

    const uphoneFilterableColumns = [
        // {
        //     key: "campaign",
        //     value: t("uphone_calls.campaign"),
        // },
        // {
        //     key: "client_id",
        //     value: t("uphone_calls.client_id"),
        // },
        {
            key: "direction",
            value: t("uphone_calls.direction"),
        },
        {
            key: "duration",
            value: t("uphone_calls.duration"),
        },
        {
            key: "guid",
            value: t("uphone_calls.guid"),
        },
        // {
        //     key: "discriptions",
        //     value: t("uphone_calls.discriptions"),
        // },
        {
            key: "number",
            value: t("uphone_calls.number"),
        },
    ];

    onMounted(() => {
        const campaignsUrl = getCampaignUrl(
            activeCampaignType.value,
            viewType.value
        );
        const campaignStatsUrl = getCampaignStatsUrl();
        const formFieldNamesPromise = axiosAdmin.get(formFieldNamesUrl);
        const campaignsPromise = axiosAdmin.get(campaignsUrl);
        const campaignStatsPromise = axiosAdmin.get(campaignStatsUrl);
        const userCampaignsPromise = axiosAdmin.get("campaigns/user-campaigns");

        Promise.all([
            formFieldNamesPromise,
            campaignsPromise,
            campaignStatsPromise,
            userCampaignsPromise,
        ]).then(
            ([
                formFieldNamesResponse,
                campaignsResponse,
                campaignStatsResponse,
                userCampaignsResponse,
            ]) => {
                allFormFieldNames.value = formFieldNamesResponse.data.data;
                allCampaigns.value = campaignsResponse.data;
                campaignStats.value = campaignStatsResponse.data;
                userCampaigns.value = userCampaignsResponse.data.user_campaigns;

                var newColumnsArray = [
                    {
                        title: t("lead.document"),
                        dataIndex: "cedula",
                    },
                    {
                        title: t("lead.name"),
                        dataIndex: "nombre",
                    },
                    {
                        title: t("lead.phone"),
                        dataIndex: "tel1",
                    },
                    {
                        title: t("lead.campaign"),
                        dataIndex: "campaign",
                    },
                ];

                forEach(formFieldNamesResponse.data.data, (formFieldName) => {
                    newColumnsArray.push({
                        title: formFieldName.field_name,
                        dataIndex: convertStringToKey(formFieldName.field_name),
                    });
                });

                newColumnsArray.push({
                    title: t("lead.total_notes"),
                    dataIndex: "notes_count",
                });

                newColumnsArray.push({
                    title: t("lead.assign_to"),
                    dataIndex: "assign_to",
                });

                newColumnsArray.push({
                    title: t("lead.lead_status"),
                    dataIndex: "lead_status",
                });

                newColumnsArray.push({
                    title: t("common.action"),
                    dataIndex: "action",
                });

                columns.value = newColumnsArray;
            }
        );
    });

    return {
        url,
        addEditUrl,
        initData,
        columns,
        filterableColumns,
        hashableColumns,
        allFormFieldNames,
        convertStringToKey,
        allCampaigns,
        campaignStats,
        userCampaigns,
        uphoneColumn,
        activeCampaignType,
        viewType,
        uphoneFilterableColumns
    };
};

export default fields;
