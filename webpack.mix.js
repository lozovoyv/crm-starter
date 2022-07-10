const mix = require('laravel-mix');

mix.setPublicPath('./public/');

/*
 |--------------------------------------------------------------------------
 | Login form assets
 |--------------------------------------------------------------------------
 */
mix
    .webpackConfig(require('./webpack.config.js'))
    .options({
        assetModules: true,
        fileLoaderDirs: {
            images: 'images',
            fonts: 'fonts'
        },
        processCssUrls: false,
    })
    .copy('front/assets/fonts/source-sans-pro/', 'public/fonts/')
    //.sourceMaps(true)
    .ts('front/login.ts', 'js')
    .ts('front/app.ts', 'js')
    .vue();

if (mix.inProduction()) {
    mix.version();
}
