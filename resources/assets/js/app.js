
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('group-assignment', require('./components/GroupAssignment.vue'));
Vue.component('manager', require('./components/areas-asignature/Manger.vue'));


import EventBus from './plugin/event-bus';
import Tabs from 'vue-tabs-component';
import VueGoodTable from 'vue-good-table';

Vue.use(VueGoodTable);
Vue.use(EventBus);
Vue.use(Tabs);

const app = new Vue({
    el: '#app'
});

// App.js
$("#menu-toggle").click(function(e) {
    e.preventDefault();
    $("#wrapper").toggleClass("toggled");
});
$("#menu-toggle-2").click(function(e) {
    e.preventDefault();
    $("#wrapper").toggleClass("toggled-2");
    // $('#menu ul').hide();
});
