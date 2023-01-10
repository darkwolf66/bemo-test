require('./bootstrap');

import Vue from 'vue'
import VueRouter from 'vue-router'
import App from "./App.vue";

window.Vue = Vue;

const files = require.context('./', true, /\.vue$/i)
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.use(VueRouter)
import VueCookies from 'vue-cookies'
Vue.use(VueCookies)
import vmodal from 'vue-js-modal'
import 'vue-js-modal/dist/styles.css'
Vue.use(vmodal)

import '../scss/styles.scss'

import router from "./router";

new Vue({
    router,
    render: h => h(App)
}).$mount('#app')
