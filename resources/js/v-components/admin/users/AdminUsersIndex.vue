<template>
    <div class="">
        <div class="">
            <div class="my-3">
                <b-button variant="success"
                          v-if="current_user_permissions.permission_to_create"
                          @click="showModal"
                          size="sm"
                >
                    Добавить пользователя
                    <i class="fa fa-plus"></i>
                </b-button>

                <b-button v-if="current_user_permissions.permission_to_trash" variant="warning" size="sm" :href="deleted ? '/roles' : '?deleted=1'">
                    {{ deleted ? 'Назад' : `Корзина` }}
                    <template v-if="!deleted">
                        <i class="fa fa-trash"></i>
                    </template>
                </b-button>
            </div>

            <slot></slot>

            <b-table
                v-if="current_user_permissions.permission_to_view"
                :items="items"
                :fields="fields"
                ref="users_table"
                striped hover
                no-local-sorting
                :busy="loading"
                :sort-by.sync="sortBy"
                :sort-desc.sync="sortDesc"
                :current-page="currentPage"
                responsive
                @sort-changed="sortChanged"
            >

                <template #cell(name)="row">
                    <template
                        v-if="current_user_permissions.permission_to_edit"
                    >
                        <a href="#" @click="editUserData(row.item.id)">{{ row.value }}</a>
                    </template>
                    <template
                        v-else
                    >
                        {{ row.value }}
                    </template>
                </template>

                <template #cell(pv)="row">
                    <!--                <b-button variant="success" @click="editUserData(row.item.id)">{{ row.value.name }}</b-button>-->
                    {{ row.value.name }}
                </template>
                <template #cell(photo)="row">
                    <img v-if="row.value" style="width: 100px; height: 100px" :src="'/storage/' + row.value" alt="">
                    <img v-else style="width: 100px; height: 100px" :src="'/img/default_profile.jpg'" alt="">
                </template>
                <template #cell(company)="row">
                    {{ row.value.name }}
                </template>
                <template #cell(blocked)="row">
                    {{ row.value ? 'Да' : 'Нет' }}
                </template>
                <template #cell(roles)="row">
                    <template v-for="role in row.value">
                        <h5>
                            <span class="badge badge-success">
                                {{ role.guard_name }}
                            </span>
                        </h5>
                    </template>
                </template>

                <template #cell(delete_btn)="row">
                    <b-button
                        v-if="!deleted"
                        :disabled="!current_user_permissions.permission_to_delete"
                        variant="danger"
                        @click="deleteUser(row.item.id)">
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

            <b-row class="w-100 d-flex justify-content-center">
                <b-col class="my-1 d-flex justify-content-left">
                        <b-pagination
                            :disabled="busy"
                            v-model="currentPage"
                            :total-rows="totalRows"
                            :per-page="perPage"
                            align="fill"
                            class="my-0"
                        ></b-pagination>
                </b-col>
            </b-row>
            <b-row class="w-100 d-flex justify-content-center">
                <b-col class="my-1 d-flex justify-content-left">
                    <p class="text-center">
                        Количество элементов: {{ totalRows }}
                    </p>
                </b-col>
            </b-row>

            <b-modal
                v-model="enableModal"
                size="xl"
                ref="users_modal"
                hide-footer
                :title="'Добавление сотрудника'"
            >

                <b-row class="my-1">
                    <b-col sm="2">
                        <label>ФИО:</label>
                    </b-col>
                    <b-col sm="10">
                        <b-form-input v-model="infoModalUser.name"
                                      id="input-small"
                                      size="sm"
                                      placeholder="Введите ФИО"
                        >

                        </b-form-input>
                    </b-col>
                </b-row>
                <b-row class="my-1">
                    <b-col sm="2">
                        <label>Login:</label>
                    </b-col>
                    <b-col sm="10">
                        <b-form-input v-model="infoModalUser.login"
                                      size="sm"
                                      placeholder="Введите логин"
                        >

                        </b-form-input>
                    </b-col>
                </b-row>
                <b-row class="my-1">
                    <b-col sm="2">
                        <label>E-mail:</label>
                    </b-col>
                    <b-col sm="10">
                        <b-form-input v-model="infoModalUser.email"
                                      size="sm"
                                      placeholder="Введите эл. почту"
                        >

                        </b-form-input>
                    </b-col>
                </b-row>
                <b-row class="my-1">
                    <b-col sm="2">
                        <label>Пароль:</label>
                    </b-col>
                    <b-col sm="10">
                        <b-form-input v-model="infoModalUser.password"
                                      size="sm"
                                      type="password"
                                      placeholder="Введите пароль"
                        >

                        </b-form-input>
                    </b-col>
                </b-row>
                <b-row class="my-1">
                    <b-col sm="2">
                        <label>"ЭЦП":</label>
                    </b-col>
                    <b-col sm="10">
                        <b-form-input v-model="infoModalUser.eds"
                                      size="sm"
                                      placeholder="Введите эл. подпись"
                        >

                        </b-form-input>
                    </b-col>
                </b-row>
                <b-row class="my-1">
                    <b-col sm="2">
                        <label>Часовой пояс:</label>
                    </b-col>
                    <b-col sm="10">
                        <b-form-input v-model="infoModalUser.timezone"
                                      size="sm"
                                      placeholder="Введите часовой пояс"
                        >

                        </b-form-input>
                    </b-col>
                </b-row>
                <b-row class="my-1"  v-if="!infoModalUser_roles.filter((item) => {return item.id == 6})[0]">
                    <b-col sm="2">
                        <label>Пункт выпуска:</label>
                    </b-col>
                    <b-col sm="10">
                        <b-form-select v-model="infoModalUser.pv" :options="optionsPvs"></b-form-select>
                    </b-col>
                </b-row>
                <b-row class="my-1">
                    <b-col sm="2">
                        <label>Роль:</label>
                    </b-col>
                    <b-col sm="10">
                        <!--                    <b-form-input v-model="infoModalUser.role"-->
                        <!--                                  size="sm"-->
                        <!--                                  placeholder="Введите роль клиента"-->
                        <!--                    >-->

                        <!--                    </b-form-input>-->

                        <v-select
                            :multiple="true"
                            :options="optionsRoles"
                            label="guard_name"
                            v-model="infoModalUser_roles"
                        >
                        </v-select>
                    </b-col>
                </b-row>
                <b-row class="my-1">
                    <b-col sm="2">
                        <!--                    <label>Заблокирован:</label>-->
                    </b-col>
                    <b-col sm="10">
                        <b-form-checkbox
                            id="checkbox-1"
                            v-model="infoModalUser.blocked"
                            name="checkbox-1"
                            value="1"
                            unchecked-value="0"
                        >
                            Заблокирован
                        </b-form-checkbox>
                    </b-col>
                </b-row>
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
                                        v-model="infoModalUser.permissions"
                                    >
                                        <b-row>
                                            <!--                                        <template v-for="(permission, index) in allPermissions">-->
                                            <!--                                        </template>-->
                                            <div class="box">
                                                <div v-for="(permission, index) in allPermissions">
                                                    <b-col>
                                                        <b-form-checkbox
                                                            :value="permission.id"
                                                            :disabled="permission.disable"
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
                        <b-button class="mt-2" variant="outline-success" block @click="saveUser">Сохранить</b-button>
                    </b-col>
                </b-row>
            </b-modal>
        </div>
