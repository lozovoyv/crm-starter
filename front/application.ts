require('./bootstrap');

import {createApp} from 'vue';
import Application from './App/Application.vue';

const application = createApp(Application, {});

import store from "./store";
application.use(store);

import router from "./router";
application.use(router);

application.mount('#application');
