const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js')
   .js('resources/js/chart-config.js', 'public/js')
   .sass('resources/sass/app.scss', 'public/css')
   .options({
     processCssUrls: false,
   })
   .sourceMaps()
   .webpackConfig({
     externals: {
       jquery: 'jQuery',
     },
   });

if (mix.inProduction()) {
  mix.version();
}
