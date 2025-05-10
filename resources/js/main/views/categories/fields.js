import { useI18n } from "vue-i18n";

const fields = () => {
    const addEditUrl = "categories";
    const { t } = useI18n();

    const initData = {
        name: "",
    };

    const columns = [
        {
            title: t("category.id"),
            dataIndex: "id",
        },
        {
            title: t("category.name"),
            dataIndex: "name",
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
