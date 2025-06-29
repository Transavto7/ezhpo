// @ts-nocheck
import { BootstrapVue, BootstrapVueIcons } from 'bootstrap-vue'
import 'bootstrap-vue/dist/bootstrap-vue.css'
import moment from 'moment'
import Vue2Editor from 'vue2-editor'
import Multiselect from 'vue-multiselect'
import vSelect from 'vue-select'
import 'vue-select/dist/vue-select.css'
import Toast, { POSITION } from 'vue-toastification'
import 'vue-toastification/dist/index.css'
import Vue from 'vue'
import { registerDispatchGlobalEventHelper } from '@/services/global-events/dispatchGlobalEvent'

require('./bootstrap')
require('./front')

Vue.use(BootstrapVue)
Vue.use(BootstrapVueIcons)
Vue.use(Vue2Editor)
Vue.use(vSelect)
Vue.use(Toast, {
  position: POSITION.BOTTOM_RIGHT,
})
moment.locale('ru')
Vue.component('Multiselect', Multiselect)
const files = require.context('./', true, /\.vue$/i)
files.keys().map((key) => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

registerDispatchGlobalEventHelper()

new Vue().$mount('#app')
