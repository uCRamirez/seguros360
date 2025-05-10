import { useI18n } from "vue-i18n";

const fields = () => {
    const fieldsString =
        "fields=id,xid,event,auditable_id,tags,url,user_agent,old_values,new_values,ip_address,created_at,updated_at,auditable_type,user_type,user_id,x_user_id,user{id,xid,name}";
    const url = `audits?${fieldsString}`;
    const addEditUrl = "audits";
    const { t } = useI18n();

    const columns = [
        {
            title: t("audit.id"),
            dataIndex: "id",
        },
        {
            title: t("audit.user_id"),
            dataIndex: "user_id",
        },
        {
            title: t("audit.date_time"),
            dataIndex: "created_at",
        },
        {
            title: t("audit.ip_address"),
            dataIndex: "ip_address",
        },
        {
            title: t("audit.auditable_id"),
            dataIndex: "auditable_id",
        },
        {
            title: t("audit.event"),
            dataIndex: "event",
        },
        {
            title: t("common.action"),
            dataIndex: "action",
        },
    ];

    const filterableColumns = [
        {
            key: "auditable_id",
            value: t("audit.auditable_id"),
        },
        {
            key: "ip_address",
            value: t("audit.ip_address"),
        },
    ];

    return {
        url,
        fieldsString,
        addEditUrl,
        columns,
        filterableColumns,
    };
};

export default fields;
