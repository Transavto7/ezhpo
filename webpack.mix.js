const mix = require('laravel-mix');
const config = require('./webpack.config')

mix.js('resources/js/app.js', 'public/js')
    .js('resources/js/helpers/signature.js', 'public/js')
    .js('resources/js/helpers/cades.js', 'public/js')
    .vue()
    .sass('resources/sass/app.scss', 'public/css')
    .extract()
    .version()
    .webpackConfig(config);
