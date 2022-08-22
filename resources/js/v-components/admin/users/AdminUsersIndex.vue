<template>
    <div class="">
        <b-button @click="showModal">Добавить пользователя</b-button>


        <b-table
            :items="items"
            :fields="fields"
            ref="users_table"
            striped hover
            no-local-sorting
            :busy="loading"
            responsive
        >

            <template #cell(name)="row">
                <b-button variant="success" @click="editUserData(row.item.id)">{{ row.value }}</b-button>
                <!--                {{ row.value.name }}-->
            </template>

            <template #cell(pv)="row">
                <!--                <b-button variant="success" @click="editUserData(row.item.id)">{{ row.value.name }}</b-button>-->
                {{ row.value.name }}
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
                            {{
                                roles.find((item) => {
                                    return item.value == role.name
                                }).text
                            }}
                        </span>
                    </h5>
                </template>
            </template>
        </b-table>

        <b-modal
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
            <b-row class="my-1">
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
                        label="text"
                        v-model="infoModalUser.roles"
                    >
                    </v-select>

                    <!--                    <b-form-select v-model="infoModalUser.roles"-->
                    <!--                                   :options="optionsRoles"-->
                    <!--                                   multiple-->
                    <!--                    >-->

                    <!--                    </b-form-select>-->
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
</template>

<script>
import Swal2 from "sweetalert2";
import vSelect from "vue-select";
import 'vue-select/dist/vue-select.css';

export default {
    name:       "AdminUsersIndex",
    props:      ['users', 'roles', 'points'],
    components: {Swal2, vSelect},

    data() {
        return {
            infoModalUser: {
                id:       null,
                name:     null,
                login:    null,
                email:    null,
                password: null,
                eds:      null,
                timezone: null,
                pv:       null,
                roles:    [],
                blocked:  null,
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
            fields:      [
                {key: 'id', label: 'ID'},
                {key: 'type', label: 'Фото', class: 'text-center'},
                {key: 'name', label: 'ФИО'},
                {key: 'eds', label: 'ЭЦП'},
                {key: 'login', label: 'Login'},
                {key: 'email', label: 'E-mail'},
                {key: 'pv', label: 'ПВ'},
                {key: 'company', label: 'Компания'},
                {key: 'timezone', label: 'GMT'},
                {key: 'blocked', label: 'Заблокирован'},
                {key: 'roles', label: 'Роль'},
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
        saveUser() {
            this.loading = true;

            console.log({
                params: {
                    user_id:  this.infoModalUser.id,
                    name:     this.infoModalUser.name,
                    login:    this.infoModalUser.login,
                    email:    this.infoModalUser.email,
                    eds:      this.infoModalUser.eds,
                    timezone: this.infoModalUser.timezone,
                    pv:       this.infoModalUser.pv,
                    roles:    this.infoModalUser.roles,
                    blocked:  this.infoModalUser.blocked,
                },
            })

            axios.get('/users/saveUser', {
                params: {
                    user_id:  this.infoModalUser.id,
                    name:     this.infoModalUser.name,
                    login:    this.infoModalUser.login,
                    email:    this.infoModalUser.email,
                    eds:      this.infoModalUser.eds,
                    timezone: this.infoModalUser.timezone,
                    pv:       this.infoModalUser.pv,
                    roles:    this.infoModalUser.roles,
                    blocked:  this.infoModalUser.blocked,
                },
            }).then(({data}) => {
                if (data.status) {
                    console.log(data)
                    // let user = this.items.find((element) => {
                    //     if(element.id == data.user_info.id){
                    //         return true;
                    //     }
                    //     return false;
                    // })

                    this.items.forEach((item, i, arr) => {
                        if (item.id == data.user_info.id) {
                            this.items[i] = data.user_info // Новый объект с новыми свойствами
                            // Или так Object.books[i].author = "..."; и так для каждого изменяемого свойства
                        }
                    })
                    this.$refs.users_table.refresh()
                    // this.$refs.users_table::refresh::table
                    console.log(123123)


                    // console.log(user)
                    // user = data.user_info
                    //
                    // console.log(user)

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
                console.log(data)
                data = data[0]
                this.infoModalUser.id = data.id;
                this.infoModalUser.name = data.name
                this.infoModalUser.login = data.login;
                this.infoModalUser.email = data.email;
                // this.infoModalUser.password = data.password;
                this.infoModalUser.eds = data.eds;
                this.infoModalUser.timezone = data.timezone;
                this.infoModalUser.pv = data.pv.id;
                this.infoModalUser.roles = data.roles.map((role) => {
                    return role.name
                });
                this.infoModalUser.blocked = data.blocked;

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
            // this.infoModalUser.password = data.password;
            this.infoModalUser.eds = null;
            this.infoModalUser.timezone = null;
            this.infoModalUser.pv = null;
            this.infoModalUser.roles = [];
            this.infoModalUser.blocked = null;
        },

        showModal() {
            this.$refs['users_modal'].show()
        },
        hideModal() {
            this.resetModal();
            this.$refs['users_modal'].hide()
        },
        toggleModal() {
            // Мы передаем идентификатор кнопки, на которую мы хотим вернуть фокус,
            // когда модальное окно скрыто
            this.resetModal();
            this.$refs['users_modal'].toggle('#toggle-btn')
        },
    },
    mounted() {
        this.items = this.users;
        this.optionsPvs = this.points;
        this.optionsRoles = this.roles;
    },
}
</script>

<style scoped>

</style>
