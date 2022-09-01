<template>
    <div class="">
        <div class="my-2">
            <b-button variant="success" size="sm" v-if="current_user_permissions.permission_to_create" @click="showModal">
                Добавить группу
                <i class="fa fa-plus"></i>
            </b-button>

            <b-button v-if="current_user_permissions.permission_to_trash"
                      variant="warning" size="sm" :href="deleted ? '/roles' : '?deleted=1'">
                {{ deleted ? 'Назад' : `Корзина` }}
                <template v-if="!deleted">
                    <i class="fa fa-trash"></i>
                </template>
            </b-button>
        </div>


        <b-table
            v-if="current_user_permissions.permission_to_view"
            :items="items"
            :fields="fields"
            :per-page="perPage"
            :current-page="currentPage"
            striped hover
            no-local-sorting
            responsive
        >

            <template #cell(guard_name)="row">
                <template
                    v-if="current_user_permissions.permission_to_edit"
                >
                    <a href="#" @click="editRoleData(row.item.id)">{{ row.value }}</a>
                </template>
                <template
                    v-else
                >
                    {{ row.value }}
                </template>
                <!--                {{ row.value.name }}-->
            </template>
            <template #cell(delete_btn)="row">
                <b-button
                    v-if="!deleted"
                    :disabled="!current_user_permissions.permission_to_delete"
                    variant="warning"
                    @click="deleteRole(row.item.id)">
                    <b-icon icon="trash-fill" aria-hidden="true"></b-icon>
                </b-button>
                <!--                {{ row.value.name }}-->
            </template>

            <template #cell(return_trash)="row">
                <b-button
                    :disabled="!current_user_permissions.permission_to_trash"
                    variant="warning"
                    @click="returnTrash(row.item.id)">
                    <i class="fa fa-undo"></i>
                </b-button>
                <!--                {{ row.value.name }}-->
            </template>
        </b-table>

        <b-pagination
            v-model="currentPage"
            :total-rows="total"
            :per-page="perPage"
            aria-controls="my-table"
        ></b-pagination>

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

<!--            <b-row class="my-1">-->
<!--                <b-col sm="2">-->
<!--                    <label>Код группы:</label>-->
<!--                </b-col>-->
<!--                <b-col sm="10">-->
<!--                    <b-form-input v-model="infoModalRole.name"-->
<!--                                  size="sm"-->
<!--                                  placeholder="Введите код группы"-->
<!--                                  :disabled="infoModalRole.id"-->
<!--                    >-->

