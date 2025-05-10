import { useI18n } from "vue-i18n";

const fields = () => {
    const url = "message-providers?fields=id,xid,name,source,auth_token,campaign,subdomain";
    const addEditUrl = "message-providers";
    const hashableColumns = [];
    const { t } = useI18n();

    const initData = {
        name: "",
        source: "",
        auth_token: "",
        campaign: "",
        subdomain: ""
    };

    const columns = [
        {
            title: t("message_provider.name"),
            dataIndex: "name",
        },
        {
            title: t("message_provider.source"),
            dataIndex: "source",
        },
        // {
        //     title: t("message_provider.auth_token"),
        //     dataIndex: "auth_token",
        // },
        {
            title: t("message_provider.campaign"),
            dataIndex: "campaign",
        },
        {
            title: t("message_provider.subdomain"),
            dataIndex: "subdomain",
        },
        {
            title: t("common.action"),
            dataIndex: "action",
        },
    ];

    const filterableColumns = [
        {
            key: "source",
            value: t("message_provider.source"),
        },
        {
            key: "auth_token",
            value: t("message_provider.auth_token"),
        },
        {
            key: "campaign",
            value: t("message_provider.campaign"),
        },
    ];

    return {
        url,
        addEditUrl,
        initData,
        columns,
        filterableColumns,
        hashableColumns,
    };
};

export default fields;
