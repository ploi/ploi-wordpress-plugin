const mix = require('laravel-mix');
const tailwindcss = require('tailwindcss');


mix.js("resources/js/app.js", "assets/js/app.js")
    .sourceMaps();

mix.postCss('./resources/css/style.css', './assets/css/style.css',
    tailwindcss('./tailwind.config.js')
);
