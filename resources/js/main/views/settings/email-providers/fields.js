import { useI18n } from "vue-i18n";

const fields = () => {
    const url = "email-providers?fields=id,xid,name,auth_token,subdomain";
    const addEditUrl = "email-providers";
    const hashableColumns = [];
    const { t } = useI18n();

    const initData = {
        name: "",
        auth_token: "",
        subdomain: ""
    };

    const columns = [
        {
            title: t("email_provider.name"),
            dataIndex: "name",
        },
        {
            title: t("email_provider.auth_token"),
            dataIndex: "auth_token",
        },
        {
            title: t("email_provider.subdomain"),
            dataIndex: "subdomain",
        },
        {
            title: t("common.action"),
            dataIndex: "action",
        },
    ];

    const filterableColumns = [
        {
            key: "name",
            value: t("email_provider.name"),
        },
        {
            key: "auth_token",
            value: t("email_provider.auth_token"),
        },
        {
            key: "subdomain",
            value: t("email_provider.subdomain"),
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
