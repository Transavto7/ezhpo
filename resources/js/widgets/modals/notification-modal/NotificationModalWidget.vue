<script setup>

import {computed, onMounted, onUnmounted, ref} from "vue";
import {fetchModalData, sendActionComplete, sendActionOk, showModal} from "./api";

const globalNotificationModal = ref(null);
const reminders = ref([]);

// Обработчик глобального события
const handleGlobalEvent = (event) => {
    fetchModalData(event.detail).then(({data}) => {
        reminders.value = data;
        if (currentReminder.value.id) {
            showModal(currentReminder.value.id);
        }
    })
};

// Добавляем слушатель события при монтировании компонента
onMounted(() => {
    window.addEventListener('showModalNotificationWindow', handleGlobalEvent);
});

// Удаляем слушатель события при размонтировании компонента
onUnmounted(() => {
    window.removeEventListener('showModalNotificationWindow', handleGlobalEvent);
});

const actionOk = (bvModalEvent) => {
    bvModalEvent.preventDefault()
    if (currentReminder.value.id) {
        sendActionOk(currentReminder.value.id)
        removeReminder()
    }
}

const actionComplete = (bvModalEvent) => {
    bvModalEvent.preventDefault()
    sendActionComplete(currentReminder.value.id)
    removeReminder()
}

const removeReminder = () => {
    reminders.value.splice(0, 1);
    if (currentReminder.value.id) {
        showModal(currentReminder.value.id);
    }
}

const hasReminders = computed(() => {
    return reminders.value.length > 0
})

const currentReminder = computed(() => {
    return reminders.value[0] ?? {}
})
</script>

<template>
    <b-modal ref="globalNotificationModal" v-model="hasReminders" @hidden="actionOk" @cancel="actionOk"
             @ok="actionComplete">
        <template #modal-title v-if="currentReminder.name !== null">
            {{ currentReminder.name }}
        </template>

        <template>
            <div class="d-block" v-html="currentReminder.content">
            </div>
        </template>

        <template #modal-footer="{ok, cancel}">
            <b-button class="mt-3" @click="cancel(currentReminder.id)" variant="info">Ок</b-button>
            <b-button class="mt-3" @click="ok(currentReminder.id)" variant="success">Выполнено</b-button>
        </template>
    </b-modal>
</template>
