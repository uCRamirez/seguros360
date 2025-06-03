// DO NOT DELETE
import ucb_framework from "../../../../framework/ucb_framework";
import $ from 'jquery';
import { reactive, nextTick, watch, ref } from "vue";
import { notification } from "ant-design-vue";
import { useI18n } from "vue-i18n";
import dataForm from "../../../../../../../common/composable/dataForm";
import common from "../../../../../../../common/composable/common";
import dayjs from 'dayjs';
import { useStopwatch } from "vue-timer-hook";
// ** END DO NOT DELETE


function getEmptyClient() {
    return {
    id: null,
    xid: null,
    campaign: { id: null, name: '', status: '', xid: '' },
    lead_status: { id: null, xid: null, name: '', type: '' },
    cedula: '',
    nombre: '',
    apellido1: '',
    apellido2: '',
    estado_civil: { id: null, name: '', type: '', xid: null },
    hijos: 0,
    fechaNacimiento: '',
    edad: 0,
    nacionalidad: '',
    tel1: '',
    tel2: '',
    tel3: '',
    tel4: '',
    tel5: '',
    tel6: '',
    email: '',
    provincia: null,
    canton: null,
    distrito: null,
    time_taken: 0,
    nombreBase: '',
    tarjeta: '',
    assign_to: { id: null, name: '', xid: '' },
    isNew: true,
    toManage: false,
    managing: false,
    call_log:{}
    }
};


// Information client to update in the form 
export const crmState = reactive({
    client: getEmptyClient()
});

// Reactive index tab
export const crmGoTab = reactive({
    tab: {
        value: '1'
    }
});

// Reactive data for the tipifications table
const tableScheduled = reactive({
    data: [],
    loading: false,
    selectedRowKeys: [],
});

// Reactive data for the tipifications table
const tableTypifications = reactive({
    data: [],
    pagination: {
        current: 1,
        pageSize: 5,
        total: 0,
        showSizeChanger: false,
    },
    loading: false,
    selectedRowKeys: [],
});

// Reactive data for the tipifications table
const tableClienteSerch = reactive({
    data: [],
    pagination: {
        current: 1,
        pageSize: 10,
        total: 0,
        showSizeChanger: false,
    },
    loading: false,
    selectedRowKeys: [],
});


// Information client to update in the form 
export const crmSerch = reactive({
    clientSerch: {
        campana: null,
        leadStatus: null,
        document: '',
        name: '',
        email: '',
        phone: '',
    }
});

var activeKey = ref("lead_details");


// framework function that we are or we have to use in the form
const { UCB_executeQuery, UCB_audit, UCB_uploadFile } = ucb_framework();
// ************************************************************ //


