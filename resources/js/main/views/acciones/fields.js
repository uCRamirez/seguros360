import { useI18n } from "vue-i18n";

const fields = () => {
    const addEditUrl = "acciones-calidad";
    const { t } = useI18n();
    
    const initData = {
        nombre: "",
        descripcion: "",
        tipo: "accion",
        users_ids: [],
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
            title: t("lead_status.type"),
            dataIndex: "tipo",
        },
        {
            title: t("common.action"),
            dataIndex: "action",
        },
    ];

    const filterableColumns = [
        {
            key: "nombre",
            value: t("common.name"),
        },
        {
            key: "tipo",
            value: t("lead_status.type"),
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
