<script setup>
import {useAction} from "@/features/change-user-password/useAction";
import {computed, ref} from "vue";

const props = defineProps({
  userId: {
    type: Number,
    required: true,
  },
  disabled: {
    type: Boolean,
    required: false,
    default: false,
  },
})

const { pending, performChangeUserPassword } = useAction()

const password = ref(null)
const confirmPassword = ref(null)

const disabledChangePassword = computed(() => {
  return !password.value || password.value.length < 6 || password.value !== confirmPassword.value
})

const passwordError = computed(() => {
  if (!password.value || !confirmPassword.value) {
    return null
  }

  if (password.value.length < 6) {
    return 'Пароль должен состоять минимум из 6 символов'
  }

  if (password.value !== confirmPassword.value) {
    return 'Пароли не совпадают'
  }

  return null
})

const handleChangePassword = async () => {
  await performChangeUserPassword(props.userId, {
    password: password.value,
    confirmPassword: confirmPassword.value,
  })

  password.value = null
  confirmPassword.value = null
}
</script>

<template>
  <div>
    <div class="row">
      <div class="col-12 col-md-6 col-lg-3">
        <label for="password">Новый пароль</label>
        <input v-model="password" class="form-control" type="text" />
      </div>
    </div>
    <div class="row mt-2">
      <div class="col-12 col-md-6 col-lg-3">
        <label for="password">Повторите пароль</label>
        <input v-model="confirmPassword" class="form-control" type="text" />
        <div v-if="passwordError" class="mt-1 text-danger" style="font-size: 12px">
          {{ passwordError }}
        </div>
      </div>
    </div>

    <b-btn class="btn btn-sm btn-success mt-3" :disabled="pending || disabledChangePassword" @click="handleChangePassword">
      <i class="fa fa-key"></i>
      <span class="ml-1">Сменить пароль</span>
    </b-btn>
  </div>
</template>

<style scoped></style>