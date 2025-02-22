const mix = require('laravel-mix');
const config = require('./webpack.config')

mix.js('resources/js/app.js', 'public/js')
  .vue()
  .sass('resources/sass/app.scss', 'public/css')
  .extract()
  .version()
  .webpackConfig(config);
