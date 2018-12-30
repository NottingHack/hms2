const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.webpackConfig({
        devtool: 'source-map'
    })
   .autoload({
        jquery: ['jQuery', '$', 'window.jQuery'],
        'popper.js/dist/umd/popper.js': ['Popper']
    })
   .js('resources/assets/js/app.js', 'public/js')
   .extract(['lodash', 'jquery', 'axios', 'select2', 'bootstrap', 'popper.js'])
   .sass('resources/assets/sass/app.scss', 'public/css')
   .version()
   .browserSync('hmsdev');
