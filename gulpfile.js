const elixir = require('laravel-elixir');

require('laravel-elixir-vue-2');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(mix => {

    // compile SCSS
    var options = {
        includePaths: [
            'node_modules/foundation-sites/scss',
            'node_modules/motion-ui/src'
        ]
    };

    mix.sass('app.scss', null, null, options);

    // bundle up jQuery and Foundation JavaScript to app-base.js
    var jQuery = '../../../node_modules/jquery/dist/jquery.js';
    var foundation = '../../../node_modules/foundation-sites/dist/foundation.js';

    mix.scripts([
      jQuery,
      foundation
    ], 'public/js/app-base.js');

    // compile application global JS
    mix.webpack('app.js');

    mix.version(['css/app.css', 'js/app-base.js', 'js/app.js']);
});
