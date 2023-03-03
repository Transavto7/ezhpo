<template>
  <b-modal v-model="show" hide-footer>
    <template #modal-title>
      Добавить подсказку
    </template>
    <div class="d-block">
      <div class="form-group mb-3">
        <label class="mb-1">Название элемента меню</label>
        <b-form-input
            v-model="item.title"
            size="sm"
            placeholder="Введите название элемента меню"
        />
      </div>
      <div class="form-group mb-3">
        <label class="mb-1">Родительский элемент меню</label>
        <b-form-select
            v-model="item.parent_id"
            :options="headitems"
            size="sm"
            placeholder="Выберите родительский элемент"
        />
      </div>
      <div class="form-group mb-3">
        <label class="mb-1">Текстовая подсказка для элемента</label>
        <b-form-textarea
            v-model="item.tooltip_prompt"
            size="sm"
            rows="10"
            placeholder="Введите текст для всплывающей подсказки"
        />
      </div>
      <div class="mt-3 d-flex justify-content-end">
        <b-button variant="danger" @click="show = false">Отмена</b-button>
        <b-button class="ml-2" variant="success" :disabled="saving" @click="saveElement">
          <b-spinner v-if="saving" small type="grow"></b-spinner>
          Сохранить
        </b-button>
      </div>
    </div>
  </b-modal>
</template>

<script>
export default {
  props: ['headitems'],
  name: "AdminSidebarItemModal",
  data() {
    return {
      show: false,
      saving: false,
      item: {}
    }
  },
  mounted() {
    this.headitems[0] = {value: null, text: "Без родительского элемента"};
  },
  methods: {
    open(item) {
      this.show = true;
      this.item = Object.assign({}, item);
    },
    async saveElement() {
      // if (!this.validate()) {
      //   return;
      // }

      this.saving = true;
      await axios.put('/sidebar/items/' + this.item.id, {
        title: this.item.title,
        tooltip_prompt: this.item.tooltip_prompt,
        parent_id: this.item.parent_id,
      }).then(({data}) => {
        this.show = false;
        this.$toast('Изменения сохранены');
        this.$emit('success', this.item);
      }).catch((error) => {
        console.log(error);
      });
      this.saving = false;
    }
  }
}
</script>

<style scoped>

</style>