import { useI18n } from "vue-i18n";

const fields = () => {
    const url = `uphone-calls?fields=id,xid,campaign,client_id,date,direction,duration,guid,discriptions,number,response_data,campaign_id,x_campaign_id,campaigns{id,xid,name},lead_id,x_lead_id,leadData{id,xid,reference_number,first_action_by,last_action_by,time_taken,x_first_action_by,x_last_action_by,lead_data},leadData:firstActioner{id,xid,name}leadData:lastActioner{id,xid,name},leadData:campaign{id,xid,name}`;

    const hashableColumns = ["lead_id", "campaign_id"];
    const { t } = useI18n();

    const columns = [
        {
            title: t("uphone_calls.lead"),
            dataIndex: "reference_number",
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
            title: t("uphone_calls.number"),
            dataIndex: "number",
        },
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
        {
            title: t("common.action"),
            dataIndex: "action",
        },
    ];

    const filterableColumns = [
        {
            key: "campaign",
            value: t("uphone_calls.campaign"),
        },
        {
            key: "client_id",
            value: t("uphone_calls.client_id"),
        },
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
        {
            key: "discriptions",
            value: t("uphone_calls.discriptions"),
        },
        {
            key: "number",
            value: t("uphone_calls.number"),
        },

    ];

    return {
        url,
        columns,
        filterableColumns,
        hashableColumns,
    };
};

export default fields;
