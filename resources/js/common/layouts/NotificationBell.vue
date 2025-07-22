<template>
    <a-dropdown
        v-model:open="isOpen"
        placement="bottomRight"
        :trigger="['click']"
    >
        <a @click.prevent="toggleDropdown" class="cursor-pointer">
            <a-badge :dot="unreadCount > 0">
                <BellOutlined class="text-2xl text-gray-700" />
            </a-badge>
        </a>
        <template #overlay>
            <a-menu>
                <a-spin v-if="isLoading" class="flex justify-center my-4" />
                <div v-else-if="error" class="text-red-500 p-2 text-center">
                    {{ error }}
                </div>
                <a-menu v-else>
                    <a-menu-item
                        v-if="notifications.length === 0"
                        disabled
                        class="text-center"
                    >
                        <strong>{{ $t('common.not_notifications') }}</strong>
                    </a-menu-item>

                    <a-menu-item
                        v-for="note in notifications"
                        :key="note.id"
                        @click="handleClickNotification(note)"
                        class="flex flex-col py-2"
                    >
                        <strong>
                            <span class="font-semibold">
                                {{ note.data.title }}
                            </span>
                        </strong>
                        <br />
                        <span class="text-sm">{{ note.data.message }}</span>
                        <br />

                        <span
                            v-if="note.data.url"
                            class="text-sm"
                        >
                            <a>
                                {{ $t('common.view') }}
                                {{ $t('common.link') }}
                            </a>
                        </span>
                        <br v-if="note.data.url" />

                        <span class="text-xs text-gray-500 mt-1">
                            {{ formatDateTime(note.created_at) }}
                        </span>
                    </a-menu-item>

                    <a-menu-divider v-if="notifications.length != 0" />

                    <a-menu-item
                        v-if="notifications.length != 0"
                        key="mark-all"
                        @click="markAllAsRead"
                        class="text-center"
                    >
                        <strong>{{ $t('common.mark_read') }}</strong>
                    </a-menu-item>
                </a-menu>
            </a-menu>
        </template>
    </a-dropdown>
</template>

<script>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useStore } from 'vuex'
import { BellOutlined } from '@ant-design/icons-vue'
import common from '../../common/composable/common'

export default {
    name: 'NotificationsDropdown',
    components: { BellOutlined },
    setup() {
        const notifications = ref([])
        const isOpen = ref(false)
        const isLoading = ref(false)
        const error = ref(null)
        const dropdownContainer = ref(null)
        const { formatDateTime } = common()
        const store = useStore()
        const unreadCount = computed(() => notifications.value.length)
        const poller = ref(null)

        function toggleDropdown() {
            isOpen.value = !isOpen.value
            if (isOpen.value && notifications.value.length === 0) {
                fetchNotifications()
            }
        }

        async function fetchNotifications() {
            // isLoading.value = true
            error.value = null
            try {
                const res = await axiosAdmin.get('notifications?limit=10')
                notifications.value = res.unread
            } catch (err) {
                console.error(err)
                error.value = 'No se pudieron cargar las notificaciones.'
            } finally {
                // isLoading.value = false
            }
        }

        async function markAllAsRead() {
            try {
                await axiosAdmin.post('notifications/mark-read')
                notifications.value = []
                isOpen.value = false
            } catch (err) {
                console.error(err)
            }
        }

        function handleClickNotification(note) {
            isOpen.value = false
            if (!note.data.url) return

            let url = note.data.url.trim()
            if (!/^https?:\/\//i.test(url)) {
                url = 'https://' + url
            }
            window.open(url, '_blank', 'noopener,noreferrer')
        }



        onMounted(() => {
            fetchNotifications()
            poller.value = setInterval(fetchNotifications, 1000)
            const userId = store.state.user?.id
            if (window.Echo && userId) {
                window.Echo
                    .private(`App.Models.User.${userId}`)
                    // escucha tu evento broadcast con el nombre que definiste
                    .listen('.MainNotification', (payload) => {
                        notifications.value.unshift({
                            id: payload.id,
                            data: payload.data,
                            created_at: new Date().toISOString(),
                        })
                    })
            }
        })

        onUnmounted(() => {
            clearInterval(poller.value)
        })

        return {
            notifications,
            isOpen,
            isLoading,
            error,
            dropdownContainer,
            formatDateTime,
            unreadCount,
            toggleDropdown,
            markAllAsRead,
            handleClickNotification,
        }
    },
}
</script>
