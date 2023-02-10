<template>
    <div class="">
        <div class="">
            <div class="my-3">
                <b-button variant="success"
                          v-if="current_user_permissions.permission_to_create"
                          @click="showModal"
                          size="sm"
                >
                    Добавить терминал
                    <i class="fa fa-plus"></i>
                </b-button>

                <b-button
                    v-if="current_user_permissions.permission_to_trash"
                    variant="warning" size="sm"
                    :href="deleted ? '/terminals' : '?deleted=1'"
                >
                    {{ deleted ? 'Назад' : `Корзина` }}
                    <i v-if="!deleted" class="fa fa-trash"></i>
                </b-button>
            </div>

            <slot></slot>

            <div class="card">
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
                        responsive
                        @sort-changed="sortChanged"
                    >
                        <template #cell(name)="row">
                            <template v-if="current_user_permissions.permission_to_edit">
                                <a href="#" @click="editUserData(row.item)">{{ row.value }}</a>
                            </template>
                            <template v-else>
                                {{ row.value }}
                            </template>
                        </template>
                        <template #cell(status)="row">
                            <span v-if="row.value" class="badge badge-success">on</span>
                            <span v-else class="badge badge-danger">off</span>
                        </template>

                        <template #cell(pv)="row">
                             {{ row.value.name }}
                        </template>

                        <template #cell(town)="{ item }">
                            {{ item.pv && item.pv.town ? item.pv.town.name : 'Неизвестно' }}
                        </template>

                        <template #cell(company_id)="{ item }">
                            {{ item.company ? item.company.name : 'Неизвестно' }}
                        </template>

                        <template #cell(blocked)="row">
                            {{ row.value === '1' ? 'Да' : 'Нет' }}
                        </template>

                        <template #cell(delete_btn)="row">
                            <b-button
                                v-if="!deleted"
                                :disabled="!current_user_permissions.permission_to_delete"
                                variant="danger"
                                size="sm"
                                @click="deleteUser(row.item.id)">
                                <b-icon icon="trash-fill" aria-hidden="true"></b-icon>
                            </b-button>
                        </template>
                        <template #cell(return_trash)="row">
                            <b-button
                                :disabled="!current_user_permissions.permission_to_trash"
                                variant="warning"
                                size="sm"
                                @click="returnTrash(row.item.id)">
                                <i class="fa fa-undo"></i>
                            </b-button>
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
                </div>
            </div>

            <b-modal
                size="lg"
                v-model="enableModal"
                ref="users_modal"
                hide-footer
                :title="'Добавление терминала'"
            >

                <b-row class="mb-3">
                    <b-col lg="12">
                        <label>AnyDesk:</label>
                        <b-form-input v-model="infoModalUser.name"
                                      id="input-small"
                                      size="sm"
                                      placeholder="Введите AnyDesk"
                        />
                    </b-col>
                </b-row>
                <b-row class="mb-3">
                    <b-col lg="12">
                        <label class="mb-1" for="company">Компании</label>
                        <multiselect
                            v-model="infoModalUser.company"
                            @search-change="searchCompany"
                            @select="(company) => infoModalUser.company_id = company.id"
                            :options="companies"
                            :searchable="true"
                            :close-on-select="true"
                            :show-labels="false"
                            placeholder="Выберите компанию"
                            label="name"
                            class="is-invalid"
                        >
                            <span slot="noResult">Результатов не найдено</span>
                            <span slot="noOptions">Список пуст</span>
                        </multiselect>
                    </b-col>
                </b-row>

                <b-row class="mb-3">
                    <b-col lg="12">
                        <label>Часовой пояс:</label>
                        <b-form-input v-model="infoModalUser.timezone"
                                      size="sm"
                                      placeholder="Введите часовой пояс"
                        />
                    </b-col>
                </b-row>
                <b-row class="mb-3">
                    <b-col lg="12">
                        <label>Пункт выпуска:</label>
                        <b-form-select v-model="infoModalUser.pv_id"
                               :options="[{ value: null, text: 'Выберите пункт выпуска', disabled: true }, ...optionsPvs]"/>
                    </b-col>
                </b-row>
                <b-row class="mb-3">
                    <b-col lg="12">
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

                <div class="row mt-2 mx-2 d-flex justify-content-end">
                    <b-button variant="danger" @click="hideModal">Закрыть</b-button>
                    <b-button class="ml-2" variant="success" @click="saveUser">Сохранить</b-button>
                </div>
            </b-modal>
        </div>
    </div>
