const mix = require('laravel-mix');
const config = require('./webpack.config')

mix.ts('resources/js/app.ts', 'public/js')
  .vue()
  .sass('resources/sass/app.scss', 'public/css')
  .extract()
  .version()
  .webpackConfig(config);
