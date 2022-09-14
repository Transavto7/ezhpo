<template>
    <div class="col-lg-12">
        <div class="d-flex buttons flex-wrap">
            <div class="d-flex justify-content-center flex-wrap">
                <b-button class="mb-3"
                          variant="success"
                          size="sm"
                          @click="$refs.addModal.open()"
                          v-if="permissions.permission_to_create"
                >
                    <b-icon-plus></b-icon-plus> Добавить
                </b-button>
                <div v-if="permissions.permission_to_trash">
                    <b-button class="mb-3 ml-2"
                              size="sm"
                              variant="warning"
                              @click="changeTrash(true)"
                              v-if="!filters.trash"
                    >
                        <b-icon-trash></b-icon-trash> Корзина
                    </b-button>
                    <b-button class="mb-3 ml-2"
                              size="sm"
                              variant="warning"
                              @click="changeTrash(false)"
                              v-else
                    >
                        назад
                    </b-button>
                </div>
            </div>
        </div>

        <div class="card" style="overflow-x: inherit">
            <div class="card-body">
                <div class="row">
                    <div class="d-flex align-items-center col-lg-3">
                        <multiselect
                            v-model="type"
                            :options="types"
                            :searchable="true"
                            :close-on-select="true"
                            :show-labels="false"
                            @input="field = null"
                            placeholder="Выберите журнал"
                            label="name"
                            :taggable="true"
                        >
                            <span slot="noResult">Результатов не найдено</span>
                            <span slot="noOptions">Результатов не найдено</span>
                        </multiselect>
                    </div>
                    <div class="d-flex align-items-center col-lg-3">
                        <multiselect
                            v-model="field"
                            :options="fieldsType()"
                            :disabled="!filters.type"
                            :searchable="true"
                            :close-on-select="true"
                            :show-labels="false"
                            placeholder="Выберите поле"
                            label="name"
                            :taggable="true"
                        >
                            <span slot="noResult">Результатов не найдено</span>
                            <span slot="noOptions">Результатов не найдено</span>
                        </multiselect>
                    </div>
                    <div class="d-flex align-items-center">
                        <b-button
                                  variant="info"
                                  @click="loadData"
                        >
                            Поиск
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
                    <template #table-busy>
                        <div class="text-center text-primary my-2">
                            <b-spinner class="align-middle"></b-spinner>
                        </div>
                    </template>


                    <template #cell(type)="{ item }">
                        {{ types.filter((el) => el.key === item.type)[0].name }}
                    </template>

                    <template #cell(field)="{ item }">
                        {{ fields[item.type].filter((el) => el.key === item.field)[0].name }}
                    </template>

                    <template #cell(content)="{ item }">
                        <div class="text-center" v-html="item.content"></div>
                    </template>

                    <template #cell(actions)="row">
                        <div class="d-flex justify-content-end">
                            <b-button size="sm"
                                      variant="info"
                                      class="mr-2"
                                      @click="$refs.editModal.open(row.item)"
                                      v-if="!filters.trash && permissions.permission_to_edit"
                            >
                                <b-icon-pencil></b-icon-pencil>
                            </b-button>
                            <b-button size="sm"
                                      variant="danger"
                                      class="mr-2"
                                      @click="$refs.deleteModal.open(row.item)"
                                      v-if="!filters.trash && permissions.permission_to_delete"
                            >
                                <b-icon-trash-fill></b-icon-trash-fill>
                            </b-button>

                            <b-button size="sm"
                                      variant="warning"
                                      class="mr-2"
                                      v-if="filters.trash"
                                      @click="$refs.restoreModal.open(row.item)"
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
                            @change="loadData"
                        />
                    </b-col>
                </b-row>
            </div>
        </div>

        <AdminPromptAddModal
            :types="types"
            :fields="fields"
            ref="addModal"
            v-on:success="loadData"
        />

        <AdminPromptDeleteModal
            ref="deleteModal"
            v-on:success="loadData"
        />

        <AdminPromptRestoreModal
            ref="restoreModal"
            v-on:success="loadData"
        />

        <AdminPromptEditModal
            :types="types"
            :fields="fields"
            ref="editModal"
            v-on:success="loadData"
        />
    </div>
</template>

<script>
import AdminPromptAddModal from "./AdminPromptAddModal";
import AdminPromptDeleteModal from "./AdminPromptDeleteModal";
import AdminPromptRestoreModal from "./AdminPromptRestoreModal";
import AdminPromptEditModal from "./AdminPromptEditModal";
import {addParams, getParams} from "../../const/params";

export default {
    props: ['types', 'fields', 'permissions'],
    components: {
        AdminPromptAddModal,
        AdminPromptDeleteModal,
        AdminPromptRestoreModal,
        AdminPromptEditModal
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
                    key: "type",
                    sortable: true,
                    label: "Журнал"
                },
                {
                    key: "field",
                    sortable: true,
                    label: "Поле"
                },
                {
                    key: "content",
                    sortable: true,
                    label: "Подсказка",
                    class: "text-center options-column"
                },
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
      if (this.filters.trash) {
          this.changeTrash(true);
      }
      if (this.filters.type) {
          this.type = this.types.filter(el => el.key === this.filters.type)[0];

          if (this.filters.field) {
              this.field = this.fields[this.filters.type].filter(el => el.key === this.filters.field)[0];
          }
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
        loadData(page = this.currentPage) {
            this.filters.field = this.field?.key;
            this.filters.type = this.type?.key;

            const data = {
                page,
                ...this.filters,
            };

            addParams(data);
            axios.post('/field/prompt/filter', data).then(({ data }) => {
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
