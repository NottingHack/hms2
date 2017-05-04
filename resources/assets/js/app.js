require('./bootstrap');
// This file is for global JavaScript which is to be present on every page.
// It is compiled by Webpack, so can freely contain ES2015 code.

// Initialise Foundation plugins
$(() => {
  $(document).foundation();
});
