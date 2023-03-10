<template>
  <div class="col-lg-12">
    <div class="card">
      <div class="card-body pt-0">
        <b-table
            :items="items"
            :fields="fields"
            striped hover
            responsive
        >
          <template #cell(actions)="{ item }">
            <div class="d-flex justify-content-end">
              <b-button size="sm"
                        variant="info"
                        class="mr-2"
                        @click="$refs.itemModal.open(item)"
              >
                <b-icon-pencil></b-icon-pencil>
              </b-button>
            </div>
          </template>
          <template #table-busy>
            <div class="text-center text-primary my-2">
              <b-spinner class="align-middle"></b-spinner>
            </div>
          </template>

        </b-table>
      </div>
    </div>

    <AdminSidebarItemModal
        ref="itemModal"
        :headitems="headitems"
        v-on:success="loadData"
    />
  </div>
</template>

<script>
import AdminSidebarItemModal from "./AdminSidebarItemModal";

import {addParams, getParams} from "../../const/params";

export default {
  props: ['sidebaritems', 'headitems'],
  components: {
    AdminSidebarItemModal,
  },
  data() {
    return {
      items: [],
      perPage: 100,
      sortBy: 'id',
      sortDesc: false,
      loading: false,
      filters: {},
      total: 0,
      fields: [
        {
          key: "title",
          sortable: true,
          label: "Название"
        },
        {
          key: "parent",
          sortable: false,
          label: "Родительский элемент",
          formatter: (value, key, item) => {
            return item.parent?.title;
          }
        },
        // {
        //   key: "access_permissions",
        //   sortable: true,
        //   label: "Права доступа"
        // },
        {
          key: "tooltip_prompt",
          sortable: false,
          label: "Подсказка"
        },
        {
          key: "actions",
          sortable: false,
          label: "Действия",
          class: "text-right options-column"
        },
      ],
      type: null,
      field: null,
      currentPage: 1,
    }
  },
  mounted() {
    this.filters = getParams();
    this.items = this.sidebaritems
  },
  methods: {
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
      axios.post('/sidebar/items/filter', data).then(({data}) => {
        this.items = data.data;
      });
    }
  }
}
</script>

<style scoped>

</style>