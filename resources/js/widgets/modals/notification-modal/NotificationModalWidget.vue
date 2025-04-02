<script setup>

import {ref} from "vue";
import {fetchModalData, sendActionComplete, sendActionOk} from "./api";
import { onMounted, onUnmounted } from 'vue';

const globalNotificationModal = ref(null);
const id = ref(null);
const title = ref(null);
const body = ref(null);
const isSendAction = ref(false)

const showModal = () => {
    globalNotificationModal.value.show();
    isSendAction.value = false
}

const hideModal = () => {
    globalNotificationModal.value.hide();
    isSendAction.value = true
}

// Обработчик глобального события
const handleGlobalEvent = (event) => {
    fetchModalData(event.detail).then(response => {
        id.value = response.id
        title.value = response.name ?? null
        body.value = response.content
        showModal()
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

/*
// Отправка события в модальное окно
window.dispatchEvent(new CustomEvent('showModalNotificationWindow', {
    detail: { ... Тут какой-то JSON ... }
}));
*/

const actionOk = () => {
    const isSendActionValue = isSendAction.value
    hideModal()
    if (!isSendActionValue) {
        sendActionOk(id.value)
    }
}

const actionComplete = () => {
    const isSendActionValue = isSendAction.value
    hideModal()
    if (!isSendActionValue) {
        sendActionComplete(id.value)
    }
}

</script>

<template>
    <b-modal ref="globalNotificationModal" @hidden="actionOk" @cancel="actionOk" @ok="actionComplete">
        <template #modal-title v-if="title !== null">
            {{title}}
        </template>

        <template>
            <div class="d-block" v-html="body"></div>
        </template>

        <template #modal-footer="{ok, cancel}">
            <b-button class="mt-3" @click="cancel()" variant="info">Ок</b-button>
            <b-button class="mt-3" @click="ok()" variant="success">Выполнено</b-button>
        </template>
    </b-modal>
</template>
