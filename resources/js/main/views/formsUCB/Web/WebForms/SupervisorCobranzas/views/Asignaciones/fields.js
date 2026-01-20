import { useI18n } from "vue-i18n";

const fields = () => {
    const { t } = useI18n();

    // COLUMNAS DE LA TABLA DE BASES DE CLIENTES
    const columns = [
        {
            title: t("cobranzas.date"),
            dataIndex: "created_at",
            key: "created_at",
        },
        {
            title: t("cobranzas.records"),
            dataIndex: "registros",
            key: "registros",
            align: "center",
        },
        {
            title: t("cobranzas.base_name"),
            dataIndex: "nombre_base",
            key: "nombre_base",
            align: "center",
        },
        {
            title: t("cobranzas.status"),
            dataIndex: "activa",
            key: "activa",
            align: "center",
        },
        {
            title: t("common.action"),
            key: "action",
            fixed: "right",
            align: "center",
        },
    ];


    return {
        columns,
    };
};

export default fields;
