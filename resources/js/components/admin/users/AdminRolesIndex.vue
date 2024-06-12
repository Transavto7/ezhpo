<template>
    <div>
        <div class="my-2">
            <b-button
                variant="success"
                size="sm"
                v-if="current_user_permissions.permission_to_create"
                @click="showModal"
            >
                Добавить роль
                <i class="fa fa-plus"></i>
            </b-button>

            <b-button v-if="current_user_permissions.permission_to_trash"
                      variant="warning" size="sm" :href="deleted ? '/roles' : '?deleted=1'"
            >
                <i v-if="!deleted" class="fa fa-trash"></i>
                {{ deleted ? 'Назад' : `Корзина` }}
            </b-button>
        </div>


        <div class="card">
            <div class="card-body pt-0">
                <b-table
                    v-if="current_user_permissions.permission_to_view"
                    :items="items"
                    :fields="columns"
                    :per-page="perPage"
                    :current-page="currentPage"
                    striped hover
                    no-local-sorting
                    responsive
                >

                    <template #head()="data">
                        <span class="user-select-none"
                              data-toggle="tooltip"
                              data-html="true"
                              data-trigger="click hover"
                              data-placement="top"
                              :title="data.field ? data.field.content : ''"
                        >
                            {{ data.label }}
                        </span>
                    </template>

                    <template #cell(guard_name)="row">
                        <a v-if="current_user_permissions.permission_to_edit"
                           href="#" @click="editRoleData(row.item.id)"
                        >
                            {{ row.value }}
                        </a>
                        <span v-else>
                    {{ row.value }}
                </span>
                    </template>
                    <template #cell(delete_btn)="row">
                        <b-button size="sm"
                                  v-if="!deleted"
                                  :disabled="!current_user_permissions.permission_to_delete"
                                  variant="danger"
                                  @click="deleteRole(row.item.id)">
                            <b-icon icon="trash-fill" aria-hidden="true"></b-icon>
                        </b-button>
                    </template>

                    <template #cell(return_trash)="row">
                        <b-button size="sm"
                                  :disabled="!current_user_permissions.permission_to_trash"
                                  variant="warning"
                                  @click="returnTrash(row.item.id)">
                            <i class="fa fa-undo"></i>
                        </b-button>
                    </template>
                </b-table>

                <b-pagination
                    v-model="currentPage"
                    :total-rows="total"
                    :per-page="perPage"
                    aria-controls="my-table"
                ></b-pagination>
            </div>
        </div>

        <b-modal
            size="xl"
            v-model="editModal"
            ref="groups_modal"
            hide-footer
            :title="infoModalRole.id ? 'Редактирование роли' : 'Добавление роли'"
        >
            <b-row class="my-1">
                <b-col lg="1">
                    <label>Роль:</label>
                </b-col>
                <b-col lg="5">
                    <b-form-input v-model="infoModalRole.guard_name"
                                  placeholder="Введите роль"
                    />
                </b-col>
                <b-button
                    :class="permission_collapse ? null : 'collapsed'"
                    :aria-expanded="permission_collapse ? 'true' : 'false'"
                    aria-controls="collapse-4"
                    size="sm"
                    @click="permission_collapse = !permission_collapse"
                >
                    Раскрыть права
                </b-button>
            </b-row>
            <b-row class="my-1 mt-3">
                <b-col>
                    <b-collapse id="collapse-4" v-model="permission_collapse" class="mt-2">
                        <div class="alert alert-success my-3 text-center">
                            Не все права можно выставить, так как они предусматриваются наличием роли<br>
                            У каждой роли есть набор прав<br>
                            У каждого пользователя есть набор прав и ролей
                        </div>
                        <div class="mb-1 col-lg-5">
                            <b-form-input v-model="searchPermissions" placeholder="Поиск прав"/>
                        </div>
                        <b-card>
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
                    <div class="row mt-2 mx-2 d-flex justify-content-end">
                        <b-button variant="success" @click="saveRole">Сохранить</b-button>
                        <b-button class="ml-2" variant="danger" @click="hideModal">Закрыть</b-button>
                    </div>
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
    name: "AdminRolesIndex",
    props: ['roles', 'all_permissions', 'current_user_permissions', 'deleted', 'fields'],
    components: {Swal2, vSelect},
    data() {
        return {
            allPermissions: [],
            editModal: false,
            permission_collapse: false,
            searchPermissions: '',
            infoModalRole: {
                id: null,
                name: null,
                guard_name: null,
                permissions: [],
            },
            optionsRoles: [],
            columns: [
                {key: 'delete_btn', label: '#', class: 'text-right'},
            ],
            items: [],
            perPage: 15,
            sortBy: 'id',
            sortDesc: false,
            currentPage: 1,
            total: 0,
            loading: false,
        }
    },
    methods: {
        saveRole() {
            this.loading = true;

            if (!this.infoModalRole.id) {
                this.infoModalRole.name = 'role_' + Math.floor(Math.random() * 99999) + 1000;
                axios.post('/roles', this.infoModalRole)
                    .then(({data}) => {
                        if (data.status) {
                            this.editModal = false;
                            location.reload()
                            this.resetModal();
                        } else {
                            Swal2.fire('Ошибка', data.message, 'warning')
                        }
                    }).catch(error => {
                        console.error(error)
                        Swal2.fire('Ошибка!', '', 'warning');
                    }).finally(() => {
                        this.loading = false;
                    });
            } else {
                axios.post('/roles/' + this.infoModalRole.id, {
                    params: this.infoModalRole,
                    _method: 'PUT',
                }).then(({data}) => {
                    if (data.status) {
                        location.reload()
                        this.editModal = false;
                        this.resetModal();
                    } else {
                        Swal2.fire('Ошибка', data.message, 'warning');
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
                cancelButtonText: 'Отмена',
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.post('/roles/' + id, {
                        _method: 'DELETE',
                    }).then(({data}) => {
                        if (data.status) {
                            Swal2.fire('Удалено', 'Данные были успешно удалены', 'success');
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
                    Swal2.fire('Восстановлено', 'Данные были успешно восстановлены', 'success');
                    this.items = this.items.filter((item) => {
                        return item.id != id;
                    })
                } else {
                    Swal2.fire('Ошибка', data.message, 'warning');
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
            this.resetModal();
            this.$refs['groups_modal'].toggle('#toggle-btn')
        },
    },
    mounted() {
        this.items = this.roles;
        this.allPermissions = this.all_permissions;

        this.fields.forEach(field => {
            this.columns.unshift({
                'key': field.field,
                'label': field.name,
                'content': field.content
            });
        });

        if (this.deleted) {
            this.columns.push({
                key: 'deleted_user.name',
                label: 'Имя удалившего',
            }, {
                key: 'deleted_at',
                label: 'Время удаления',
            }, {
                key: 'return_trash',
                label: '#',
                class: 'text-center',
            })
        }

        this.total = this.roles.length;
    },
    watch: {
        searchPermissions(val) {
            if (val === '') {
                this.allPermissions = this.all_permissions
                return;
            }
            val = val.toLowerCase();
            this.allPermissions = this.all_permissions.filter((item) => {
                return item.guard_name.toLowerCase().match(val)
            })
        },
        editModal(val) {
            if (!val) {
                this.resetModal()
            }
        },
    },
}
</script>

<style scoped>
.modal-dialog.modal-xl {
    max-width: 90%;
    margin: 1.75rem auto;
}
</style>
