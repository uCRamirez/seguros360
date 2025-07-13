import { useI18n } from "vue-i18n";

const fields = () => {
    const url = "lead-status?fields=id,xid,name,color,type";
    const addEditUrl = "lead-status";
    const { t } = useI18n();

    const initData = {
        name: "",
        type: "",
        color: "1f87e8",
    };

    const columns = [
        {
            title: t("lead_status.id"),
            dataIndex: "id",
        },
        {
            title: t("lead_status.name"),
            dataIndex: "name",
        },
        {
            title: t("lead_status.color"),
            dataIndex: "color",
        },
        {
            title: t("lead_status.type"),
            dataIndex: "type",
        },
        {
            title: t("common.action"),
            dataIndex: "action",
        },
    ];

    const filterableColumns = [
        {
            key: "name",
            value: t("lead_status.name"),
        },
    ];

    return {
        url,
        addEditUrl,
        initData,
        columns,
        filterableColumns,
    };
};

export default fields;
