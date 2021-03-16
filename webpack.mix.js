const mix = require('laravel-mix');
const path = require('path');
const { exec } = require('child_process');
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
                ziggy: path.resolve('vendor/tightenco/ziggy/src/js/route.js'),
                // https://github.com/brockpetrie/vue-moment/issues/117
                'vue-moment': path.resolve(
                    __dirname,
                    'node_modules/vue-moment/vue-moment.js'
                ),
            }
        }
    })
    .ziggy({
        path: 'resources/js/ziggy.js',
    })
    .js('resources/js/app.js', 'public/js')
    .vue({
      extractStyles: 'public/css/vue.css',
      // need mix 6 and webpack 5 :(
      globalStyles: {
        scss: [
          '~bootstrap/scss/functions',
          '~bootstrap/scss/variables',
          '~bootstrap/scss/mixins',
          'resources/sass/_variables.scss',
        ],
      }
    })
    .extract()
    .sass('resources/sass/app.scss', 'public/css')
    .options({
    })
    .version();

if (!mix.inProduction()) {
    mix.bundleAnalyzer({
        analyzerMode: 'static',
        openAnalyzer: false,
    });
}
