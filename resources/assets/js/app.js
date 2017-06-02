require('./bootstrap');
import Vue from 'vue'
import store from './store'
import VueI18n from 'vue-i18n'
import messages from './locale'
import components from './components'
Vue.use(VueI18n)

const i18n = new VueI18n({
    locale: window.Laravel.locale,
    fallbackLocale: window.Laravel.fallbackLocale,
    messages
})

var App = window.App = new Vue({
    el: '#app',
    store,
    i18n,
    components
})
