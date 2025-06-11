import { useI18n } from "vue-i18n";

const fields = () => {
    const addEditUrl = "motivos-calidad";
    const { t } = useI18n();

    const initData = {
        motivo: "",
    };

    const columns = [
        {
            title: t("common.id"),
            dataIndex: "id",
        },
        {
            title: t("message_template.reason"),
            dataIndex: "motivo",
        },
        {
            title: t("common.action"),
            dataIndex: "action",
        },
    ];

    const filterableColumns = [
        {
            key: "name",
            value: t("common.name"),
        },
    ];

    return {
        addEditUrl,
        initData,
        columns,
        filterableColumns,
    };
};

export default fields;
