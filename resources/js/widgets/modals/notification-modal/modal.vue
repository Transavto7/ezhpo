<script setup>
import {watch} from "vue";

const props = defineProps({
    show: {
        type: Boolean,
        required: true,
    },
    title: {
        type: String,
    },
    modalInfo: {
        type: Object,
    },
    modalList: {
        type: Array,
        default: () => []
    }
})

const emit = defineEmits([
    'update:show',
    'ok',
    'complete'
])

const handleOk = () => {
    emit('ok')
}

const handleComplete = () => {
    emit('complete')
}

watch(() => props.show, (isVisible) => {
    if (isVisible) {
        document.body.classList.add('overflow-hidden');
    } else {
        document.body.classList.remove('overflow-hidden');
    }
});

</script>

<template>
    <div class="modal-wrapper" v-if="props.show">
        <div class="modal-background"></div>

        <div class="modal-content">
            <div class="text-center" v-if="props.modalInfo.name">
                <h3>{{ props.modalInfo.name }}</h3>
                <hr>
            </div>

            <div class="modal-body">
                <div v-html="props.modalInfo.content"></div>
            </div>

            <div class="modal-footer d-flex flex-column">
                <div class="w-100" v-if="props.modalInfo.created_at">
                    <div class="miniature-time d-flex justify-content-between">
                        <span>Создано: {{ props.modalInfo.created_at }}</span>
                        <span :class="{is_expired: props.modalInfo.is_expired}">До: {{ props.modalInfo.expires_at }}</span>
                    </div>
                    <hr>
                </div>

                <div class="w-100 d-flex justify-content-end">
                    <b-button @click="handleOk" variant="info">Ок</b-button>
                    <b-button class="ml-3" @click="handleComplete" variant="success">Выполнено</b-button>
                </div>
            </div>
        </div>

        <div class="miniature-list" v-if="props.modalList.length">
            <div class="miniature-list-item" v-for="item in props.modalList" :key="item.id">
                <span class="miniature-title">{{ item.name }}</span>
                <div class="miniature-time">
                    <span>Создано: {{ item.created_at }}</span>
                    <span :class="{is_expired: item.is_expired}">До: {{ item.expires_at }}</span>
                </div>
            </div>
        </div>
    </div>
</template>


<style scoped>
.modal-wrapper {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    overflow: hidden;
    z-index: 1000;

    .modal-background {
        background-color: rgba(0, 0, 0, 0.5);
        width: 100%;
        height: 100%;
        z-index: 1001;
        position: absolute;
        top: 0;
        left: 0;
    }

    .modal-content {
        z-index: 1002;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: auto;
        height: auto;
        background-color: white;
        padding: 20px;
        border-radius: 10px;
        overflow: auto;
        min-height: 400px;
        min-width: 600px;
        max-height: 90vh;
        max-width: 90vw;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);

        @media (max-width: 1400px) {
            left: 25%;
            max-width: 65vw;
            transform: translate(-25%, -50%);
        }

        @media (max-width: 1300px) {
            left: 10%;
            transform: translate(-10%, -50%);
        }
    }

    .miniature-time {
        font-size: 14px; /* Уменьшенный текст для дополнительных данных */
        color: #7a7a7a; /* Второстепенный цвет для данных времени */
        display: flex; /* Расположение меток времени построчно */
        gap: 4px; /* Отступы между строками данных времени */

        .is_expired {
            color: red;
            font-weight: bold;
        }
    }

    .miniature-list {
        position: absolute;
        right: 5px;
        top: 50%;
        transform: translateY(-50%);
        z-index: 1002;
        background-color: white;
        padding: 20px;
        border-radius: 10px;
        overflow-y: auto;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        width: auto;
        height: auto;
        max-height: 90vh;
        max-width: 5vw;
        min-width: 330px;
        min-height: 400px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        align-items: center;

        .miniature-list-item {
            width: 100%;
            display: flex; /* Размещение элементов в строку */
            flex-direction: column; /* Элементы располагаются вертикально */
            align-items: flex-start; /* Элементы выравниваются по левому краю */
            padding: 16px; /* Добавить внутренние отступы для воздушности */
            margin-bottom: 12px; /* Разделение между строками */
            border: 1px solid #e0e0e0; /* Лёгкая рамка для отделения элементов */
            border-radius: 8px; /* Закругление углов для мягкости */
            background-color: #fff; /* Светлый фон для контраста с текстом */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Лёгкая тень для объёмности */
            transition: all 0.3s ease; /* Анимация для плавности взаимодействия */

            &:hover {
                background-color: #f9f9f9; /* Лёгкая подсветка при наведении */
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15); /* Усиление тени при наведении */
                transform: translateY(-2px); /* Эффект подъёма */
            }


            .miniature-title {
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                display: inline-block;
                max-width: 100%;
                font-size: 16px; /* Размер заголовка */
                font-weight: bold; /* Выделить заголовок жирным */
                color: #333; /* Яркий контрастный цвет для читаемости */
                margin-bottom: 8px; /* Отступ между заголовком и остальным содержимым */
            }

            .miniature-time {
                flex-direction: column; /* Список временных меток вертикально */
            }
        }
    }
}
</style>
