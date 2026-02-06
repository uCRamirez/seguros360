import { useI18n } from "vue-i18n";
import { ref } from "vue";

const fields = () => {
    const { t } = useI18n();

    // COLUMNAS DE LA TABLA DE REGISTROS ENCONTRADOS
    const columns = [
        {
            title: t("cobranzas.wallet"),
            dataIndex: "indetificador_cartera",
            key: "indetificador_cartera",
        },
        {
            title: t("cobranzas.document"),
            dataIndex: "document",
            key: "document",
            align: "center",
        },
        {
            title: t("cobranzas.name"),
            dataIndex: "name",
            key: "name",
            align: "center",
        },
        {
            title: t("cobranzas.phone") + '1',
            dataIndex: "tel1",
            key: "tel1",
            align: "center",
        },
        {
            title: t("cobranzas.phone") + '2',
            dataIndex: "tel2",
            key: "tel2",
            align: "center",
        },
        {
            title: t("cobranzas.phone") + '3',
            dataIndex: "tel3",
            key: "tel3",
            align: "center",
        },
        {
            title: t("cobranzas.email"),
            dataIndex: "email",
            key: "email",
            align: "center",
        },
        {
            title: t("cobranzas.adviser"),
            dataIndex: "asignado_a",
            key: "asignado_a",
            align: "center",
        }
    ];

    const filters = ref({
        identificador_cartera : null,
        identificador_cartera : null,
        nombre_cliente: null,
        telefono : null,
        nombre_base : null,
        email: null,
        agente: null,
        sin_asignar: false,
        no_gestionado: false,
        no_gest_mes: false,
        no_completadas: false,
        proyecto: null,
        agentes: []
    });


    return {
        columns,
        filters,
    };
};

export default fields;
