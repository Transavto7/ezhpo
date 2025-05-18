import {ref} from "vue";
import Swal2 from "sweetalert2";
import {changeUserPassword} from "@/features/change-user-password/api";

export const useAction = () => {
  const pending = ref(false)

  const performChangeUserPassword = async (id, params) => {
    pending.value = true

    try {
      await changeUserPassword(id, {
        password: params.password,
        confirmPassword: params.confirmPassword,
      })
      await Swal2.fire('Смена пароля', 'Пароль пользователя успешно изменен', 'success');
    } catch (e) {
      await Swal2.fire('Ошибка', e.message ?? 'Что-то пошло не так...', 'error');
      console.error(e)
    } finally {
      pending.value = false
    }
  }

  return {
    pending,
    performChangeUserPassword,
  }
}
