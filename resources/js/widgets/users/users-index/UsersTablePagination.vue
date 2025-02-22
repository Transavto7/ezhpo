<script setup>
const props = defineProps({
  pending: {
    type: Boolean,
    required: true,
  },
  page: {
    type: Number,
    required: true,
  },
  total: {
    type: Number,
    required: true,
  },
  perPage: {
    type: Number,
    required: true,
  },
})

const emit = defineEmits(['update:per-page', 'update:page'])

const handleChangePerPage = (e) => {
  emit('update:per-page', +e.target.value)
}

const handleUpdatePage = (value) => {
  emit('update:page', +value)
}
</script>

<template>
  <div class="card">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-start w-100">
        <b-row class="w-100 d-flex justify-content-center">
          <b-col class="my-1 d-flex justify-content-left">
            <b-pagination
              :value="props.page"
              :disabled="props.pending"
              :total-rows="props.total"
              :per-page="props.perPage"
              align="fill"
              class="my-0"
              @change="handleUpdatePage"
            />
          </b-col>
        </b-row>

        <div class="mb-3 d-flex align-items-center">
          <label class="d-block mr-2 mb-0 pb-0" style="text-wrap: nowrap">Количество на странице:</label>
          <select class="form-control" :value="props.perPage" :disabled="props.pending" @change="handleChangePerPage">
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
          </select>
        </div>
      </div>

      <b-row v-if="props.total" class="w-100 d-flex justify-content-center">
        <b-col class="my-1 d-flex justify-content-left">
          <p class="text-center">
            Количество элементов: {{ props.total }}
          </p>
        </b-col>
      </b-row>
    </div>
  </div>
</template>

<style scoped>

</style>