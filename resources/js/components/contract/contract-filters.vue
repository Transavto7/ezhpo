<template>
<div class="">
    <b-button variant="success" @click="$emit('create_new')">Создать</b-button>
</div>
</template>

<script>
export default {
    name: "contract-filters",
    props: ['change_filters'],

    data(){
        return {

        }
    },
    methods:{
    }
}
</script>

<style scoped>

</style>
