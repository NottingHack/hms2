const mix = require('laravel-mix');
const path = require('path');
const WebpackShellPluginNext = require('webpack-shell-plugin-next');
const MomentLocalesPlugin = require('moment-locales-webpack-plugin');
require('laravel-mix-bundle-analyzer');

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

mix.sourceMaps()
   .webpackConfig({
        devtool: 'source-map',
        plugins: [
            new MomentLocalesPlugin(), // To strip all locales except “en”
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
                // https://github.com/brockpetrie/vue-moment/issues/117
                'vue-moment': path.resolve(
                    __dirname,
                    'node_modules/vue-moment/vue-moment.js'
                ),
            }
        }
    })
   .js('resources/js/app.js', 'public/js')
   .extract()
   .sass('resources/sass/app.scss', 'public/css')
   .options({
        extractVueStyles: 'public/css/vue.css',
        // need mix 6 and webpack 5 :(
        // globalVueStyles: [
        //   '~bootstrap/scss/functions',
        //   '~bootstrap/scss/variables',
        //   '~bootstrap/scss/mixins',
        //   'resources/sass/_variables.scss',
        // ],
    })
   .version();

if (!mix.inProduction()) {
    mix.bundleAnalyzer({
        analyzerMode: 'static',
        openAnalyzer: false,
    });
}
