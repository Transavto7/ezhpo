<script setup>
import HourIntervalCard from "./HourIntervalCard.vue";

const props = defineProps({
    hours: {
        type: Array,
        required: true,
    }
})

const emit = defineEmits(["update:hour", "add:hours", "remove:hour"]);

const handleChangeHour = (index, hour) => {
    emit('update:hour', index, hour)
}

const handleRemoveHour = (index) => {
    emit('remove:hour', index)
}

const handleAddInterval = () => {
    emit('add:hours')
}
</script>

<template>
    <div>
        <label>Отличающиеся интервалы:</label>
        <div class="d-flex flex-row flex-wrap align-items-center">
            <hour-interval-card
                :interval="hour"
                :index="index" v-for="(hour, index) in props.hours"
                :key="index"
                @update:hour="handleChangeHour"
                @remove:hour="handleRemoveHour"
            />
            <button @click="handleAddInterval" type="button" class="btn btn-outline-secondary"><span class="fa fa-plus"></span></button>
        </div>
    </div>
</template>

<style scoped>
.d-flex{
    gap: 16px 32px;
}

.btn {
    max-height: 48px;
    width: 48px;
}
</style>
