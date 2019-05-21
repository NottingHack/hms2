/**
 * File to hold all our select2 related JavaScript
 */

window.select2 = require('select2')

// default all select2 instances to use the bootstrap theme
$.fn.select2.defaults.set( "theme", "bootstrap" );

// views/role/edit.blade.php
$(".js-permission-select").select2({
});

// views/gateKeeper/rfidTags/edit.blade.php
$(".select2-basic-single").select2({
});
