const path = require('path');

module.exports = {
    resolve: {
        alias: {
            '@': path.resolve('front'),
            config: path.join(__dirname, 'front/config', process.env.NODE_ENV)
        }
    },
    module: {
        rules: [

        ]
    }
};
