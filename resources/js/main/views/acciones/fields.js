import { useI18n } from "vue-i18n";

const fields = () => {
    const addEditUrl = "acciones-calidad";
    const { t } = useI18n();

    const initData = {
        nombre: "",
        descripcion: "",
    };

    const columns = [
        {
            title: t("common.id"),
            dataIndex: "id",
        },
        {
            title: t("common.name"),
            dataIndex: "nombre",
        },
        {
            title: t("common.description"),
            dataIndex: "descripcion",
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
