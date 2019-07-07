/**
 * File to hold all our bootstrap-confirmation2 related JavaScript
 */
 
require('bootstrap-confirmation2');

$('[data-toggle=confirmation]').confirmation({
  rootSelector: '[data-toggle=confirmation]',
  popout: true,
  singleton: true,
}).on('click', function (e) {
    $(this).find('form').submit();
});