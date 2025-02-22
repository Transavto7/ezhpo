import {reactive, ref} from "vue";

export const useShowPageSetup = () => {
    const id = ref(window.PAGE_SETUP.id)
    const permissions = reactive({
        canRead: window.PAGE_SETUP.permissions.canRead,
        canPasswordChange: window.PAGE_SETUP.permissions.canPasswordChange,
        canBlock: window.PAGE_SETUP.permissions.canBlock,
        canAccessChange: window.PAGE_SETUP.permissions.canAccessChange,
        canReadLogs: window.PAGE_SETUP.permissions.canReadLogs,
    })

    return {
        id,
        permissions,
    }
}
