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
              align="fill"
              class="my-0"
              :disabled="props.pending"
              :per-page="props.perPage"
              :total-rows="props.total"
              :value="props.page"
              @change="handleUpdatePage"
            />
          </b-col>
        </b-row>

        <div class="mb-3 d-flex align-items-center">
          <label
            class="d-block mr-2 mb-0 pb-0"
            style="text-wrap: nowrap"
            >Количество на странице:</label
          >
          <select
            class="form-control"
            :disabled="props.pending"
            :value="props.perPage"
            @change="handleChangePerPage"
          >
            <option value="50">50</option>
            <option value="100">100</option>
            <option value="250">250</option>
            <option value="500">500</option>
          </select>
        </div>
      </div>

      <b-row
        v-if="props.total"
        class="w-100 d-flex justify-content-center"
      >
        <b-col class="my-1 d-flex justify-content-left">
          <p class="text-center">Количество элементов: {{ props.total }}</p>
        </b-col>
      </b-row>
    </div>
  </div>
</template>

<style scoped></style>
