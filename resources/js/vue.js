/**
 * File to hold all our VueJS related JavaScript
 */

window.Vue = require('vue');

/**
 * unique id helper, gives this.uid, this.$id(), this.$idRef()
 */
import UniqueId from 'vue-unique-id';

Vue.use(UniqueId);

/**
 * flash helper
 * @param  {string} message
 * @param  {String} level
 */
window.events = new Vue();

window.flash = function (message, level = 'success') {
    window.events.$emit('flash', message, level);
};

/**
 * Ziggy
 */
import route from 'ziggy';
import { Ziggy } from './ziggy';

if (process.env.NODE_ENV != 'production') {
    // use dynamic baseUrl's for ziggy in dev, great for ngrok
    Ziggy.baseUrl = location.protocol+'//'+location.hostname;
    Ziggy.baseProtocol = location.protocol;
    Ziggy.baseDomain = location.hostname;
}

Vue.mixin({
    methods: {
        route: (name, params, absolute) => route(name, params, absolute, Ziggy),
    },
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