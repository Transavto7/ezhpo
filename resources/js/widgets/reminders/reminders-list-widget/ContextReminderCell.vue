<script setup>
import {computed} from "vue";

const LANG = {
    "user": "Пользователь",
    "city": "Город",
    "company": "Компания",
    "point": "ПВ",
    "role": "Роль",
    "subject": "Субъект",
    "subject_type": "Тип субъекта",
}

const props = defineProps({
    context: {
        type: Object,
        required: true,
    }
})

const keys = computed(() => {
    return Object.keys(props.context);
})

const getName = (key) => {
    return LANG[key] || key;
}
</script>

<template>
    <div class="d-flex flex-column text-left">
        <template v-for="key in keys">
            <span class="mr-2" v-if="context[key]">
                {{ getName(key) }}: {{ context[key].name }}
            </span>
        </template>

    </div>
</template>

<style scoped>

</style>
