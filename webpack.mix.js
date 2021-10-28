const mix = require('laravel-mix');
const path = require('path');
const {exec} = require('child_process');
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

mix.extend('ziggy', new class {
    register(config = {}) {
        this.watch = config.watch ?? ['routes/**/*.php'];
        this.path = config.path ?? '';
        this.enabled = config.enabled ?? !Mix.inProduction();
    }

    boot() {
        if (!this.enabled) return;

        const command = () => exec(
            `php artisan ziggy:generate ${this.path}`,
            (error, stdout, stderr) => console.log(stdout)
        );

        command();

        if (Mix.isWatching() && this.watch) {
            ((require('chokidar')).watch(this.watch))
                .on('change', (path) => {
                    console.log(`${path} changed...`);
                    command();
                });
        };
    }
}());

mix.sourceMaps()
    .webpackConfig({
        devtool: 'source-map',
        plugins: [
            new MomentLocalesPlugin({ // To strip all locales except “en-gb”
                localesToKeep: ['en-gb'],
            }),
        ],
        resolve: {
            alias: {
                'sass': path.resolve(__dirname, 'resources/sass'),
                ziggy: path.resolve('vendor/tightenco/ziggy/dist'),
                // https://github.com/brockpetrie/vue-moment/issues/117
                'vue-moment': path.resolve(
                    __dirname,
                    'node_modules/vue-moment/vue-moment.js'
                ),
            }
        }
    })
    .ziggy()
    .js('resources/js/app.js', 'public/js')
    .vue({
      extractStyles: 'public/css/vue.css',
      globalStyles: {
        scss: [
          process.env.THEME ? `resources/sass/_variables_${process.env.THEME}.scss` : 'resources/sass/_variables.scss',
          'node_modules/bootstrap/scss/_functions.scss',
          'node_modules/bootstrap/scss/_variables.scss',
          'node_modules/bootstrap/scss/_mixins.scss',
        ],
      }
    })
    .extract()
    .sass('resources/sass/app.scss', 'public/css', {
        additionalData: process.env.THEME ? `@import 'variables_${process.env.THEME}';` : "@import 'variables';",
    })
    .version();

if (!mix.inProduction()) {
    mix.bundleAnalyzer({
        analyzerMode: 'static',
        openAnalyzer: false,
    });
}
