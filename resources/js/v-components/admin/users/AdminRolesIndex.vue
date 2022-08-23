<template>
    <div class="">
        <b-button @click="showModal">Добавить группу</b-button>


        <b-table
            :items="items"
            :fields="fields"
            striped hover
            no-local-sorting
            responsive
        >

            <template #cell(name)="row">
                <a href="#" class="btn btn-success" @click="editRoleData(row.item.id)">{{ row.item.guard_name }}</a>
                <!--                {{ row.value.name }}-->
            </template>
            <template #cell(delete_btn)="row">
                <b-button variant="danger" @click="deleteRole(row.item.id)">
                    <b-icon icon="trash-fill" aria-hidden="true"></b-icon>
                </b-button>
                <!--                {{ row.value.name }}-->
            </template>
        </b-table>

        <b-modal
            size="xl"
            v-model="editModal"
            ref="groups_modal"
            hide-footer
            :title="infoModalRole.id ? 'Редактирование группы' : 'Добавление группы'"
        >
<!--            <b-row class="my-1">-->
<!--                <b-col sm="2">-->
<!--                    <label>Id</label>-->
<!--                </b-col>-->
<!--                <b-col sm="10">-->
<!--                    <b-form-input v-model="infoModalRole.id"-->
<!--                                  size="sm"-->
<!--                                  placeholder="Введите эл. почту"-->
<!--                    >-->

<!--                    </b-form-input>-->
<!--                </b-col>-->
<!--            </b-row>-->

            <b-row class="my-1">
                <b-col sm="2">
                    <label>Группа:</label>
                </b-col>
                <b-col sm="10">
                    <b-form-input v-model="infoModalRole.guard_name"
                                  size="sm"
                                  placeholder="Введите группу"
                    >

                    </b-form-input>
                </b-col>
            </b-row>
            <b-row class="my-1">
                <b-col sm="2">
                    <label>Код группы:</label>
                </b-col>
                <b-col sm="10">
                    <b-form-input v-model="infoModalRole.name"
                                  size="sm"
                                  placeholder="Введите код группы"
                    >

                    </b-form-input>
                </b-col>
            </b-row>


            <b-form-group label="Доступы:" v-slot="{ ariaDescribedby }">
                <b-form-checkbox-group
                    id="checkbox-group-2"
                    :aria-describedby="ariaDescribedby"
                    name="flavour-2"
                >
                    <b-row>
                        <template v-for="(permission, index) in allPermissions">
                            <b-col lg="3">
                                <b-form-checkbox
                                    :value="permission.id"
                                    :key="index"
                                    v-model="infoModalRole.permissions"
                                >
                                    {{ permission.guard_name }}
                                </b-form-checkbox>
                            </b-col>
                        </template>
                    </b-row>

                </b-form-checkbox-group>
            </b-form-group>

            <b-row>
                <b-col>
                    <b-button class="mt-2" variant="outline-danger" block @click="hideModal">Закрыть</b-button>
                </b-col>
                <b-col>
                    <b-button class="mt-2" variant="outline-success" block @click="saveRole">Сохранить</b-button>
                </b-col>
            </b-row>
        </b-modal>
    </div>
</template>

<script>
import Swal2 from "sweetalert2";
import vSelect from "vue-select";
import 'vue-select/dist/vue-select.css';

export default {
    name:       "AdminRolesIndex",
    props:      ['roles', 'all_permissions'],
    components: {Swal2, vSelect},

    data() {
        return {
            allPermissions: [],
            editModal:      false,
            infoModalRole:  {
                id:          null,
                name:        null,
                guard_name:  null,
                permissions: [],
            },
            optionsRoles:   [],
            // Поля таблицы
            fields:      [
                {key: 'name', label: 'Название'},
                {key: 'id', label: 'ID'},
                {key: 'code', label: 'Код'},
                {key: 'delete_btn', label: '#'},
            ],
            items:       [],
            perPage:     15,
            sortBy:      'id',
            sortDesc:    false,
            currentPage: 1,
            total:       0,
            loading:     false,
        }
    },
    methods: {
        saveRole() {
            this.loading = true;

            if (!this.infoModalRole.id) {
                axios.post('/roles', this.infoModalRole)
                    .then(({data}) => {
                        if (data.status) {
                            Swal2.fire(
                                'Сохранено',
                                'Данные были успешно записаны',
                                'success'
                            );
                            this.editModal = false;
                            this.resetModal();
                        }else {
                            Swal2.fire(
                                'Ошибка',
                                data.message,
                                'warning'
                            )
                        }

                    })
                    .catch((err) => {
                        Swal2.fire(
                            'Ошибка!',
                            '',
                            'warning'
                        )
                    })
                    .finally(() => {
                    this.loading = false;
                });
            } else {
                axios.post('/roles/' + this.infoModalRole.id, {
                    params: this.infoModalRole,
                    _method: 'PUT'
                }).then(({data}) => {
                    if (data.status) {
                        Swal2.fire(
                            'Сохранено',
                            'Данные были успешно записаны',
                            'success'
                        );
                        this.editModal = false;
                        this.resetModal();
                    }else {
                        Swal2.fire(
                            'Ошибка',
                            data.message,
                            'warning'
                        )
                    }

                }).finally(() => {
                    this.loading = false;
                });
            }
        },

        deleteRole(id) {
            Swal2.fire({
                title: 'Вы уверены, что хотите удалить?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Да, удалить!',
                cancelButtonText: 'Отмена'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.post('/roles/' + id, {
                        _method: 'DELETE'
                    }).then(({data}) => {
                        if (data.status) {
                            Swal2.fire(
                                'Удалено',
                                'Данные были успешно удалены',
                                'success'
                            );
                            this.items = this.items.filter((item) => {
                                return item.id != id;
                            })
                        }else {
                            Swal2.fire(
                                'Ошибка',
                                data.message,
                                'warning'
                            )
                        }

                    }).finally(() => {
                        this.loading = false;
                    });
                }
            })

        },

        editRoleData(id) {
            this.fetchRoleData(id)
        },

        fetchRoleData(id) {
            this.loading = true;

            axios.get('/roles/' + id)
                .then(({data}) => {
                    this.infoModalRole.id = id;
                    this.infoModalRole.name = data.name;
                    this.infoModalRole.guard_name = data.guard_name;
                    this.infoModalRole.permissions = data.permissions.map((item) => {
                        return item.id
                    });


                    this.showModal()
                })
                .finally(() => {
                this.loading = false;
            });
        },

        resetModal() {
            this.infoModalRole.id = null;
            this.infoModalRole.name = null;
            this.infoModalRole.guard_name = null;
            this.infoModalRole.permissions = [];
        },

        showModal() {
            this.editModal = true
        },

        hideModal() {
            this.editModal = false
            this.resetModal();
        },

        toggleModal() {
            // Мы передаем идентификатор кнопки, на которую мы хотим вернуть фокус,
            // когда модальное окно скрыто
            this.resetModal();
            this.$refs['groups_modal'].toggle('#toggle-btn')
        },
    },
    mounted() {
        this.items = this.roles;
        this.allPermissions = this.all_permissions;
    },
    watch:{
        editModal(val){
            if(!val){
                this.resetModal()
            }
        }
    },
}
</script>

<style scoped>

</style>
