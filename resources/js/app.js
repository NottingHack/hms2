
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

// HMS JavaScript
window.Highcharts = require('highcharts');
require('highcharts/modules/exporting')(Highcharts);

// Inclue all our Vue bits
require('./vue');

// Include our select2 related JavaScript
require('./select2');

// Inlcude our bootstrap-confirmation2 related Javascript
require('./bootstrap-confirmation2');

// Include our summernote WYSIWYG editor related Javascript
require('./summernote.js');

// Negative money values should be red
$(".money:contains('-')").css("color", "red");

// Workaround to reformat submitted date's into ISO if there in UK format
$("#user-edit-form,#membership-edit-details-form,#register-form").submit(function() {
  var date = $("input[type='date']");
  if (date.val().includes('/')) {
    date.val(date.val().split('/').reverse().join('-'));
  }
});

// https://codepen.io/shaikmaqsood/pen/XmydxJ
// https://stackoverflow.com/a/30905277
window.copyToClipboard = function (element) {
  var $temp = $("<input>");
  $("body").append($temp);
  $temp.val($(element).text()).select();
  document.execCommand("copy");
  $temp.remove();
}

// Enable bootstrap tool tips
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})
