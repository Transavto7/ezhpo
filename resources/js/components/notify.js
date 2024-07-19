import { createToastInterface } from "vue-toastification";

const options = {
    position: "bottom-right",
    timeout: 3000,
    closeOnClick: true,
    pauseOnFocusLoss: false,
    pauseOnHover: false,
};

const toast = createToastInterface(options);

export default class Notify {
    static success(message) {
        toast.success(message);
    }
    static error(message) {
        toast.error(message);
    }
}