<!--                    </b-form-input>-->
<!--                </b-col>-->
<!--            </b-row>-->

            <b-row class="my-1">
                <b-col>
                    <b-button
                        :class="permission_collapse ? null : 'collapsed'"
                        :aria-expanded="permission_collapse ? 'true' : 'false'"
                        aria-controls="collapse-4"
                        @click="permission_collapse = !permission_collapse"
                    >
                        Раскрыть права
                    </b-button>
                    <b-collapse id="collapse-4" v-model="permission_collapse" class="mt-2">
                        <b-card>
                            <div class="alert alert-success m-3">
                                <!--                <i class="fa fa-info"></i>-->
                                <!--                <b>Доброго времени суток!</b><br>-->
                                Не все права можно выставить, так как они предусматриваются наличием роли<br>
                                У каждой роли есть набор прав<br>
                                У каждого пользователя есть набор прав и ролей
                            </div>

                            <b-form-group label="Доступы:" v-slot="{ ariaDescribedby }">
                                <b-form-checkbox-group
                                    :aria-describedby="ariaDescribedby"
                                    name="flavour-2"
                                    v-model="infoModalRole.permissions"
                                >
                                    <b-row>
                                        <div class="box">
                                            <div v-for="(permission, index) in allPermissions">
                                                <b-col>
                                                    <b-form-checkbox
                                                        :value="permission.id"
                                                        :key="index"
                                                    >
                                                        {{ permission.guard_name }}
                                                    </b-form-checkbox>
                                                </b-col>
                                            </div>
                                        </div>
                                    </b-row>
                                </b-form-checkbox-group>
                            </b-form-group>

                        </b-card>
                    </b-collapse>

                </b-col>
            </b-row>

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
    props:      ['roles', 'all_permissions', 'current_user_permissions', 'deleted'],
    components: {Swal2, vSelect},

    data() {
        return {
            allPermissions:      [],
            editModal:           false,
            permission_collapse: false,
            infoModalRole:       {
                id:          null,
                name:        null,
                guard_name:  null,
                permissions: [],
            },
            optionsRoles:        [],
            // Поля таблицы
            fields:      [
                {key: 'guard_name', label: 'Название'},
                {key: 'id', label: 'ID'},
                // {key: 'name', label: 'Код'},
                {key: 'delete_btn', label: '#', class: 'text-center'},
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
                let max = 99999;
                let min = 1000;

                this.infoModalRole.name = Math.random() * (max - min) + min;

                axios.post('/roles', this.infoModalRole)
                    .then(({data}) => {
                        if (data.status) {
                            Swal2.fire(
                                'Сохранено',
                                'Данные были успешно записаны',
                                'success',
                            );
                            this.editModal = false;
                            this.resetModal();
                        } else {
                            Swal2.fire(
                                'Ошибка',
                                data.message,
                                'warning',
                            )
                        }

                    })
                    .catch((err) => {
                        Swal2.fire(
                            'Ошибка!',
                            '',
                            'warning',
                        )
                    })
                    .finally(() => {
                        this.loading = false;
                    });
            } else {
                axios.post('/roles/' + this.infoModalRole.id, {
                    params:  this.infoModalRole,
                    _method: 'PUT',
                }).then(({data}) => {
                    if (data.status) {
                        Swal2.fire(
                            'Сохранено',
                            'Данные были успешно записаны',
                            'success',
                        );
                        this.editModal = false;

                        this.resetModal();
                    } else {
                        Swal2.fire(
                            'Ошибка',
                            data.message,
                            'warning',
                        )
                    }

                }).finally(() => {
                    this.loading = false;
                });
            }
        },

        deleteRole(id) {
            Swal2.fire({
                title:              'Вы уверены, что хотите удалить?',
                icon:               'warning',
                showCancelButton:   true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor:  '#d33',
                confirmButtonText:  'Да, удалить!',
                cancelButtonText:   'Отмена',
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.post('/roles/' + id, {
                        _method: 'DELETE',
                    }).then(({data}) => {
                        if (data.status) {
                            Swal2.fire(
                                'Удалено',
                                'Данные были успешно удалены',
                                'success',
                            );
                            this.items = this.items.filter((item) => {
                                return item.id != id;
                            })
                        } else {
                            Swal2.fire(
                                'Ошибка',
                                data.message,
                                'warning',
                            )
                        }

                    }).finally(() => {
                        this.loading = false;
                    });
                }
            })

        },

        returnTrash(id) {
            axios.post('/roles/return_trash', {
                id: id,
            }).then(({data}) => {
                if (data.status) {
                    Swal2.fire(
                        'Восстановлено',
                        'Данные были успешно восстановлены',
                        'success',
                    );
                    this.items = this.items.filter((item) => {
                        return item.id != id;
                    })
                } else {
                    Swal2.fire(
                        'Ошибка',
                        data.message,
                        'warning',
                    )
                }

            }).finally(() => {
                this.loading = false;
            });

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

        if (this.deleted) {
            this.fields.push({
                key:   'deleted_user.name',
                label: 'Имя удалившего',
            }, {
                key:   'deleted_at',
                label: 'Время удаления',
            }, {
                key:   'return_trash',
                label: '#',
                class: 'text-center',
            })
        }

        this.total = this.roles.length;
    },

    watch: {
        editModal(val) {
            if (!val) {
                this.resetModal()
            }
        },
    },
}
</script>

<style scoped>
.box {
    display: flex;
    flex-wrap: wrap;
    flex-direction: column;
    width: 500px;
    height: 400px;
    align-content: space-between;
}
</style>
