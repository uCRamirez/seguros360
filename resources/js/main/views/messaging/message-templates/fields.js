import { useI18n } from "vue-i18n";

const fields = () => {
    const url =
        "message-templates?fields=id,xid,name,subject,message,status,sharable,code,message_provider_id,x_message_provider_id,messageProvider{id,xid,name}";
    const addEditUrl = "message-templates";
    const hashableColumns = ['message_provider_id'];
    const { t } = useI18n();

    const initData = {
        name: "",
        subject: "",
        message: "",
        status: 1,
        sharable: 0,
        code: "",
        message_provider_id: undefined
    };

    const columns = [
        {
            title: t("message_template.id"),
            dataIndex: "id",
        },
        {
            title: t("message_template.message_provider"),
            dataIndex: "message_provider",
        },
        {
            title: t("message_template.name"),
            dataIndex: "name",
        },
        {
            title: t("message_template.message"),
            dataIndex: "message",
        },
        {
            title: t("message_template.code"),
            dataIndex: "code",
        },
        // {
        //     title: t("message_template.status"),
        //     dataIndex: "status",
        // },
        {
            title: t("common.action"),
            dataIndex: "action",
        },
    ];

    const filterableColumns = [
        {
            key: "name",
            value: t("message_template.name"),
        },
        {
            key: "subject",
            value: t("message_template.subject"),
        },
        {
            key: "message",
            value: t("message_template.message"),
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
