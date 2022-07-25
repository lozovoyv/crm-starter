import {RouteRecordRaw} from 'vue-router'

const routes: Array<RouteRecordRaw> = [
    {path: '/login', name: 'login', component: () => import("./Login.vue")},
    {path: '/', name: 'home', component: () => import("./Pages/Welcome.vue")},
    {path: '/test', name: 'test', component: () => import("./Pages/Test.vue")},
];

export default routes;