</template>

<script>
import vSelect from "vue-select";
import 'vue-select/dist/vue-select.css';
import Swal2 from "sweetalert2";

export default {
    name: "AdminTerminalsIndex",
    props: ['users', 'deleted', 'roles', 'points', 'all_permissions', 'current_user_permissions', 'options_company', 'fields'],
    components: { Swal2, vSelect },

    data() {
        return {
            allPermissions: [],
            enableModal: false,
            permission_collapse: false,
            searchPermissions: '',
            infoModalUser_roles: [],
            busy: false,
            companies: [],

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
                blocked: 0,
                company: null,
                permissions: [],
            },
            optionsPvs: [],
            optionsRoles: [],
            columns: [],
            items: [],
            loading: false,
        }
    },
    methods: {
        sortChanged(e) {
            this.sortBy = e.sortBy;
            this.sortDesc = e.sortDesc;
            this.loadData();
        },
        loadData() {
            this.busy = true;
            this.loading = true;

            axios.get('/terminals' + window.location.search, {
                params: {
                    sortBy: this.sortBy,
                    sortDesc: this.sortDesc,
                    page: this.currentPage,
                    take: this.perPage,
                    api: 1,
                },
            }).then(({data}) => {
                data.items.forEach(item => {
                    let date = item.last_connection_at;
                    if (!date) {
                        item.status = false;
                    } else {
                        item.status = (Date.now() - Date.parse(date)) <= 30;
                    }

                });
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
                if (result.isConfirmed) {
                    axios.post('/users', {
                        id: id,
                    }).then(({data}) => {
                        if (data.status) {
                            Swal2.fire('Удалено', 'Данные были успешно удалены', 'success');
                            this.items = this.items.filter((item) => {
                                return item.id != id;
                            })
                        } else {
                            Swal2.fire('Ошибка', data.message, 'warning');
                        }

                    }).finally(() => {
                        this.loading = false;
                    });
                }
            })
        },
        returnTrash(id) {
            axios.post('/terminals/return_trash', {
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

        saveUser() {
            this.loading = true;

            axios.post('/terminals', {
                user_id: this.infoModalUser.id,
                name: this.infoModalUser.name,
                timezone: this.infoModalUser.timezone,
                pv: this.infoModalUser.pv_id,
                company_id: this.infoModalUser.company_id,
                blocked:  this.infoModalUser.blocked,
            }).then(({data}) => {
                if (data.status) {
                    this.items.forEach((item, i, arr) => {
                        if (item.id == data.user_info.id) {
                            this.items[i] = data.user_info // Новый объект с новыми свойствами
                        }
                    })
                    Swal2.fire('Сохранено', 'Данные были успешно записаны', 'success');
                    this.$refs.users_table.refresh()
                    this.enableModal = false
                    location.reload()

                }

            }).finally(() => {
                this.loading = false;
            });
        },

        editUserData(user) {
            this.infoModalUser = { ...user };
            this.showModal();
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
            this.infoModalUser.pv_id = null;
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
        searchCompany(query = '') {
            axios.get('/api/companies/find', {
                params: {
                    search: query
                }
            }).then(({ data }) => {
                data.forEach(company => {
                    company.name = `[${company.hash_id}] ${company.name}`;
                });
                this.companies = data;
            });
        },
    },
    mounted() {
        this.loadData()
        this.searchCompany();
        this.optionsPvs = this.points;
        this.optionsRoles = this.roles.filter((item) => {
            return ![3, 9, 6].includes(item.id)
        });
        this.allPermissions = this.all_permissions;

        this.fields.forEach(field =>{
            this.columns.push({
                'key': field.field,
                'label': field.name,
                'sortable': true, // field.field !== 'roles'
                'thAttr': {
                    'data-toggle': 'tooltip',
                    'data-html': true,
                    'data-trigger': 'hover',
                    'data-placement': 'top',
                    title: field.content,
                }
            });
        });
        this.columns.push({ key: 'delete_btn', label: '#', class: 'text-right' });

        if (this.deleted) {
            this.columns.push({
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
        searchPermissions(val){
            if(val === ''){
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
        infoModalUser_roles(val) {
            this.fetchRoleData(this.infoModalUser_roles)
        },
        currentPage(){
            this.loadData();
        },
    },
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
</style>