const functionsCRM = () => {
    // Translations to use
    const { t } = useI18n();
    const { permsArray } = common();
    const timer = useStopwatch(0, false); // para parar el reloj timer.pause() para iniciar el reloj timer.start() 
    // From the dataForms.js, we get the campaings and the tipifications levels to the form
    const {
        //En uso
        myAgent,
        myId,
        fetchUserCampaigns,
        fetchLeadStatus,
        fetchLocalidades,
        locations,
        provinceOptions,
        cantonOptions,
        districtOptions,
        /////////////////
    } = dataForm();


    // funciones actuales en uso

    // To show notifications
    // Parameters
    // Type => type of notification to display
    // message => key of the notification message
    // description => key of the decription 
    const showNotification = (type, message, description) => {
        switch (type) {
            case 'info':
                notification.info({ message: t(`common.${message}`), description: t(`lead.${description}`) });
                break;

            case 'success':
                notification.success({ message: t(`common.${message}`), description: t(`lead.${description}`) });
                break;

            case 'warning':
                notification.warning({ message: t(`common.${message}`), description: t(`lead.${description}`) });
                break;

            case 'error':
                notification.error({ message: t(`common.${message}`), description: t(`lead.${description}`) });
                break;
            default:
                break;
        }
    };

    // Funcion para validar parametros de un objeto
    const validarParametros = (val) => val !== undefined && val !== null && val.trim() !== '';
    const validarParametrosSelect = (val) => val !== undefined && val !== null;


    // Funcion para buscar informacion de leads
    const serchInformationClient = async () => {
        //clearSerchInformation();
        clearClientSelection();
        const { document, name, email, phone, campana, leadStatus } = crmSerch.clientSerch;

        const filters = [];

        if (validarParametros(document)) {
            filters.push(`cedula eq "${document.trim()}"`);
        }
        if (validarParametros(name)) {
            filters.push(`(nombre lk "${name.trim()}" or apellido1 lk "${name.trim()}" or apellido2 lk "${name.trim()}")`);
        }
        if (validarParametros(email)) {
            filters.push(`email eq "${email.trim()}"`);
        }
        if (validarParametros(phone)) {
            filters.push(`(tel1 lk "${phone.trim()}" or tel2 lk "${phone.trim()}" or tel3 lk "${phone.trim()}" or tel4 lk "${phone.trim()}" or tel5 lk "${phone.trim()}" or tel6 lk "${phone.trim()}")`);
        }
        if (validarParametrosSelect(campana)) {
            filters.push(`campaign_id eq "${campana}"`);
        }
        if (validarParametrosSelect(leadStatus)) {
            filters.push(`lead_status_id eq "${leadStatus}"`);
        }


        // Filtro para que cada agente vea solo lo asignado a el
        !permsArray.value?.includes('admin') && filters.push(`assign_to eq "${myId.value}"`);


        const filtersString = filters.join(' and ');

        const fields = [
            // campos base del lead
            'id', 'xid', 'cedula', 'nombre', 'apellido1', 'apellido2',
            'email', 'tel1', 'tel2', 'tel3', 'tel4', 'tel5', 'tel6',
            'provincia', 'canton', 'distrito', 'hijos', 'fechaNacimiento', 'edad', 'nacionalidad', 'nombreBase','tarjeta',
            'estadoCivil_id','campaign_id', 'lead_status_id', 'assign_to', 'time_taken',
            // expansiones
            'campaign{id,xid,name,status,detail_fields,remaining_leads,total_leads,started_on,completed_on}',
            'estadoCivil{id,xid,name,type}',
            'leadStatus{id,xid,name,type}',
            'assignTo{id,xid,name}'
        ].join(',');

        const leadsURL =
            `leads?fields=${encodeURIComponent(fields)}` +
            `&filters=${encodeURIComponent(filtersString)}`;

        try {
            const { data } = await axiosAdmin.get(leadsURL);
            fillSearchTable(data);
        } catch (err) {
            console.error('Error al buscar leads con expansiones:', err);
        }
    };


    // Function to fill the table with the data
    const fillSearchTable = (data) => {
        if (data.length >= 1) {
            tableClienteSerch.data = data;
            tableClienteSerch.pagination.total = data.length;
            tableClienteSerch.selectedRowKeys = [];

        } else {
            tableClienteSerch.data = [];
            tableClienteSerch.pagination.total = 0;
            tableClienteSerch.selectedRowKeys = [];

            showNotification('info', 'information', 'not_found');
        }
    };

    // For the pagination change of the client sech table
    const handleClientSerchTableChange = (pagination) => {
        tableClienteSerch.pagination.current = pagination.current;
        tableClienteSerch.pagination.pageSize = pagination.pageSize;
    };

    // To clear the search
    const clearSerchInformation = () => {
        crmSerch.clientSerch.campana = null;
        crmSerch.clientSerch.leadStatus = null;
        crmSerch.clientSerch.document = '';
        crmSerch.clientSerch.name = '';
        crmSerch.clientSerch.email = '';
        crmSerch.clientSerch.phone = '';
        tableClienteSerch.data = [];
        tableClienteSerch.pagination.total = 0;

        tableClienteSerch.selectedRowKeys = [];

        clearClientSelection();
    };

    // Function to execute when we select a record in the search table
    const onClientSelected = (client) => {
        // Para cada campo declarado en el estado...
        Object.keys(crmState.client).forEach(field => {
            // ...si client trae esa misma propiedad, la copiamos
            if (client[field] !== undefined) {
                crmState.client[field] = client[field];
            }
        });

        // Y finalmente los flags que siempre van así
        crmState.client.isNew = false;
        crmState.client.toManage = true;

        timer.reset(crmState.client.time_taken, false);
    };

    // Funcion para calcular edad
    function calcularEdad() {
        const fechaNacimiento = crmState.client.fechaNacimiento;
        if (!fechaNacimiento) return null;

        const today = dayjs();
        const birthDate = dayjs(fechaNacimiento);
        let edad = today.year() - birthDate.year();

        if (
            today.month() < birthDate.month() ||
            (today.month() === birthDate.month() && today.date() < birthDate.date())
        ) {
            edad--;
        }

        crmState.client.edad = edad;
    }

    const clearClientSelection = () => {
        // Resetea todo el objeto cliente al estado inicial
        Object.assign(crmState.client, getEmptyClient())
      
        // Resetea el timer si lo necesitas
        timer.reset(crmState.client.time_taken, false)
    };

    // To manage a selected client from the search table
    const clientToManage = async () => {
        crmState.client.toManage = false;
        crmState.client.managing = true;
        timer.reset(crmState.client.time_taken, true);
        activeKey.value = "lead_details";
    };

    // Watcher 1: cuando cambie la provincia
    watch(
        () => crmState.client.provincia,
        provincia => {

            if (provincia) {
                // poblo cantones únicos de esa provincia
                cantonOptions.value = Array.from(
                    new Set(
                        locations.value
                            .filter(l => l.provincia === provincia)
                            .map(l => l.canton)
                    )
                );
            } else {
                crmState.client.canton = null;
                crmState.client.distrito = null;
                cantonOptions.value = [];
                districtOptions.value = [];
            }
            // siempre limpio distritos aquí
            districtOptions.value = [];
        },
        { immediate: true }
    );

    // Watcher 2: cuando cambie el cantón
    watch(
        () => crmState.client.canton,
        canton => {

            if (canton) {
                // poblo distritos de la provincia + cantón
                districtOptions.value = locations.value
                    .filter(
                        l =>
                            l.provincia === crmState.client.provincia &&
                            l.canton === canton
                    )
                    .map(l => l.distrito);
            } else {
                districtOptions.value = [];
                crmState.client.distrito = null;

            }
        },
        { immediate: true }
    );

    /////////////

    // Funciones a retornar para ser usadas en TakeLeadAction
    return {
        // En uso
        timer,
        tableClienteSerch,
        crmState, // informacion del cliente
        crmSerch, // para buscar informacion del cliente
        serchInformationClient, // funcion de buscar informacion de leads
        fetchUserCampaigns, // Obtener las campanas a las que pertene el agente desde DataForm
        fetchLeadStatus, // Obtener los lead status
        onClientSelected,// cuando se selecciona un cliente de la tabla de busqueda
        calcularEdad, // funcion para calcular la edad con base en la fecha seleccionada
        fetchLocalidades, // Obtener las localides actuales Provincia Canton Distrito
        clearClientSelection, // limpiar la informacion del cliente cuando se deselecciona en la tb busqueda
        provinceOptions,
        cantonOptions,
        districtOptions,
        activeKey,
        clientToManage,
        clearSerchInformation,
        ////////
    };
};

export default functionsCRM;