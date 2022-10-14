<template>
<div class="">
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
            <h5 v-for="service in row.value">
                <span class="badge badge-success">
                    {{ service.name }}
                </span>
            </h5>
        </template>

        <template #cell(main_for_company)="row">
            {{ row.value ? 'Да' : 'Нет' }}
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
                          @click="$emit('update_data', row.item)"
                >
                    <b-icon-pencil></b-icon-pencil>
                </b-button>

                <b-button size="sm"
                          variant="outline-danger"
                          class="mr-2"
                          style="border: none"
                          @click="deleteItem(row.item.id, $event.target)"
                >
                    <b-icon-trash-fill></b-icon-trash-fill>
                </b-button>
            </template>
            </div>
        </template>
    </b-table>
</div>
</template>

<script>
import Swal2 from "sweetalert2";

export default {
    name: "contract-table",
    props: ['table', 'change_sort', 'busy', 'trash'],
    mounted() {
    },
    methods:{
        changeSort(e){
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
                .catch((err) => {
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
                .catch((err) => {
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

</style>
