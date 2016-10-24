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
    var options = {
        includePaths: [
            'node_modules/foundation-sites/scss',
            'node_modules/motion-ui/src'
        ]
    };

    mix.sass('app.scss', null, null, options);

    mix.version('css/app.css');

    var jQuery = '../../../node_modules/jquery/dist/jquery.js';
    var foundationJsFolder = '../../../node_modules/foundation-sites/js/';

    mix.scripts([
        jQuery,
        foundationJsFolder + 'foundation.core.js'
    ]);

    mix.webpack('app.js');
});
