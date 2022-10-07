require('./bootstrap');
require('./front')

import Vue from 'vue';
import Multiselect from 'vue-multiselect'
import { BootstrapVue, BootstrapVueIcons } from 'bootstrap-vue'
import Vue2Editor from "vue2-editor";
import "bootstrap-vue/dist/bootstrap-vue.css"
import Toast, { POSITION } from "vue-toastification";
import "vue-toastification/dist/index.css";
import vSelect from "vue-select";
import 'vue-select/dist/vue-select.css';

Vue.use(BootstrapVue)
Vue.use(BootstrapVueIcons)
Vue.use(Vue2Editor)
Vue.use(vSelect)
Vue.use(Toast, {
    position: POSITION.BOTTOM_RIGHT
});

Vue.component('multiselect', Multiselect)
const files = require.context('./', true, /\.vue$/i);
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

new Vue().$mount('#app')
