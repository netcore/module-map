let mix = require('laravel-mix');
let path = require('path');

const config = {
    dev: process.env.NODE_ENV === 'development',
    src: __dirname + '/node_modules/',
    res: __dirname + '/Resources/assets/',
    out: __dirname + '/Assets/'
};

// Configure mix.
mix.js(path.join(config.res, 'js/index.js'), path.join(config.out, 'js/index.js'));
mix.js(path.join(config.res, 'js/form.js'), path.join(config.out, 'js/form.js'));

mix.disableNotifications();

