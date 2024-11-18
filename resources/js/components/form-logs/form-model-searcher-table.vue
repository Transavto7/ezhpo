<template>
    <b-table
        :fields="fields"
        :items="items"
        head-variant="light"
        striped
        responsive
        bordered
        class="mt-2"
    >
    </b-table>
</template>

<script>
export default {
    name: "form-model-searcher-table",
    props: {
        items: {
            type: Array,
            required: true,
        },
    },
    data() {
        return {
            fields: [
                {key: 'id', label: 'ID', class: 'text-left'},
                {key: 'uuid', label: 'UUID', class: 'text-left'},
                {key: 'type', label: 'Вид осмотра', class: 'text-left'},
                {key: 'user', label: 'Пользователь', class: 'text-left'}
            ],
        }
    }
}
</script>

<style scoped>

</style>
