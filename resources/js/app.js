
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

// HMS JavaScript

// Inclue all our Vue bits
require('./vue');

// Include our select2 related JavaScript
require('./select2');

// Inlcude our bootstrap-confirmation2 related Javascript
require('./bootstrap-confirmation2');

// Negative money values should be red
$(".money:contains('-')").css("color", "red");

// Workaround to reformat submitted date's into ISO if there in UK format
$("#user-edit-form,#membership-edit-details-form,#register-form").submit(function() {
  var date = $("input[type='date']");
  if (date.val().includes('/')) {
    date.val(date.val().split('/').reverse().join('-'));
  }
});
