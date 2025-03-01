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

                <b-button
                    v-if="current_user_permissions.permission_to_trash"
                    variant="warning" size="sm"
                    :href="deleted ? '/users' : '?deleted=1'"
                >
                    {{ deleted ? 'Назад' : `Корзина` }}
                    <i v-if="!deleted" class="fa fa-trash"></i>
                </b-button>
            </div>

            <slot></slot>

            <div class="card table-card">
                <div class="card-body pt-0">
                    <b-table
                        v-if="current_user_permissions.permission_to_view"
                        :items="items"
                        :fields="columns"
                        ref="users_table"
                        striped hover
                        no-local-sorting
                        :busy="loading"
                        :sort-by.sync="sortBy"
                        :sort-desc.sync="sortDesc"
                        :current-page="currentPage"
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
                            {{ row.value.name }}
                        </template>
                        <template #cell(photo)="row">
                            <img v-if="row.value" style="width: 100px; height: 100px" :src="'/storage/' + row.value"
                                 alt="">
                            <img v-else style="width: 100px; height: 100px" :src="'/img/default_profile.jpg'" alt="">
                        </template>
                        <template #cell(company)="row">
                            {{ row.value.name }}
                        </template>
                        <template #cell(blocked)="row">
                            {{ row.value == '1' ? 'Да' : 'Нет' }}
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
                        <template #cell(buttons)="row">
                            <div class="d-flex">
                                <b-button
                                    v-if="current_user_permissions.permission_to_logs_read"
                                    size="sm"
                                    variant="primary"
                                    @click="logsRead(row.item.id)"
                                    title="Журнал действий"
                                >
                                    <i class="fa fa-book"></i>
                                </b-button>
                                <b-button
                                    v-if="!deleted"
                                    :disabled="!current_user_permissions.permission_to_delete"
                                    variant="danger"
                                    size="sm"
                                    class="ml-1"
                                    @click="deleteUser(row.item.id)">
                                    <i class="fa fa-trash"></i>
                                </b-button>
                                <b-button
                                    v-if="deleted"
                                    :disabled="!current_user_permissions.permission_to_trash"
                                    variant="warning"
                                    size="sm"
                                    class="ml-1"
                                    @click="returnTrash(row.item.id)">
                                    <i class="fa fa-undo"></i>
                                </b-button>
                            </div>
                        </template>
                    </b-table>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
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
                </div>
            </div>

            <b-modal
                size="xl"
                v-model="enableModal"
                ref="users_modal"
                hide-footer
                :title="'Добавление сотрудника'"
            >
                <b-row class="my-1">
                    <b-col lg="2">
                        <label>ФИО:</label>
                    </b-col>
                    <b-col lg="5">
                        <b-form-input v-model="infoModalUser.name"
                                      id="input-small"
                                      size="sm"
                                      placeholder="Введите ФИО"
                        />
                    </b-col>
                </b-row>
                <b-row class="my-1">
                    <b-col lg="2">
                        <label>Login:</label>
                    </b-col>
                    <b-col lg="5">
                        <b-form-input v-model="infoModalUser.login"
                                      size="sm"
                                      placeholder="Введите логин"
                        />
                    </b-col>
                </b-row>
                <b-row class="my-1">
                    <b-col lg="2">
                        <label>E-mail:</label>
                    </b-col>
                    <b-col lg="5">
                        <b-form-input v-model="infoModalUser.email"
                                      size="sm"
                                      placeholder="Введите эл. почту"
                        />
                    </b-col>
                </b-row>
                <b-row class="my-1">
                    <b-col lg="2">
                        <label>Пароль:</label>
                    </b-col>
                    <b-col lg="5">
                        <b-form-input v-model="infoModalUser.password"
                                      size="sm"
                                      type="password"
                                      placeholder="Введите пароль"
                        />
                    </b-col>
                </b-row>
                <b-row class="my-1">
                    <b-col lg="2">
                        <label>ЭЦП:</label>
                    </b-col>
                    <b-col lg="5">
                        <b-form-input v-model="infoModalUser.eds"
                                      size="sm"
                                      placeholder="Введите эл. подпись"
                        />
                    </b-col>
                </b-row>
                <b-row class="my-1">
                    <b-col lg="2">
                        <label>Срок действия ЭЦП:</label>
                    </b-col>
                    <b-col lg="5" class="d-flex align-items-baseline" style="gap: 5px">
                        с <input v-model="infoModalUser.validity_eds_start" type="date" name="date"
                                 class="form-control">
                        по
                        <input v-model="infoModalUser.validity_eds_end" type="date" name="date" class="form-control">
                    </b-col>
                </b-row>
                <b-row class="my-1">
                    <b-col lg="2">
                        <label>Часовой пояс:</label>
                    </b-col>
                    <b-col lg="5">
                        <b-form-input v-model="infoModalUser.timezone"
                                      size="sm"
                                      placeholder="Введите часовой пояс"
                        />
                    </b-col>
                </b-row>
                <b-row class="my-1" v-if="isNotClient">
                    <b-col lg="2">
                        <label>Пункт выпуска:</label>
                    </b-col>
                    <b-col lg="5">
                        <b-form-select v-model="infoModalUser.pv" :options="optionsPvs"/>
                    </b-col>
                </b-row>
                <b-row class="my-1">
                    <b-col lg="2">
                        <label>Роль:</label>
                    </b-col>
                    <b-col lg="5">
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
                    <b-col lg="10" offset-lg="2">
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
                            size="sm"
                            @click="permission_collapse = !permission_collapse"
                        >
                            Раскрыть права
                        </b-button>
                        <b-collapse id="collapse-4" v-model="permission_collapse" class="mt-2">
                            <div class="alert alert-success my-3 text-center">
                                Не все права можно выставить, так как они предусматриваются наличием роли<br>
                                У каждой роли есть набор прав<br>
                                У каждого пользователя есть набор прав и ролей
                            </div>
                            <div class="col-lg-5 mx-0 px-0 mb-3">
                                <b-form-input v-model="searchPermissions" placeholder="Поиск прав"/>
                            </div>
                            <b-card>
                                <b-form-group label="Доступы:" v-slot="{ ariaDescribedby }">
                                    <b-form-checkbox-group
                                        :aria-describedby="ariaDescribedby"
                                        name="flavour-2"
                                        v-model="infoModalUser.permissions"
                                    >
                                        <b-row>
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
                <b-row class="my-1" v-if="isHeadOperator">
                    <user-pvs-list
                        :all-items="allPvs"
                        :selected-items="infoModalUser.pvs"
                        @input="handlePvsSelect"
                    >
                    </user-pvs-list>
                </b-row>
                <b-row class="my-1">
                    <b-col>
                        <div class="row mt-2 mx-2 d-flex justify-content-end">
                            <b-button variant="danger" @click="hideModal">Закрыть</b-button>
                            <b-button class="ml-2" variant="success" @click="saveUser">Сохранить</b-button>
                        </div>
                    </b-col>
                </b-row>
            </b-modal>
        </div>

        <b-modal
            v-model="logsModalShow"
            :title="'Журнал действий'"
            :static="true"
            size="lg"
            hide-footer>
            <logs-modal ref="logsModal">
            </logs-modal>
        </b-modal>
    </div>
