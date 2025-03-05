import {ref} from "vue";

export const useEditPageSetup = () => {
    const tariff = ref(window.PAGE_SETUP.tariff)

    return {
        tariff,
    }
}
