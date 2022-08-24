require('./bootstrap');
require('./front')

import Vue from 'vue';
import Multiselect from 'vue-multiselect'
import { BootstrapVue, BootstrapVueIcons } from 'bootstrap-vue'

Vue.use(BootstrapVue)
Vue.use(BootstrapVueIcons)

Vue.component('multiselect', Multiselect)
const files = require.context('./', true, /\.vue$/i);
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

new Vue().$mount('#app')
