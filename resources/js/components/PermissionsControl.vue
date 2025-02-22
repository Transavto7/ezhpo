<script setup>
import {computed, ref} from "vue";

/**
 * @typedef {Object} PermissionItem
 * @property {string} id
 * @property {string} name
 */

/**
 * @type {{
 *   value: int[],
 *   mandatoryPermissions: int[],
 *   items: PermissionItem[]
 * }}
 */
const props = defineProps({
  value: {
    type: Array,
    required: true,
  },
  mandatoryPermissions: {
    type: Array,
    required: true,
  },
  items: {
    type: Array,
    required: true,
  },
})

const emit = defineEmits(['input'])

const search = ref(null)
const permissionTabExpanded = ref(false)

const displayedValue = computed(() => {
  return [...props.value, ...props.mandatoryPermissions]
})

const searchedItems = computed(() => {
  if (!search.value) {
    return props.items
  }

  return props.items.filter((item) => item.name.toLowerCase().includes(search.value.toLowerCase()))
})

const displayedItems = computed(() => {
  return searchedItems
    .value
    .map((item) => {
      const disabled = props.mandatoryPermissions.includes(item.id)

      return {
        id: item.id,
        label: item.name,
        disabled,
      }
    })
})

const handleChange = (value) => {
  const newValue = value.filter((id) => {
    return !props.mandatoryPermissions.includes(id)
  })

  emit('input', newValue)
}
</script>

<template>
  <div>
    <a
      href="#"
      :aria-expanded="permissionTabExpanded"
      aria-controls="collapse-permissions"
      style="font-size: 13px"
      @click="() => permissionTabExpanded = !permissionTabExpanded"
    >
      {{ permissionTabExpanded ? 'Скрыть права' : 'Показать права' }}
    </a>
    <b-collapse id="collapse-permissions" v-model="permissionTabExpanded" class="mt-2">
      <div class="alert alert-success my-3 text-center">
        Не все права можно выставить, так как они предусматриваются наличием роли<br>
        У каждой роли есть набор прав<br>
        У каждого пользователя есть набор прав и ролей
      </div>
      <div class="col-md-4 col-lg-3 mx-0 px-0 mb-3">
        <b-form-input v-model="search" placeholder="Поиск"/>
      </div>
      <b-card>
        <b-form-group label="Доступы:" v-slot="{ ariaDescribedby }">
          <b-form-checkbox-group
            :aria-describedby="ariaDescribedby"
            name="flavour-2"
            :checked="displayedValue"
            @change="handleChange"
          >
            <b-row>
              <div class="box">
                <div v-for="(item, index) in displayedItems">
                  <b-col>
                    <b-form-checkbox
                      :value="item.id"
                      :disabled="item.disabled"
                      :key="index"
                    >
                      {{ item.label }}
                    </b-form-checkbox>
                  </b-col>
                </div>
              </div>
            </b-row>
          </b-form-checkbox-group>
        </b-form-group>
      </b-card>
    </b-collapse>
  </div>
</template>

<style scoped>

</style>