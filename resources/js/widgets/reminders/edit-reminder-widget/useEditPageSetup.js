import {ref} from "vue";

export const useEditPageSetup = () => {
    const reminder = ref(window.PAGE_SETUP.reminder)

    return {
        reminder,
    }
}
