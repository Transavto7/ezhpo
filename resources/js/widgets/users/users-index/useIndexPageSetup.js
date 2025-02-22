import {reactive, ref} from "vue";

export const useIndexPageSetup = () => {
    const permissions = reactive({
        canRead: window.PAGE_SETUP.permissions.canRead,
        canPasswordChange: window.PAGE_SETUP.permissions.canPasswordChange,
        canBlock: window.PAGE_SETUP.permissions.canBlock,
        canReadLogs: window.PAGE_SETUP.permissions.canReadLogs,
    })

    const statusFilterOptions = ref(window.PAGE_SETUP.statusFilterOptions)
    const entityTypeFilterOptions = ref(window.PAGE_SETUP.entityTypeFilterOptions)

    return {
        permissions,
        statusFilterOptions,
        entityTypeFilterOptions,
    }
}
