<script>
export default {
    name: "logs-modal-table",
    props: {
        items: {
            type: Array,
            required: true,
        },
    },
    data() {
        return {
            fields: [
                {key: 'user', label: 'Пользователь', class: 'text-left'},
                {key: 'created_at', label: 'Дата, время', class: 'text-left'},
                {key: 'type', label: 'Действие', class: 'text-left'},
                {key: 'data', label: 'Изменения', class: 'text-left'},
            ],
        }
    }
}
</script>

<template>
    <div>
        <b-table
            :fields="fields"
            :items="items"
            head-variant="light"
            striped
            responsive
            bordered
        >
            <template #cell(data)="{ item }">
                <div v-for="(changeItem, index) of item.data" :key="index" class="p-1 mb-1" style="line-height: 19px">
                    <div>
                        <b>{{ changeItem.name }}</b>
                    </div>
                    <span style="height: 18px">{{ changeItem.oldValue }}</span>
                    <span class="text-primary">&xrarr;</span>
                    <span> {{ changeItem.newValue }}</span>
                </div>
            </template>
        </b-table>
    </div>
</template>

<style scoped>

</style>
