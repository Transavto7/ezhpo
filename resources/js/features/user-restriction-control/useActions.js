import {ref} from "vue";
import {unblockUser, blockUser} from "@/features/user-restriction-control/api";
import Swal2 from "sweetalert2";

export const useActions = () => {
  const pending = ref(false)

  const performUnblockUser = async (id) => {
    pending.value = true

    try {
      await unblockUser(id)
      await Swal2.fire('Разблокировка пользователя', 'Пользователь успешно разблокирован', 'success');
    } catch (e) {
      await Swal2.fire('Ошибка', e.message ?? 'Что-то пошло не так...', 'error');
      throw e
    } finally {
      pending.value = false
    }
  }

  const performBlockUser = async (id) => {
    pending.value = true

    try {
      await blockUser(id)
      await Swal2.fire('Блокировка пользователя', 'Пользователь успешно заблокирован', 'success');
    } catch (e) {
      await Swal2.fire('Ошибка', e.message ?? 'Что-то пошло не так...', 'error');
      throw e
    } finally {
      pending.value = false
    }
  }

  return {
    pending,
    performUnblockUser,
    performBlockUser,
  }
}