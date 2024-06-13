<template>
    <b-table
        :items="table.items"
        :fields="table.fields"
        @sort-changed="changeSort"
        :busy="busy"
        striped hover
        no-local-sorting
        responsive
        label-sort-asc=""
        label-sort-desc=""
        label-sort-clear=""
    >
        <template #table-busy>
            <div class="text-center text-primary my-2">
                <b-spinner class="align-middle"></b-spinner>
            </div>
        </template>
        <template #cell(services)="row">
            <h2 v-for="service in row.value">
                    <span class="badge badge-success">
                        {{ service.name }}
                    </span>
            </h2>
        </template>
        <template #cell(company)="row">
            <a :href="'/elements/Company?filter=1&id=' + row.value.id">
                {{ row.value.name }}
            </a>
        </template>
        <template #cell(main_for_company)="row">
            {{ Number(row.value) ? 'Да' : 'Нет' }}
        </template>
        <template #cell(finished)="row">
            {{ Number(row.value) ? 'Да' : 'Нет' }}
        </template>
        <template #cell(date_of_start)="row">
            {{ row.value ? new Date(row.value).toLocaleDateString("ru-RU") : '' }}
        </template>
        <template #cell(date_of_end)="row">
            {{ row.value ? new Date(row.value).toLocaleDateString("ru-RU") : '' }}
        </template>
        <template #cell(created_at)="row">
            {{ row.value ? new Date(row.value).toLocaleDateString("ru-RU") : '' }}
        </template>
        <template #cell(buttons)="row">
            <div class="d-flex justify-content-center">
                <template v-if="trash">
                    <b-button size="sm"
                              variant="outline-primary"
                              class="mr-2"
                              style="border: none"
                              @click="restoreItem(row.item.id, $event.target)"
                    >
                        <i class="fa fa-undo"></i>
                    </b-button>
                </template>
                <template v-else>
                    <b-button size="sm"
                              variant="outline-primary"
                              class="mr-2"
                              style="border: none"
                              @click="$emit('logs_read', row.item.id)"
                              v-if="permissions.logs_read"
                              title="Журнал действий"
                    >
                        <b-icon-journal></b-icon-journal>
                    </b-button>
                    <b-button size="sm"
                              variant="outline-primary"
                              class="mr-2"
                              style="border: none"
                              @click="$emit('clone_data', row.item)"
                              v-if="permissions.edit && permissions.create"
                              title="Клонировать"
                    >
                        <b-icon-download></b-icon-download>
                    </b-button>
                    <b-button size="sm"
                              variant="outline-primary"
                              class="mr-2"
                              style="border: none"
                              @click="$emit('update_data', row.item)"
                              v-if="permissions.edit"
                              title="Редактировать"
                    >
                        <b-icon-pencil></b-icon-pencil>
                    </b-button>
                    <b-button size="sm"
                              variant="outline-danger"
                              class="mr-2"
                              style="border: none"
                              @click="deleteItem(row.item.id, $event.target)"
                              v-if="permissions.delete"
                              title="Удалить"
                    >
                        <b-icon-trash-fill></b-icon-trash-fill>
                    </b-button>
                </template>
            </div>
        </template>
    </b-table>
</template>

<script>
import Swal2 from "sweetalert2";

export default {
    name: "contract-table",
    props: ['table', 'change_sort', 'busy', 'trash', 'permissions'],
    methods: {
        changeSort(e) {
            this.$emit('change_sort', e)
        },
        deleteItem(id, element) {
            element.disabled = true

            axios.post(`/contract/` + id, {
                _method: 'DELETE'
            })
                .then(({data}) => {
                    if (data.status) {
                        this.$emit('success');
                    } else {
                        Swal2.fire('Ошибка!', '', 'warning')
                    }
                })
                .catch(error => {
                    console.error(error)
                    Swal2.fire('Ошибка!', '', 'warning');
                })
                .finally(() => {
                    element.disabled = false
                });
        },
        restoreItem(id, element) {
            element.disabled = true

            axios.post(`/contract/restore/` + id, {
                _method: 'PUT'
            })
                .then(({data}) => {
                    if (data.status) {
                        this.$emit('success');
                    } else {
                        Swal2.fire('Ошибка!', '', 'warning')
                    }
                })
                .catch(error => {
                    console.error(error)
                    Swal2.fire('Ошибка!', '', 'warning');
                })
                .finally(() => {
                    element.disabled = false
                });
        },
    }
}
</script>

<style scoped>
.table-responsive {
    max-height: 80vh !important;
    overscroll-behavior: none !important;
}
</style>
