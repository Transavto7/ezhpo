<template>
    <div class="col-lg-12">
        <div class="d-flex buttons flex-wrap">
            <div class="d-flex justify-content-center flex-wrap">
                <b-button class="mb-3 ml-2"
                          variant="success"
                          v-if="permissions.permission_to_create"
                          @click="$refs.create.open()"
                          size="sm"
                >
                    Добавить
                    <i class="fa fa-plus"></i>
                </b-button>

                <b-button class="mb-3 ml-2"
                          size="sm"
                          variant="warning"
                          @click="changeTrash(true)"
                          v-if="permissions.permission_to_trash && !filters.trash"
                >
                    <b-icon-trash></b-icon-trash> Корзина
                </b-button>

                <b-button  class="mb-3 ml-2"
                          size="sm"
                          variant="warning"
                          @click="changeTrash(false)"
                          v-else-if="permissions.permission_to_trash"
                >
                    назад
                </b-button>
            </div>
        </div>

        <div class="card" style="overflow-x: inherit">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-3">
                        <input type="text" name="company_name"
                               v-model="filters.company_name" placeholder="Введите заголовок"
                               class="form-control">
                    </div>

                    <div class="col-lg-3">
                        <input type="text" name="licence"
                               v-model="filters.licence" placeholder="Введите лицензию"
                               class="form-control">
                    </div>

                    <div class="col-lg-2 form-group">
                        <b-button
                            variant="info"
                            @click="loadData"
                            size="sm"
                        >
                            Поиск
                        </b-button>

                        <b-button
                            variant="danger"
                            @click="filters = {}"
                            size="sm"
                        >
                            Сбросить
                        </b-button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body pt-0">
                <b-table
                    :items="prompts"
                    :fields="column"
                    striped hover
                    no-local-sorting
                    :sort-by.sync="sortBy"
                    :sort-desc.sync="sortDesc"
                    :current-page="currentPage"
                    @sort-changed="loadData"
                    :busy="loading"
                    responsive
                    label-sort-asc=""
                    label-sort-desc=""
                    label-sort-clear=""
                >
                    <template #cell(actions)="{ item }">
                        <div class="d-flex justify-content-end">
                            <b-button size="sm"
                                      variant="info"
                                      class="mr-2"
                                      @click="$refs.edit.open(item)"
                                      v-if="!filters.trash && permissions.permission_to_edit"
                            >
                                <b-icon-pencil></b-icon-pencil>
                            </b-button>
                            <b-button size="sm"
                                      variant="danger"
                                      class="mr-2"
                                      @click="$refs.delete.open(item)"
                                      v-if="!filters.trash && permissions.permission_to_delete"
                            >
                                <b-icon-trash-fill></b-icon-trash-fill>
                            </b-button>

                            <b-button size="sm"
                                      variant="warning"
                                      class="mr-2"
                                      v-if="filters.trash"
                                      @click="$refs.restore.open(item)"
                            >
                                <i class="fa fa-undo"></i>
                            </b-button>
                        </div>
                    </template>
                </b-table>
                <b-row class="w-100 d-flex justify-content-start">
                    <b-col class="my-1 d-flex justify-content-start">
                        <b-pagination
                            :total-rows="total"
                            :per-page="perPage"
                            v-model="currentPage"
                            class="my-0"
                            @change="changePage"
                        />
                    </b-col>
                </b-row>
            </div>
        </div>

        <admin-stamp-create-modal ref="create" v-on:success="loadData" />
        <admin-stamp-delete-modal ref="delete" v-on:success="loadData" />
        <admin-stamp-restore-modal ref="restore" v-on:success="loadData" />
        <admin-stamp-edit-modal ref="edit" v-on:success="loadData" />
    </div>
</template>

<script>
import { addParams, getParams } from "../../const/params";
import AdminStampCreateModal from "./AdminStampCreateModal.vue";
import AdminStampDeleteModal from "./AdminStampDeleteModal.vue";
import AdminStampRestoreModal from "./AdminStampRestoreModal.vue";
import AdminStampEditModal from "./AdminStampEditModal.vue";

export default {
    props: [ 'fields', 'permissions' ],
    components: {
        AdminStampCreateModal, AdminStampDeleteModal, AdminStampRestoreModal,
        AdminStampEditModal
    },
    data() {
        return {
            prompts: [ ],
            perPage: 15,
            sortBy: 'id',
            sortDesc: false,
            loading: false,
            total: 0,
            type: null,
            field: null,
            filters: {
            },
            currentPage: 1,
            column: [
                {
                    key: "actions",
                    sortable: false,
                    label: "#",
                    class: "text-right options-column"
                }
            ],
            columnTrash: [
                {
                    key: "deleted_user.name",
                    sortable: true,
                    label: "Имя удалившего"
                },
                {
                    key: "deleted_at",
                    sortable: true,
                    label: "Время удаления",
                },
            ]
        }
    },
    mounted() {
        this.filters = getParams();
        if (this.filters.type) {
            this.type = this.types.filter(el => el.key === this.filters.type)[0];

            if (this.filters.field) {
                this.field = this.fields[this.filters.type].filter(el => el.key === this.filters.field)[0];
            }
        }

        this.currentPage = Number(this.filters.page) || 1;
        const column = [];

        this.fields.forEach(field => {
            const cm = {
                key: field.field,
                label: field.name,
                sortable: true,
                thAttr: {
                    'data-toggle': 'tooltip',
                    'data-html': true,
                    'data-trigger': 'hover',
                    'data-placement': 'top',
                    title: field.content,
                }
            };

            if (cm.key === 'content')
                cm['class'] = 'text-center options-column';

            column.push(cm);
        });

        this.column = [...column, ...this.column];

        if (this.filters.trash) {
            this.changeTrash(true);
        }

        this.loadData();
    },
    methods: {
        fieldsType() {
            if (!this.type) {
                return [];
            }

            return this.fields[this.type.key];
        },
        changeTrash(trash) {
            this.filters.trash = trash;
            if (trash) {
                this.column.splice(2, 0, ...this.columnTrash);
            } else {
                this.column = this.column.filter((el) => {
                    return !this.columnTrash.includes(el);
                })
            }
            this.loadData();
        },
        updatePrompt(prompt) {
            this.fields[prompt.type] = this.fields[prompt.type].map(p => {
                if (p.key === prompt.field) {
                    p.name = prompt.name;
                }
                return p;
            });

            this.loadData();
        },
        changePage(page) {
            this.currentPage = page;
            this.loadData();
        },
        loadData() {
            this.filters.field = this.field?.key;
            this.filters.type = this.type?.key;

            const data = {
                ...this.filters,
                page: this.currentPage,
                sortBy: this.sortBy,
                sortDesc: this.sortDesc
            };

            addParams(data);
            axios.post('/stamp/filter', data).then(({ data }) => {
                this.currentPage = data.current_page;
                this.total = data.total;
                this.prompts = data.data;
            });
        }
    }
}
</script>

<style scoped>

</style>