<!--        <div class="text-center" v-if="busy">-->
<!--            <b-spinner label="Spinning"></b-spinner>-->
<!--        </div>-->
    </div>
</template>

<script>
import vSelect from "vue-select";
import 'vue-select/dist/vue-select.css';
import Swal2 from "sweetalert2";

export default {
    name:       "AdminUsersIndex",
    props:      ['users', 'deleted', 'roles', 'points', 'all_permissions', 'current_user_permissions', 'options_company'],
    components: {Swal2, vSelect},

    data() {
        return {
            allPermissions:      [],
            enableModal:         false,
            permission_collapse: false,
            infoModalUser_roles: [],
            optionsCompany: [],
            busy: false,

            currentPage: 1,
            totalRows:   0,
            perPage:     15,
            sortBy:      '',
            sortDesc:    false,

            infoModalUser: {
                id:          null,
                name:        null,
                login:       null,
                email:       null,
                password:    null,
                eds:         null,
                timezone:    null,
                pv:          null,
                blocked:     0,
                company:     null,
                permissions: [],
            },
            optionsPvs:    [
                // {
                //     label: 'Сгруппированные опции',
                //     options: [
                //         { value: { C: '3PO' }, text: 'Опция со значением объекта' },
                //         { value: { R: '2D2' }, text: 'Другая опция со значением объекта' }
                //     ]
                // }
            ],
            optionsRoles:  [],
            // Поля таблицы
            fields:  [
                {key: 'hash_id', label: 'ID', sortable: true},
                {key: 'photo', label: 'Фото', class: 'text-center', sortable: true},
                {key: 'name', label: 'ФИО', sortable: true},
                {key: 'eds', label: 'ЭЦП', sortable: true},
                {key: 'login', label: 'Login', sortable: true},
                {key: 'email', label: 'E-mail', sortable: true},
                {key: 'pv', label: 'ПВ', sortable: true},
                {key: 'timezone', label: 'GMT', sortable: true},
                {key: 'blocked', label: 'Заблокирован', sortable: true},
                {key: 'roles', label: 'Роль' },
                {key: 'delete_btn', label: '#', class: 'text-center'},
            ],
            items:   [],
            loading: false,
        }
    },
    methods: {
        fetchCompanies(search, loading) {
            // loading(true);

            let data = {
                params: {
                    query: search,
                },
            }

            axios.get("/users/fetchCompanies", data)

                .then(response => {
                    this.optionsCompany = response.data;
                    // loading(false);
                })

                .catch(error => {
                    //Ошибка
                    Swal.fire({
                        title: "Неизвестная ошибка",
                        icon:  "error",
                    });
                    // loading(false);
                });
        },

        sortChanged(e) {
            this.sortBy = e.sortBy;
            this.sortDesc = e.sortDesc;
            this.loadData();
        },

        loadData() {
            this.busy = true;

            axios.get('/users' + window.location.search, {
                params: {
                    sortBy:   this.sortBy,
                    sortDesc: this.sortDesc,
                    page:     this.currentPage,
                    take:     this.perPage,
                    api:      1,
                },
            }).then(({data}) => {
                console.log(data)
                this.items = data.items;
                this.currentPage = data.current_page;
                this.totalRows = data.total_rows;
                this.busy = false;

            }).finally(() => {
                this.loading = false;
            });
        },

        fetchRoleData(e) {

            let newRoles = e.map((item) => {
                return item.id
            })
            let oldRoles = [];

            if (this.infoModalUser.id) {
                oldRoles = this.items.filter((item) => {
                    return item.id == this.infoModalUser.id
                })[0].roles.map((item) => {
                    return item.id
                })
            }

            if (JSON.stringify(newRoles) != JSON.stringify(oldRoles)) {
                this.allPermissions = this.allPermissions.map((item) => {
                    item.disable = false;
                    item.checked = false;

                    return item;
                })
                this.infoModalUser.permissions = [];

                // Если не выьрана роль
                if (newRoles.length == 0) {
                    return;
                }

                axios.get('/users/fetchRoleData', {
                    params: {
                        role_ids: newRoles,
                    },
                }).then(({data}) => {

                    this.allPermissions.map((item, index) => {
                        if (data.includes(item.id)) {
                            this.allPermissions[index].disable = true;
                            this.infoModalUser.permissions.push(item.id)
                        }
                    })

                    this.showModal()
                }).finally(() => {
                    this.loading = false;
                });
            }
        },

        deleteUser(id) {
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
                    axios.post('/users', {
                        id: id,
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
            axios.post('/users/return_trash', {
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

        saveUser() {
            this.loading = true;

            axios.get('/users/saveUser', {
                params: {
                    user_id:     this.infoModalUser.id,
                    name:        this.infoModalUser.name,
                    login:       this.infoModalUser.login,
                    email:       this.infoModalUser.email,
                    eds:         this.infoModalUser.eds,
                    timezone:    this.infoModalUser.timezone,
                    password:    this.infoModalUser.password,
                    pv:          this.infoModalUser.pv,
                    company:          this.infoModalUser.company?.id,
                    roles:       this.infoModalUser_roles.map((item) => {
                        return item.id;
                    }),
                    blocked:     this.infoModalUser.blocked,
                    permissions: this.infoModalUser.permissions.filter((item) => {
                        return !(this.allPermissions.filter((all_prm) => {
                            return all_prm.id == item
                        })[0].disable)
                    }),
                },
            }).then(({data}) => {
                if (data.status) {
                    this.items.forEach((item, i, arr) => {
                        if (item.id == data.user_info.id) {
                            this.items[i] = data.user_info // Новый объект с новыми свойствами
                            // Или так Object.books[i].author = "..."; и так для каждого изменяемого свойства
                        }
                    })
                    Swal2.fire(
                        'Сохранено',
                        'Данные были успешно записаны',
                        'success',
                    );
                    this.$refs.users_table.refresh()
                    this.enableModal = false
                    location.reload()

                }

            }).finally(() => {
                this.loading = false;
            });
        },

        editUserData(id) {
            this.fetchUserData(id)
        },

        fetchUserData(id) {
            this.loading = true;

            axios.get('/users/fetchUserData', {
                params: {
                    user_id: id,
                },
            }).then(({data}) => {
                // data = data[0]
                this.infoModalUser.id = data.id;
                this.infoModalUser.name = data.name
                this.infoModalUser.login = data.login;
                this.infoModalUser.email = data.email;
                // this.infoModalUser.password = data.password;
                this.infoModalUser.eds = data.eds;
                this.infoModalUser.timezone = data.timezone;
                this.infoModalUser.pv = data.pv.id;
                this.infoModalUser_roles = data.roles;
                this.infoModalUser.blocked = data.blocked;
                this.infoModalUser.company = data.company.name;

                // не редактируемые
                this.allPermissions.map((item, index) => {
                    if (data.disable.includes(item.id)) {
                        this.allPermissions[index].disable = true;
                        this.infoModalUser.permissions.push(item.id)
                    }
                })

                // редактируемые
                data.permission_user.map((item) => {
                    this.infoModalUser.permissions.push(item)
                })

                this.showModal()
            }).finally(() => {
                this.loading = false;
            });
        },

        resetModal() {
            this.infoModalUser.id = null;
            this.infoModalUser.name = null;
            this.infoModalUser.login = null;
            this.infoModalUser.email = null;
            this.infoModalUser.password = null;
            this.infoModalUser.eds = null;
            this.infoModalUser.timezone = null;
            this.infoModalUser.pv = null;
            this.infoModalUser_roles = [];
            this.infoModalUser.blocked = 0;
            this.infoModalUser.company = null;
            this.infoModalUser.permissions = [];
            this.permission_collapse = false;

            this.allPermissions = this.allPermissions.map((item) => {
                item.disable = false;

                return item;
            })
        },

        showModal() {
            this.enableModal = true
        },

        hideModal() {
            this.enableModal = false
        },

        // toggleModal() {
        //     // Мы передаем идентификатор кнопки, на которую мы хотим вернуть фокус,
        //     // когда модальное окно скрыто
        //     // this.resetModal();
        //     // this.enableModal = false
        // },
    },

    mounted() {
        this.fetchCompanies()
        this.loadData()
        // this.items = this.users;
        this.optionsPvs = this.points;
        this.optionsRoles = this.roles.filter((item) => {
            return ![3, 9, 6].includes(item.id)
        });
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
            })
        }
    },
    watch: {
        enableModal(val) {
            if (!val) {
                this.resetModal()
            }
        },
        infoModalUser_roles(val) {
            this.fetchRoleData(this.infoModalUser_roles)
        },
        currentPage(){
            this.loadData();
        }
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
