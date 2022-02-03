require('./bootstrap');
require('./front')

import Vue from 'vue/dist/vue.js'

const files = require.context('./', true, /\.vue$/i);
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

new Vue().$mount('#app')
