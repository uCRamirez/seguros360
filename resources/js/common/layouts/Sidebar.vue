<template>
    <div class="right-sidebar" :style="{
        borderLeft: themeMode == 'dark' ? '1px solid #303030' : '1px solid #f0f0f0',
    }">
        <!-- iframe de sofphone de ucontact -->
        <iframe src="https://desarrollocr.ucontactcloud.com/uphone/" width="100%" frameborder="0" scrolling="no"
            allow="camera;microphone" id="ucontact" ref="iframe" @load="onIframeLoad" style="height: 100vh"></iframe>
    </div>
</template>
<script>
import { defineComponent, onMounted, onUnmounted, ref, computed } from "vue";
import common from "../../common/composable/common";
import { useRouter } from "vue-router";

import { useStore } from "vuex";
import apiAdmin from "../../../js/common/composable/apiAdmin";
import { useI18n } from "vue-i18n";


export default defineComponent({
    props: {
        leadId: {
            default: "",
        },
    },
    setup(props, { emit }) {
        const { rightSidebarValue, user, themeMode } = common();
        const loading = ref(true);
        const iframe = ref(null);
        const { t } = useI18n();
        const store = useStore();
        const router = useRouter();
        //const routePath = computed(() => route.name);

        const { addEditRequestAdmin } = apiAdmin();

        // Define the event handler
        // AQUI SE OBTIENE LA INFORMACION QUE RETORNA EL SOFTPHONE EN LLAMDAS ENTRANTES Y SALIENTES
        const handleMessageEvent = (e) => {
            if (e.data.params) {
                axiosAdmin
                    .post("uphone-calls/save", {
                        ...e.data.params,
                        lead_id: props.leadId,
                    })
                    .then((response) => {
                        store.commit(
                            "auth/updateUphoneCallReloadString",
                            Math.random() * 20
                        );
                    });
            }

            switch (e.data.action) {
                case "gettingCall":
                    gettingCallFunction(e);
                    break;
                case "makeCall":
                    makeCallFunction(e);
                    break;
                case "finishedCall":
                    // console.log('finishedCall');
                    break;
                default:
                    break;
            }

        };

        function gettingCallFunction(e) {
            let phone = e.data.params.number;
            let campaign = e.data.params.campaign;
            axiosAdmin
                .post("campaigns/find-campaigns-homologate", { campaign })
                .then((res) => {
                    const array =
                        res.data && res.data.length > 0 && res.data[0] !== undefined
                            ? (typeof res.data[0] === "string"
                                ? JSON.parse(res.data[0])
                                : (Array.isArray(res.data[0])
                                    ? res.data[0]
                                    : [res.data[0]]))
                            : [];
                    // Si encuentra solo una campana homologada
                    if (array.length === 1) {
                        campaign = array[0].name;
                        axiosAdmin
                            .post("leads/find-by-phone-campaign", { phone, campaign })
                            .then((res) => {
                                if (res.data?.x_lead_id) {

                                    if(res.data.x_lead_id.length  === 1){
                                         // Se encontró el lead; redirigimos a la vista CRM con su ID
                                        router.push({
                                            name: "admin.formsUCB.CRM",
                                            params: { id: res.data.x_lead_id[0] },
                                        });
                                    }else{
                                        console.warn('Mas de un lead con el numero telefonico asignado - INBOUN CALL');
                                    }
                                   
                                } else if (res.data?.x_campaign_id) {
                                    // Lead no encontrado, se debe crear uno nuevo
                                    addEditRequestAdmin({
                                        url: "campaigns/take-action",
                                        data: { x_campaign_id: res.data.x_campaign_id },
                                        successMessage: t("campaign.new_lead_added"),
                                        success: (res) => {
                                            router.push({
                                                name: "admin.formsUCB.CRM",
                                                params: { id: res.x_lead_id },
                                            });
                                        },
                                    });
                                }
                            })
                            .catch((error) => {
                                console.error("Error en find-by-phone-campaign:", error);
                            });

                    } else if (array.length > 1) {
                        console.warn('Campana de uContact homologada con mas de una campana de LeadPro. - INBOUND CALL');
                    }else{
                        console.warn('Campaña de uContact no homologada con Campaña de LeadPro');
                    }
                })
                .catch((error) => {
                    console.error("Error en find-campaigns-homologate:", error);
                });
        }

        function makeCallFunction(e) {
            let phone = e.data.params.number;
            let campaign = e.data.params.campaign;
            axiosAdmin
                .post("campaigns/find-campaigns-homologate", { campaign })
                .then((res) => {

                    const array =
                        res.data && res.data.length > 0 && res.data[0] !== undefined
                            ? (typeof res.data[0] === "string"
                                ? JSON.parse(res.data[0])
                                : (Array.isArray(res.data[0])
                                    ? res.data[0]
                                    : [res.data[0]]))
                            : [];

                    // Si encuentra solo una campana homologada
                    if (array.length === 1) {
                        campaign = array[0].name;
                        axiosAdmin
                            .post("leads/find-by-phone-campaign", { phone, campaign }) // Este endPoint retorna el primer registro que encuentra
                            .then((res) => {
                                if (res.data?.x_lead_id) {
                                    // Se encontró el lead; redirigimos a la vista CRM con su ID
                                    router.push({
                                        name: "admin.formsUCB.CRM",
                                        params: { id: res.data.x_lead_id },
                                    });
                                } else if (res.data?.x_campaign_id) {
                                    // Lead no encontrado, se debe crear uno nuevo
                                    addEditRequestAdmin({
                                        url: "campaigns/take-action",
                                        data: { x_campaign_id: res.data.x_campaign_id },
                                        successMessage: t("campaign.new_lead_added"),
                                        success: (res) => {
                                            router.push({
                                                name: "admin.formsUCB.CRM",
                                                params: { id: res.x_lead_id },
                                            });
                                        },
                                    });
                                }
                            })
                            .catch((error) => {
                                console.error("Error en find-by-phone-campaign:", error);
                            });

                    } else if (array.length > 1) {
                        console.warn('Campana de uContact homologada con mas de una campana de LeadPro. - OUTBOUND CALL');
                    }else{
                        console.warn('Campaña de uContact no homologada con Campaña de LeadPro');
                    }

                })
                .catch((error) => {
                    console.error("Error en find-campaigns-homologate:", error);
                });

        }

        // function cleanCampaignName(name) {
        //     name = name.replace(/[-\s]*(<\-|->)\s*$/i, '');
        //     name = name.replace(/[_\s]*(in|out)\s*$/i, '');
        //     return name;
        // }


        // axiosAdmin
        // .post("leads/find-by-phone", { phone })
        // .then((res) => {
        // if (res.data.x_lead_id) {
        //     router.push({
        //         name: "admin.formsUCB.CRM",
        //         params: { id: res.data.x_lead_id },
        //     });
        // } else {
        // Lead no encontrado, creamos uno nuevo
        // addEditRequestAdmin({
        //     url: "campaigns/take-action",
        //     data: { x_campaign_id: campaign.xid },
        //     successMessage: t("campaign.new_lead_added"),
        //     success: (res) => {
        //         route.push({
        //             name: "admin.formsUCB.CRM",
        //             params: { id: res.data.x_lead_id },
        //         });
        //     },
        // });
        // }
        // });




        // const handleMessageEvent = (e) => {
        //     if (e.data.params) {
        //         axiosAdmin
        //             .post("uphone-calls/save", {
        //                 ...e.data.params,
        //                 lead_id: props.leadId,
        //             })
        //             .then((response) => {
        //                 store.commit(
        //                     "auth/updateUphoneCallReloadString",
        //                     Math.random() * 20
        //                 );
        //             });
        //     }
        // };

        onMounted(() => {
            window.addEventListener("message", handleMessageEvent);
            // window.addEventListener("message", receiveMessage, false);
            // iframe.value.addEventListener("load", sendMessage, false);
            // onIframeLoad();
        });

        onUnmounted(() => {
            // console.log("unmounted");
            // Remove the event listener to prevent memory leaks
            window.removeEventListener("message", handleMessageEvent);
        });

        // const sendMessage = () => {
        //     const iframe = this.$refs.myIframe;
        //     const message = {
        //         ucontact_user: user.value.ucontact_user,
        //         ucontact_password: user.value.ucontact_password,
        //     };
        //     iframe.contentWindow.postMessage(
        //         message,
        //         "https://desarrollocr.ucontactcloud.com/uphone/"
        //     );
        // };

        // const receiveMessage = (event) => {
        //     if (
        //         event.origin !==
        //         "https://desarrollocr.ucontactcloud.com/uphone/"
        //     ) {
        //         return;
        //     }
        //     console.log("Received message from iframe:", event.data);
        // };

        // iframe window code

        // window.addEventListener(
        //     "message",
        //     function (event) {
        //         // Ensure the message is from the expected origin
        //         if (event.origin !== "http://parent-domain.com") {
        //             return;
        //         }

        //         const credentials = event.data;
        //         if (credentials.token) {
        //             console.log("Received token:", credentials.token);
        //             // Use the token to authenticate requests or perform other actions
        //         }
        //     },
        //     false
        // );

        const onIframeLoad = () => {
            const iframeContent = document.getElementById("ucontact");
            // const iframeDocument =
            //     iframeContent.contentDocument || iframeContent.contentWindow;
            loading.value = false;

            // Assuming you have a form in the iframe that requires credentials
            // var uContactUser = iframeDocument.getElementById("input-10");
            // var uContactPassword = iframeDocument.getElementById("input-12");

            // if (uContactUser && uContactPassword) {
            //     uContactUser.value = user.value.ucontact_user;
            //     uContactPassword.value = user.value.ucontact_password;
            // }
        };

        return { rightSidebarValue, loading, onIframeLoad, iframe, themeMode };
    },
});
</script>

<style lang="less">
.right-sidebar {
    height: calc(100vh);
    position: fixed;
    width: -webkit-fill-available;
}

.loader {
    position: absolute;
    left: 90%;
    top: 32%;
    width: 100px;
    height: 100px;
    margin-left: -50px;
    margin-top: -50px;
    border: 16px solid #f3f3f3;
    border-radius: 50%;
    border-top: 16px solid #3498db;
    width: 120px;
    height: 120px;
    -webkit-animation: spin 2s linear infinite;
    animation: spin 2s linear infinite;
}

.content {
    position: absolute;
    left: 87.5%;
    top: 40%;
    width: 200px;
    height: 100px;
    // font-weight: bold;
    font-size: 20px;
}

@-webkit-keyframes spin {
    0% {
        -webkit-transform: rotate(0deg);
    }

    100% {
        -webkit-transform: rotate(360deg);
    }
}

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }

    100% {
        transform: rotate(360deg);
    }
}

@media only screen and (max-width: 1150px) {
    .ant-layout-sider.ant-layout-sider-collapsed {
        left: -80px !important;
    }
}
</style>
