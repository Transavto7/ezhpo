<script setup>

import {computed, onMounted, onUnmounted, ref} from "vue";
import {fetchModalData, fetchUnreadNotifications, sendActionComplete, sendActionOk, showModal} from "./api";
import Modal from "./modal.vue";

const globalNotificationModal = ref(null);
const reminders = ref([]);

// Обработчик глобального события
const handleGlobalEvent = (event) => {
    fetchModalData(event.detail).then(({data}) => {
        reminders.value = data;
    })
};

const fetchUnreadModalsTimer = () => {
    setInterval(() => {
        fetchUnreadNotifications().then(({data}) => {
            console.log(data)
            data.forEach(reminder => {
                if (!reminders.value.find(r => r.id === reminder.id)) {
                    reminders.value.push(reminder);
                }
            });
        })
    }, 10000);
};

// Добавляем слушатель события при монтировании компонента
onMounted(() => {
    window.addEventListener('showModalNotificationWindow', handleGlobalEvent);
    fetchUnreadModalsTimer();
});

// Удаляем слушатель события при размонтировании компонента
onUnmounted(() => {
    window.removeEventListener('showModalNotificationWindow', handleGlobalEvent);
});

const actionOk = () => {
    if (currentReminder.value.id) {
        sendActionOk(currentReminder.value.id)
        showModal(currentReminder.value.id);
        removeReminder()
    }
}

const actionComplete = () => {
    sendActionComplete(currentReminder.value.id)
    removeReminder()
}

const removeReminder = () => {
    reminders.value.splice(0, 1);
}

const hasReminders = computed(() => {
    return reminders.value.length > 0
})

const currentReminder = computed(() => {
    return reminders.value[0] ?? {}
})
</script>

<template>
    <modal
        :show="hasReminders"
        :modal-info="currentReminder"
        @ok="actionOk"
        @complete="actionComplete"
        :modal-list="reminders"
    />
</template>
