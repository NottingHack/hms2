/**
 * File to hold all our select2 related JavaScript
 */

window.Vue = require('vue');

window.events = new Vue();

window.flash = function (message, level = 'success') {
    window.events.$emit('flash', { message, level });
};

import {Ziggy} from './ziggy';
import route from '../../vendor/tightenco/ziggy/src/js/route';

window.Ziggy = Ziggy; // this was missing from your setup

Vue.mixin({
    methods: {
        route: route
    }
});

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

const files = require.context('./', true, /\.vue$/i);
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const headerApp = new Vue({
    el: '#headerApp',
});

const app = new Vue({
    el: '#app',
});