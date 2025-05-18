<script setup>
const LANG = {
    "user": "Пользователь",
    "city": "Город",
    "company": "Компания",
    "point": "ПВ",
    "role": "Роль",
    "subject": "Субъект",
    "subject_type": "Тип субъекта",
    "title": "Название",
    "content": "Контент",
    "status": "Статус",
    "type": "Тип",
    "action": "Действие",
    "is_activated": "Статус активации"
}

const props = defineProps({
    payload: {
        type: Object,
        required: true,
    },
    mapList: {
        type: Object,
        required: false,
    }
})

const getName = (key) => {
    return LANG[key] || key;
}

const getValue = (key, rawValue) => {
    if (rawValue === null) return 'null';
    if (typeof rawValue === 'boolean') return rawValue ? 'Да' : 'Нет';
    if (props.mapList && props.mapList[key] && typeof props.mapList[key][rawValue] !== 'undefined') return props.mapList[key][rawValue];

    return rawValue;
}
</script>

<template>
    <div class="d-flex flex-column text-left">
        <template v-for="(value, key) in payload">
            <span class="mr-2">
                <span class="name">{{ getName(key) }}</span>: <span :class="['oldValue', {null: value.old === null}]">{{ getValue(key, value.old) }}</span> → <span :class="['newValue', {null: value.new === null}]">{{ getValue(key, value.new) }}</span>
            </span>
        </template>

    </div>
</template>

<style scoped>
.name {
    font-weight: bold;
}

.null {
    font-style: italic;
}

.oldValue {
    color: #007bff;
}

.newValue {
    color: #dc3545;
}
</style>
