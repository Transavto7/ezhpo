require('./bootstrap');
require('./front')

import Vue from 'vue/dist/vue.js'
import Multiselect from 'vue-multiselect'
import BootstrapVue from 'bootstrap-vue'

Vue.use(BootstrapVue)
Vue.component('multiselect', Multiselect)
const files = require.context('./', true, /\.vue$/i);
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

new Vue().$mount('#app')
