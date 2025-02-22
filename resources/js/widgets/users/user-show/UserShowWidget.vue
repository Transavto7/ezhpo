<script setup>
import {useShowPageSetup} from "@/widgets/users/user-show/useShowPageSetup";
import {useUserItem} from "@/widgets/users/user-show/useUserItem";
import {computed, onMounted, ref} from "vue";
import EntityTypeChip from "@/widgets/users/shared/EntityTypeChip.vue";
import ChangeUserPasswordForm from "@/features/change-user-password/ChangeUserPasswordForm.vue";
import UserRestrictionControl from "@/features/user-restriction-control/UserRestrictionControl.vue";
import UserAccessSettings from "@/features/user-access-settings/UserAccessSettings.vue";
import LogsModal from "@/components/logs/logs-modal.vue";

const {id, permissions} = useShowPageSetup()
const {pendingFetchUserItem, user, isDeleted, fetchUserItem} = useUserItem()
const logsModalElement = ref(null)
const logsModalShow = ref(false)

const nameLabel = computed(() => {
  if (!user.value) {
    return ''
  }

  if (user.value?.entityType === 'terminal' || user.value?.entityType === 'company') {
    return 'Наименование'
  }

  return 'ФИО'
})

const handleChangeUserStatus = async () => {
  await fetchUserItem(id.value)
}

const handleChangeUserAccess = async () => {
  await fetchUserItem(id.value)
}

const handleLogsRead = (id) => {
  logsModalShow.value = true
  logsModalElement.value.loadData(id)
}

onMounted(async () => {
  await fetchUserItem(id.value)
})
</script>

<template>
  <div v-if="user" class="mt-2">
    <div v-if="permissions.canRead" class="card">
      <div class="card-body">
        <entity-type-chip
          :label="user.entityTypeLabel"
          :entity-type="user.entityType"
        />

        <div v-if="user.entity" class="mt-2">
          <div class="user-attr-container">
            <div class="user-attr-title">{{ nameLabel }}</div>
            <div class="user-attr-value">
              <a :href="user.entity.url" target="_blank">
                [{{ user.entity.hashId }}] {{ user.entity.name }}
              </a>
              <div v-if="user.entity.isDeleted" class="ml-1 badge badge-danger">связанная сущность удалена</div>
            </div>
          </div>
        </div>

        <hr>

        <div v-if="user.entityType !== 'terminal'" class="user-attr-container">
          <div class="user-attr-title">Логин</div>
          <div class="user-attr-value">{{ user.login }}</div>
        </div>

        <div v-if="user.entityType !== 'terminal'" class="user-attr-container">
          <div class="user-attr-title">Email</div>
          <div class="user-attr-value">{{ user.email }}</div>
        </div>

        <div v-if="user.entityType === 'terminal' || user.entityType === 'employee'" class="user-attr-container">
          <div class="user-attr-title">API токен</div>
          <div class="user-attr-value">
            <code class="text-body">{{ user.apiToken }}</code>
          </div>
        </div>

        <div v-if="user.entityType === 'terminal' || user.entityType === 'driver'" class="user-attr-container">
          <div class="user-attr-title">Компания</div>
          <div class="user-attr-value">
            <span v-if="user.company">[{{ user.company.hashId }}] {{ user.company.name }}</span>
            <span v-else>-</span>
          </div>
        </div>

        <div v-if="user.entityType === 'employee'" class="user-attr-container">
          <div class="user-attr-title">Роли</div>
          <div class="user-attr-value">
            <span v-if="!user.roles.length">-</span>
            <div v-else class="d-flex flex-wrap" style="gap: 5px;">
              <span
                v-for="(roleName, index) of user.roles"
                :key="index"
                class="badge badge-success"
              >
                {{ roleName }}
              </span>
            </div>
          </div>
        </div>

        <div v-if="isDeleted" class="user-attr-container">
          <div class="user-attr-title text-danger">Дата удаления</div>
          <div class="user-attr-value text-danger">
            {{ user.deletedAt }}
          </div>
        </div>

        <hr v-if="permissions.canBlock">

        <div class="d-flex" style="gap: 30px">
          <div
            v-if="permissions.canBlock"
            class="d-flex align-items-center"
          >
            <user-restriction-control
              :disabled="pendingFetchUserItem || isDeleted"
              :user-blocked="user.blocked"
              :user-id="id"
              @changed="handleChangeUserStatus"
            />
            <div class="ml-2" style="font-size: 14px">{{ (user.blocked ? 'Разблокировать' : 'Заблокировать') }}</div>
          </div>

          <div
            v-if="permissions.canReadLogs"
            class="d-flex align-items-center"
          >
            <b-btn
              class="btn btn-sm btn-secondary"
              style="width: 32px; height: 32px;"
              @click="handleLogsRead(id)"
            >
              <i class="fa fa-book"></i>
            </b-btn>
            <div class="ml-2" style="font-size: 14px">Журнал действий</div>
          </div>
        </div>
      </div>
    </div>

    <div
      v-if="permissions.canAccessChange && !isDeleted && user.entityType === 'employee'"
      class="card mt-3"
      style="overflow: visible"
    >
      <div class="card-body">
        <div class="mb-2"><b>Настройки доступа</b></div>

        <user-access-settings
          :disabled="pendingFetchUserItem"
          :user-id="id"
          @changed="handleChangeUserAccess"
        />
      </div>
    </div>

    <div
      v-if="permissions.canPasswordChange && !isDeleted && user.entityType !== 'terminal'"
      class="card mt-3"
    >
      <div class="card-body">
        <div class="mb-2"><b>Смена пароля</b></div>

        <change-user-password-form
          :disabled="pendingFetchUserItem"
          :user-id="id"
        />
      </div>
    </div>

    <b-modal
      v-model="logsModalShow"
      :title="'Журнал действий'"
      :static="true"
      size="lg"
      hide-footer>
      <logs-modal ref="logsModalElement"/>
    </b-modal>
  </div>
</template>

<style scoped lang="scss">
.user-attr-container {
  display: flex;
  font-size: 14px;
  padding: 5px 0;

  & + & {
    margin-top: 10px;
  }
}

.user-attr-title {
  width: 165px;
  font-weight: 400;
}

.user-attr-value {
  font-weight: 500;
}
</style>