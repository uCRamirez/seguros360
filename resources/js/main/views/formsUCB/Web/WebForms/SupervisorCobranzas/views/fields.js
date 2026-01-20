import { useI18n } from "vue-i18n";

const fields = () => {
    const { t } = useI18n();

    // COLUMNAS DE LA TABLA DE BUSQUEDA
    const columns = [
        {
            title: t("lead.document"),
            dataIndex: "cedula",
            key: "cedula",
        },
        {
            title: t("lead.name"),
            dataIndex: "nombre",
            key: "nombre",
        },
        {
            title: t("lead.phone"),
            dataIndex: "tel1",
            key: "tel1",
        },
        {
            title: t("lead.email"),
            dataIndex: "email",
            key: "email",
        },
        {
            title: t("lead.proyect"),
            dataIndex: "campaign",
            key: "campaign",
        },
        {
            title: t("lead.agent"),
            dataIndex: "assign_to",
            key: "assign_to",
        },
    ];

    // COLUMNAS DE LA TABLA DE BASES DE CLIENTES
    const customerColumns = [
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
        customerColumns,
    };
};

export default fields;
