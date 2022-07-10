import ToastPlugin from './toaster';

export default {
    install: (app, options) => {
        if(!options) {
            options = {};
        }

        app.config.globalProperties.$toaster = new ToastPlugin(options);
    }
}
