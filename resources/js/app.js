
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('example-component', require('./components/ExampleComponent.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app'
});

// HMS JavaScript

// Include our select2 related JavaScript
require('./select2');

// Inlcude our bootstrap-confirmation2 related Javascript
require('./bootstrap-confirmation2');

// Negative money values should be red
$(".money:contains('-')").css("color", "red");

// views/membership/showDetails.blade.php
$(".js-programmatic-enable").on("click", function () {
  $(".js-data-existing-account-ajax").prop("disabled", false);
});
 
$(".js-programmatic-disable").on("click", function () {
  $(".js-data-existing-account-ajax").prop("disabled", true);
});

// Workaround to reformat submitted date's into ISO if there in UK format
$("#user-edit-form,#membership-edit-details-form,#register-form").submit(function() {
  var date = $("input[type='date']");
  if (date.val().includes('/')) {
    date.val(date.val().split('/').reverse().join('-'));
  }
});
