<template>
    <div class="relative" ref="dropdownContainer">
        <div @click="toggleDropdown" class="cursor-pointer">
            <BellOutlined class="text-2xl text-gray-700" />
            <span v-if="unreadCount > 0"
                class="absolute top-0 right-0 bg-red-600 text-white rounded-full text-xs w-4 h-4 flex items-center justify-center">
                {{ unreadCount }}
            </span>
        </div>

        <transition enter-active-class="transition ease-out duration-100"
            enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95">
            <div v-if="isOpen" class="absolute right-0 mt-2 w-80 bg-white shadow-lg rounded-lg z-10 border">
                <div class="p-3 font-bold border-b">Notificaciones</div>

                <div v-if="isLoading" class="p-8 text-center text-gray-500">
                    <p>Cargando...</p>
                </div>

                <div v-else-if="error" class="p-8 text-center text-red-500">
                    <p>{{ error }}</p>
                </div>

                <ul v-else-if="notifications.length > 0" class="max-h-96 overflow-y-auto">
                    <li v-for="note in notifications" :key="note.id" class="border-b last:border-b-0">
                        <a :href="note.data.url" @click="isOpen = false"
                            class="block px-4 py-3 hover:bg-gray-100 text-sm">
                            <p class="font-semibold text-gray-800">{{ note.data.title }}</p>
                            <p class="text-gray-600">{{ note.data.message }}</p>
                        </a>
                    </li>
                </ul>

                <div v-else class="px-4 py-8 text-center text-gray-500">
                    <p>No tienes notificaciones nuevas.</p>
                </div>

                <div v-if="!isLoading && !error && notifications.length > 0" class="border-t">
                    <button @click="markAllAsRead"
                        class="w-full text-center py-2 text-sm text-blue-600 hover:bg-gray-50 rounded-b-lg">
                        Marcar todas como leídas
                    </button>
                </div>
            </div>
        </transition>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useStore } from 'vuex'; // Para acceder al store de Vuex
import { BellOutlined } from "@ant-design/icons-vue";
// import axiosAdmin from '@/path/to/your/axios/config'; // Asegúrate de importar tu instancia de Axios

// --- STATE ---
const notifications = ref([]);
const isOpen = ref(false);
const isLoading = ref(false);
const error = ref(null);
const dropdownContainer = ref(null); // Ref para el div principal
const store = useStore(); // Hook para acceder al store

// --- COMPUTED ---
const unreadCount = computed(() => notifications.value.length);

// --- METHODS ---
const toggleDropdown = () => {
    isOpen.value = !isOpen.value;
    // Si abrimos el dropdown y está vacío (y no ha habido un error previo), cargamos las notificaciones.
    if (isOpen.value && notifications.value.length === 0 && !error.value) {
        fetchNotifications();
    }
};

const fetchNotifications = async () => {
    isLoading.value = true;
    error.value = null;
    try {
        // Simulación de llamada a API. Descomenta para usar tu código.
        // const res = await axiosAdmin.get('/notifications');
        // this.notifications = res.data.unread;

        // Datos de ejemplo para demostración:
        await new Promise(resolve => setTimeout(resolve, 1000)); // Simular delay de red
        notifications.value = [
            { id: 1, data: { title: 'Nuevo Pedido', message: 'Has recibido un nuevo pedido #1234.', url: '#' } },
            { id: 2, data: { title: 'Actualización de Perfil', message: 'Tu información ha sido actualizada.', url: '#' } },
        ];

    } catch (err) {
        console.error("Error al obtener notificaciones:", err);
        error.value = "No se pudieron cargar las notificaciones.";
    } finally {
        isLoading.value = false;
    }
};

const markAllAsRead = async () => {
    try {
        // await axiosAdmin.post('/api/notifications/mark-read');
        notifications.value = []; // Limpia las notificaciones en la UI
        isOpen.value = false; // Cierra el dropdown
    } catch (err) {
        console.error("Error al marcar notificaciones como leídas:", err);
        // Opcional: mostrar un error al usuario con un toast/snackbar
    }
};

// --- LOGIC PARA CERRAR AL HACER CLIC AFUERA ---
const handleClickOutside = (event) => {
    if (dropdownContainer.value && !dropdownContainer.value.contains(event.target)) {
        isOpen.value = false;
    }
};

// --- LIFECYCLE HOOKS ---
onMounted(() => {
    // Carga inicial de notificaciones
    fetchNotifications();

    // Añadir listener para clics fuera del componente
    document.addEventListener('mousedown', handleClickOutside);

    // Lógica de Laravel Echo (asegúrate de que Echo esté configurado)
    if (window.Echo) {
        const userId = store.state.user?.id;
        if (userId) {
            window.Echo.private(`App.Models.User.${userId}`)
                .notification((notification) => {
                    // Añade la nueva notificación al principio de la lista
                    notifications.value.unshift(notification);
                    // Opcional: mostrar un toast de "nueva notificación"
                });
        }
    } else {
        console.warn("Laravel Echo no está configurado.");
    }
});

onUnmounted(() => {
    // Limpiar el listener para evitar memory leaks al destruir el componente
    document.removeEventListener('mousedown', handleClickOutside);
});
</script>