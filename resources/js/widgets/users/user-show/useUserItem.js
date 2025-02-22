import {computed, ref} from "vue";
import {fetchUser} from "@/widgets/users/user-show/api";

export const useUserItem = () => {
  const pendingFetchUserItem = ref(false)

  const user = ref(null)

  const isDeleted = computed(() => {
    return user.value && !!user.value.deletedAt
  })

  const fetchUserItem = async (id) => {
    pendingFetchUserItem.value = true

    try {
      const {data} = await fetchUser(id)

      user.value = data
    } catch (e) {
      console.log(e)
    } finally {
      pendingFetchUserItem.value = false
    }
  }

  return {
    pendingFetchUserItem,
    user,
    isDeleted,
    fetchUserItem,
  }
}