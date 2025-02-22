<script setup>
import {useActions} from "@/features/user-restriction-control/useActions";

const props = defineProps({
  userId: {
    type: Number,
    required: true,
  },
  userBlocked: {
    type: Boolean,
    required: true,
  },
  disabled: {
    type: Boolean,
    required: false,
    default: false,
  },
})

const emit = defineEmits(['blocked', 'unblocked', 'changed'])

const {pending, performUnblockUser, performBlockUser} = useActions()

const handleUnblock = async () => {
  try {
    await performUnblockUser(props.userId)

    emit('unblocked')
    emit('changed')
  } catch (e) {
    console.log(e)
  }
}

const handleBlock = async () => {
  try {
    await performBlockUser(props.userId)

    emit('blocked')
    emit('changed')
  } catch (e) {
    console.log(e)
  }
}
</script>

<template>
  <div>
    <div v-if="!props.userBlocked">
      <b-btn
        class="btn btn-sm btn-danger user-restriction-action-btn"
        :disabled="pending || props.disabled"
        @click="handleBlock"
      >
        <i class="fa fa-lock"></i>
      </b-btn>
    </div>
    <div v-else>
      <b-btn
        class="btn btn-sm btn-success user-restriction-action-btn"
        :disabled="pending || props.disabled"
        @click="handleUnblock"
      >
        <i class="fa fa-unlock"></i>
      </b-btn>
    </div>
  </div>
</template>

<style scoped>
.user-restriction-action-btn {
  width: 32px;
  height: 32px;
}
</style>