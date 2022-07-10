import toaster from "./Core/Toaster/Toaster";

require('./bootstrap');



// add router
// import routes from '@/Definitions/Admin/Routes';
// import {createRouter, createWebHistory} from 'vue-router';
// const router = createRouter({
//     history: createWebHistory(),
//     routes: routes,
// });

// configure store
// import {createStore} from 'vuex';
// import DictionaryStore from "./Stores/dictionary-store";
// import PermissionsStore from "./Stores/permissions-store";
// const store = createStore({
//     modules: {
//         dictionary: DictionaryStore,
//         permissions: PermissionsStore,
//     }
// });
//
//let user = typeof window.user === "undefined" ? null : JSON.parse(window.user);
//
//import {createApp} from 'vue';
//
//import App from '@/Apps/AdminApp.vue';
//// import menu from '@/Definitions/Admin/Menu';
//const app = createApp(App, {/*menu: menu, */ user: user});
//
//app.use(store);
//// import Dialog from "@/Plugins/Dialog/dialog";
//import Toaster from "@/Plugins/Toaster/toaster-plugin";
//import Highlight from "@/Plugins/Highlight/highlight-plugin";
//// app.use(router);
//// app.use(Dialog);
//app.use(Toaster);
//app.use(Highlight);
//
//app.mount('#app');
//