</template>

<script>
import vSelect from "vue-select";
import 'vue-select/dist/vue-select.css';
import Swal2 from "sweetalert2";
import UserPvsList from "./components/UserPvsList";
import LogsModal from "../../logs/logs-modal";

export default {
    name: "AdminUsersIndex",
    props: [
        'users',
        'deleted',
        'roles',
        'points',
        'all_permissions',
        'current_user_permissions',
        'options_company',
        'fields'
    ],
    components: {UserPvsList, Swal2, vSelect, LogsModal},

    data() {
        return {
            allPermissions: [],
            allPvs: [],
            enableModal: false,
            permission_collapse: false,
            searchPermissions: '',
            infoModalUser_roles: [],
            optionsCompany: [],
            busy: false,

            currentPage: 1,
            totalRows: 0,
            perPage: 15,
            sortBy: '',
            sortDesc: false,

            infoModalUser: {
                id: null,
                name: null,
                login: null,
                email: null,
                password: null,
                eds: null,
                timezone: null,
                pv: null,
                pvs: [],
                blocked: 0,
                company: null,
                permissions: [],
            },
            optionsPvs: [],
            optionsRoles: [],
            columns: [],
            items: [],
            loading: false,
            logsModalShow: false,
        }
    },

    methods: {
        logsRead(modelId) {
            this.logsModalShow = true
            this.$refs.logsModal.loadData(modelId)
        },
        handlePvsSelect(data) {
            this.infoModalUser.pvs = data
        },
        fetchCompanies(search) {
            let data = {
                params: {
                    query: search,
                },
            }
            axios.get("/users/fetchCompanies", data)
                .then(response => {
                    this.optionsCompany = response.data;
                })
                .catch(error => {
                    console.error(error)
                    Swal.fire({title: "Неизвестная ошибка", icon: "error"});
                });
        },
        sortChanged(e) {
            this.sortBy = e.sortBy;
            this.sortDesc = e.sortDesc;
            this.loadData();
        },
        loadData() {
            this.busy = true;
            this.loading = true;

            axios.get('/users' + window.location.search, {
                params: {
                    sortBy: this.sortBy,
                    sortDesc: this.sortDesc,
                    page: this.currentPage,
                    take: this.perPage,
                    api: 1,
                },
            }).then(({data}) => {
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
                title: 'Вы уверены, что хотите удалить?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Да, удалить!',
                cancelButtonText: 'Отмена',
            }).then((result) => {
                if (!result.isConfirmed) {
                    return;
                }

                axios.post('/users', {
                    id: id,
                }).then(({data}) => {
                    if (!data.status) {
                        Swal2.fire('Ошибка', data.message, 'warning');
                        return;
                    }

                    Swal2.fire('Удалено', 'Данные были успешно удалены', 'success');
                    this.items = this.items.filter((item) => (item.id != id))
                }).finally(() => {
                    this.loading = false;
                });
            })
        },
        returnTrash(id) {
            axios.post('/users/return_trash', {
                id: id,
            }).then(({data}) => {
                if (!data.status) {
                    Swal2.fire('Ошибка', data.message, 'warning');
                    return;
                }

                Swal2.fire('Восстановлено', 'Данные были успешно восстановлены', 'success');
                this.items = this.items.filter((item) => (item.id != id))
            }).finally(() => {
                this.loading = false;
            });
        },
        saveUser() {
            this.loading = true;

            axios.post('/users/saveUser', {
                user_id: this.infoModalUser.id,
                name: this.infoModalUser.name,
                login: this.infoModalUser.login,
                email: this.infoModalUser.email,
                eds: this.infoModalUser.eds,
                timezone: this.infoModalUser.timezone,
                password: this.infoModalUser.password,
                pv: this.infoModalUser.pv ?? null,
                pvs: this.infoModalUser.pvs,
                company: this.infoModalUser.company?.id ?? null,
                roles: this.infoModalUser_roles.map((item) => {
                    return item.id;
                }),
                blocked: this.infoModalUser.blocked,
                validity_eds_start: this.infoModalUser.validity_eds_start ?? null,
                validity_eds_end: this.infoModalUser.validity_eds_end ?? null,
                permissions: this.infoModalUser.permissions.filter((item) => {
                    return !(this.allPermissions.filter((all_prm) => {
                        return all_prm.id == item
                    })[0]?.disable)
                }),
            }).then(({data}) => {
                if (!data.status) {
                    let message

                    if (typeof data.message === 'string') {
                        message = data.message
                    } else if (typeof data.message === 'object' && data.message !== null) {
                        message = Object
                            .values(data.message)
                            .reduce((carry, fieldErrors) => {
                                if (Array.isArray(fieldErrors)) {
                                    return carry + (carry ? '</br>' : '') + fieldErrors.join('</br>')
                                }
                                return carry;
                            }, '')
                    } else {
                        message = 'При сохранении произошла ошибка'
                    }

                    Swal2.fire('Ошибка', message, 'error');

                    return
                }

                if (this.infoModalUser.id) {
                    this.items = this.items.map((item) => {
                        if (item.id == data.user_info.id) {
                            return data.user_info
                        }

                        return item
                    })
                } else {
                    this.items.push(data.user_info)
                }

                Swal2.fire('Сохранено', 'Данные были успешно записаны', 'success');
                this.enableModal = false
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
                this.infoModalUser.id = data.id;
                this.infoModalUser.name = data.name
                this.infoModalUser.login = data.login;
                this.infoModalUser.email = data.email;
                this.infoModalUser.eds = data.eds;
                this.infoModalUser.timezone = data.timezone;
                this.infoModalUser.pv = data.pv.id;
                this.infoModalUser.pvs = data.pvs;
                this.infoModalUser_roles = data.roles;
                this.infoModalUser.blocked = data.blocked;
                this.infoModalUser.company = data.company.name;
                this.infoModalUser.validity_eds_start = data.validity_eds_start;
                this.infoModalUser.validity_eds_end = data.validity_eds_end;
                this.allPermissions.map((item, index) => {
                    if (data.disable.includes(item.id)) {
                        this.allPermissions[index].disable = true;
                        this.infoModalUser.permissions.push(item.id)
                    }
                })
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
            this.infoModalUser.pvs = [];
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
    },

    mounted() {
        this.fetchCompanies()
        this.loadData()
        this.optionsPvs = this.points;
        this.allPvs = (this.points ?? []).map((pvGroups) => {
            return (pvGroups.options ?? []).map((pv) => {
                return {
                    id: pv.value,
                    name: `${pvGroups.label} - ${pv.text}`
                }
            })
        }).flat()

        this.optionsRoles = this.roles.filter((item) => {
            return ![3, 9, 6].includes(item.id)
        });
        this.allPermissions = this.all_permissions;

        this.fields.forEach(field => {
            this.columns.push({
                'key': field.field,
                'label': field.name,
                'sortable': true,
                'thAttr': {
                    'data-toggle': 'tooltip',
                    'data-html': true,
                    'data-trigger': 'hover',
                    'data-placement': 'top',
                    title: field.content,
                }
            });
        });

        this.columns.push({key: 'buttons', label: '#', class: 'text-right'});

        if (this.deleted) {
            const columns = [
                {
                    key: 'deleted_user.name',
                    label: 'Имя удалившего',
                }, {
                    key: 'deleted_at',
                    label: 'Время удаления',
                }
            ]

            this.columns.push(...columns)
        }
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
        enableModal(val) {
            if (!val) {
                this.resetModal()
            }
        },
        infoModalUser_roles() {
            this.fetchRoleData(this.infoModalUser_roles)
        },
        currentPage() {
            this.loadData();
        }
    },

    computed: {
        isNotClient: function () {
            const clientRole = this.infoModalUser_roles.filter((item) => (item.name === 'client'))[0];

            return clientRole === undefined;
        },
        isHeadOperator: function () {
            const headOperatorRole = this.infoModalUser_roles.filter((item) => (item.name === 'head_operator_sdpo'))[0]

            return headOperatorRole !== undefined
        }
    }
}
</script>

<style>
.box {
    display: flex;
    flex-wrap: wrap;
    flex-direction: column;
    width: 500px;
    max-height: 400px;
    align-content: space-between;
}

.modal-dialog.modal-xl {
    max-width: 90%;
    margin: 1.75rem auto;
}

.table-card {
    max-height: 80vh;
    overflow: hidden;
}

.table-card > .card-body {
    overflow: scroll;
    padding: 0 !important;
    margin: 15px !important;
    overscroll-behavior: contain;
}
</style>
