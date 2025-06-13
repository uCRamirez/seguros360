import { ref, reactive, computed, watch } from "vue";
import ucb_framework from "../../main/views/formsUCB/framework/ucb_framework";
const { UCB_executeQuery, UCB_audit, UCB_uploadFile } = ucb_framework();

var myAgent = ref('');
var myId = ref('');
const locations = ref([]);
const provinceOptions = ref([]);
const cantonOptions = ref([]);
const districtOptions = ref([]);
const agenteCampanas = ref([]);


// This js return general information for al the forms
const dataForm = () => {


    // Actuales
    // Get the user campin
    const fetchUserCampaigns = async () => {
        getDataUser();
        try {
            const response = await axiosAdmin.get("call-managers");
            return response.data;
        } catch (error) {
            console.error("Error fetching user campaigns:", error);
        }
    };

    const fetchLeadStatus = async () => {
        try {
            const response = await axiosAdmin.get(`lead-status?fields=id,xid,name,color,type`);
            return response.data;
        } catch (error) {
            console.error("Error fetching leads status:", error);
        }
    };

    // Obtener datos del agente logueado para hacer las respectivas validaciones en las busquedas
    const getDataUser = async () => {
        myAgent.value = JSON.parse(localStorage.getItem("auth_user") || "{}").name;
        myId.value = JSON.parse(localStorage.getItem("auth_user") || "{}").id;
    };

    // obtener las localidades para el crm
    const fetchLocalidades = async () => {
        try {
            const resp = await axiosAdmin.get(
                "localidades?fields=provincia,canton,distrito&limit=1000"
            );
            locations.value = resp.data;
            // llenamos provincias únicas
            provinceOptions.value = Array.from(
                new Set(locations.value.map(l => l.provincia))
            );
        } catch (e) {
            console.error("Error fetching localidades:", e);
        }
    };

    // obtener las campanas a las que pertenece el user
    const fetchAgenteCampanas = async () => {
        try {
            axiosAdmin.get(`campaigns/${myId.value}/users`).then(res => {
                agenteCampanas.value = res;
            });     
        } catch (e) {
            console.error("Error fetching agente campanas:", e);
        }
    };



    /////////////////////////


    return {
        //En uso
        myAgent,
        myId,
        fetchLeadStatus, // Estatus registrado
        fetchUserCampaigns, // Campanas del usuario logueado
        fetchLocalidades, // Localidades de Provincia, Canton y Distrito
        fetchAgenteCampanas, // Campanas del agente
        locations,
        provinceOptions,
        cantonOptions,
        districtOptions,
        agenteCampanas, // Campanas del usuario
        ////////
    };
};

export default dataForm;
