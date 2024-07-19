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
    name: "model-searcher-table",
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
                {key: 'hash_id', label: 'HASH ID', class: 'text-left'},
                {key: 'model_type', label: 'Модель', class: 'text-left'},
                {key: 'name', label: 'Наименование', class: 'text-left'}
            ],
        }
    }
}
</script>

<style scoped>

</style>
