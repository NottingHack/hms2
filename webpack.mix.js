const mix = require('laravel-mix');
const path = require('path');
const WebpackShellPluginNext = require('webpack-shell-plugin-next');

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
        devtool: 'source-map',
        plugins: [
            new WebpackShellPluginNext({
                onBuildStart: {
                    scripts: ['php artisan ziggy:generate resources/js/ziggy.js'],
                    blocking: true,
                },
            }),
        ],
        resolve: {
            alias: {
                'sass': path.resolve(__dirname, 'resources/sass'),
                ziggy: path.resolve('vendor/tightenco/ziggy/src/js/route.js'),
            }
        }
    })
   .options({
        extractVueStyles: true,
    })
   .js('resources/js/app.js', 'public/js')
   .extract()
   .sass('resources/sass/app.scss', 'public/css')
   .version()
   .sourceMaps();
