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
        </b-table>
      </div>
    </div>
  </div>

</template>

<script>
// import AdminPromptDeleteModal from "./AdminPromptDeleteModal";
// import AdminPromptRestoreModal from "./AdminPromptRestoreModal";
// import AdminPromptEditModal from "./AdminPromptEditModal";

import {addParams, getParams} from "../../const/params";

export default {
  props: ['sidebarItems'],
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
          key: "tooltip_prompt",
          sortable: false,
          label: "Подсказка"
        },
        {
          key: "title",
          sortable: true,
          label: "Название"
        },
        {
          key: "access_permissions",
          sortable: true,
          label: "Права доступа"
        },
        // {
        //   key: "route_name",
        //   sortable: true,
        //   label: "Ссылка"
        // },
        // {
        //   key: "is_header",
        //   sortable: true,
        //   label: "Заголовок",
        //   formatter: (value, key, item) => {
        //       return value === 1? "Да" : "Нет";
        //   }
        // },

        {
          key: "parent",
          sortable: false,
          label: "Родительский элемент",
          formatter: (value, key, item) => {
             return item.parent?.title;
          }
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
    // this.sidebarItems.forEach(item => {
    //   console.log(item);
    // });
    this.loadData()
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