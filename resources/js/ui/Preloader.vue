<script setup>
const props = defineProps({
    loading: {
        type: Boolean,
        required: true,
    },
})
</script>

<template>
    <div v-if="loading" class="preloader">
        <div class="lds-ring">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
        </div>
        <span class="text-white">Загрузка</span>
    </div>
</template>

<style scoped>
.preloader {
    position: absolute;
    z-index: 99999;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
}
</style>
