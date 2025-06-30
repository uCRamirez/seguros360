import { ref } from "vue";
import { useI18n } from "vue-i18n";
import { forEach } from "lodash-es";
import common from "../../../common/composable/common";

const fields = (props) => {
    const { convertStringToKey, getCampaignUrl, user } = common();
    const leadUrl =
        "lead{id,xid,reference_number,cedula,nombre,apellido1,apellido2,fechaNacimiento,edad,nacionalidad,nombreBase,tel1,email,lead_data,started,campaign_id,x_campaign_id,time_taken,first_action_by,x_first_action_by,last_action_by,x_last_action_by},lead:campaign{id,xid,name,status,plantilla_calidad_id},lead:firstActioner{id,xid,name},lead:lastActioner{id,xid,name}";
    // const formFieldNamesUrl = "form-field-names/all";
    const url = 
        `lead-logs?fields=id,xid,log_type,time_taken,next_contact,date_time,user_id,x_user_id,notes,phone,message,lead_id,x_lead_id,notes_file,notes_file_url,${leadUrl},campaign_id,notes_typification_id_1,x_notes_typification_id_1,notes_typification_id_2,x_notes_typification_id_2,notes_typification_id_3,x_notes_typification_id_3,notes_typification_id_4,x_notes_typification_id_4,notes_typification_name_1,notes_typification_name_2,notes_typification_name_3,notes_typification_name_4,isSale{idVenta,idNota,idLead,telVenta,estadoVenta,tarjeta,aplicaBeneficiarios,cantidadBeneficiarios,beneficiarios,aplicaBeneficiariosAsist,cantidadBeneficiariosAsist,beneficiariosAsist,montoTotal,calidad},isSale:productos{x_id_producto,idProducto,cantidadProducto,precio},isSale:user{id,xid,name,profile_image,profile_image_url}`;//,isSale:productos{id,xid,name,coverage,price,internal_code}
    const allCalidadTemplates = ref([]);
    const allAccionesCalidad = ref([]);
    const allMotivosCalidad = ref([]);
    const addEditUrl = "lead-logs";
    const hashableColumns = ["lead_id", "campaign_id", "user_id", "notes_typification_id_1", "notes_typification_id_2", "notes_typification_id_3","notes_typification_id_4"];
    const urlProductos =
    "products?fields=id,xid,name,coverage,price,campaign_id,x_campaign_id,product_type,tax_rate,tax_label,image,image_url,internal_code,category_id,x_category_id,categories{id,xid,name},campaigns{id,xid,name}";
    const urlCalidadTemplates = `plantillas-calidad?fields=id,xid,nombre,descripcion,activo,variables{id,xid,tipo,nombre,descripcion,nota_maxima}&filters=activo eq 1&order=id asc&limit=1000`
    const urlAccionesCalidad = `acciones-calidad?fields=id,xid,nombre,descripcion,tipo&limit=5000`;
    const urlMotivosCalidad = `motivos-calidad?fields=id,xid,motivo&limit=5000`;

    const { t } = useI18n();
    const initData = {
        notes: "",
        notes_file: undefined,
        notes_file_url: undefined,
        log_type: "notes",
        notes_typification_id_1: undefined,
        notes_typification_id_2: undefined,
        notes_typification_id_3: undefined,
        notes_typification_id_4: undefined,
        is_sale: undefined,
        phone: "",
        message: ""
    };
    const columns = ref([]);
    const allCampaigns = ref([]);
    const allUsers = ref([]);
    const allProductos = ref([]);

    const filterableColumns = [
        {
            key: "leads.id",
            value: t("uphone_calls.client_id"),
        },
        {
            key: "leads.cedula",
            value: t("lead.document"),
        },
        {
            key: "leads.nombre",
            value: t("lead.name"),
        },
        {
            key: "leads.apellido1",
            value: t("lead.first_last_name"),
        },
        {
            key: "leads.apellido2",
            value: t("lead.second_last_name"),
        },
        {
            key: "leads.tel1",
            value: `${t("lead.phone")} 1`,
        },
        {
            key: "leads.tel2",
            value: `${t("lead.phone")} 2`,
        },
        {
            key: "leads.tel3",
            value: `${t("lead.phone")} 3`,
        },
        {
            key: "leads.tel4",
            value: `${t("lead.phone")} 4`,
        },
        {
            key: "leads.tel5",
            value: `${t("lead.phone")} 5`,
        },
        {
            key: "leads.tel6",
            value: `${t("lead.phone")} 6`,
        },
        {
            key: "campaigns.name",
            value: t("campaign.campaign"),
        },
    ];

    const getPrefetchData = () => {
        const campaignsUrl = getCampaignUrl();
        const campaignsPromise = axiosAdmin.get(campaignsUrl);
        const calidadTemplatesPromise = axiosAdmin.get(urlCalidadTemplates);
        const accionesCalidadPromise = axiosAdmin.get(urlAccionesCalidad);
        const motivosCalidadPromise = axiosAdmin.get(urlMotivosCalidad);
        const staffMembersPromise = axiosAdmin.get(`all-users`);
        const productosPromise = axiosAdmin.get(urlProductos);

        return Promise.all([
            campaignsPromise,
            calidadTemplatesPromise,
            accionesCalidadPromise,
            motivosCalidadPromise,
            staffMembersPromise,
            productosPromise,
        ]).then(
            ([
                campaignsResponse,
                calidadTemplatesResponse,
                accionesCalidadResponse,
                motivosCalidadResponse,
                staffMembersResponse,
                productosResponse,
            ]) => {
                allCampaigns.value = campaignsResponse.data;
                allCalidadTemplates.value = calidadTemplatesResponse.data;
                allAccionesCalidad.value = accionesCalidadResponse.data;
                allMotivosCalidad.value = motivosCalidadResponse.data;
                allUsers.value = staffMembersResponse.data.users;
                allProductos.value = productosResponse.data;

                var newColumnsArray = [];

                if (props.showLeadDetails) {
                    var newColumnsArray = [
                        {
                            title: `${t("lead.id")} ${t("lead_notes.sale")}`,
                            dataIndex: "is_sale.idVenta",
                        },
                        {
                            title: t("uphone_calls.client_id"),
                            dataIndex: "id",
                        },
                        {
                            title: t("lead.document"),
                            dataIndex: "cedula",
                        },
                        {
                            title: t("lead.name"),
                            dataIndex: "nombre",
                        },
                        {
                            title: t("lead.campaign"),
                            dataIndex: "campaign",
                        },
                        {
                            title: t("common.status"),
                            dataIndex: "is_sale.estadoVenta",
                        },
                        {
                            title: t("menu.quality"),
                            dataIndex: "is_sale.calidad",
                        },
                    ];

                    // Showing form fields if props.showFormFields
                    // sets to true
                    if (props.showFormFields) {
                        forEach(
                            calidadTemplatesResponse.data.data,
                            (formFieldName) => {
                                newColumnsArray.push({
                                    title: formFieldName.field_name,
                                    dataIndex: convertStringToKey(
                                        formFieldName.field_name
                                    ),
                                });
                            }
                        );
                    }
                }

                newColumnsArray.push({
                    title: t("common.notes"),
                    dataIndex: "notes",
                    width:
                        props.showLeadDetails && props.showFormFields
                            ? "30%"
                            : "40%",
                });


                if (props.showActionButton) {
                    newColumnsArray.push({
                        title: t("common.action"),
                        dataIndex: "action",
                    });
                }

                columns.value = newColumnsArray;
            }
        );
    };

    return {
        url,
        addEditUrl,
        initData,
        columns,
        filterableColumns,
        hashableColumns,
        allCalidadTemplates,
        allAccionesCalidad,
        allMotivosCalidad,
        allCampaigns,
        allProductos,
        allUsers,
        getPrefetchData,
    };
};

export default fields;
