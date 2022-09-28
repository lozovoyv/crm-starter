import {RouteRecordRaw} from 'vue-router'

const routes: Array<RouteRecordRaw> = [
    {path: '/login', name: 'login', component: () => import("./Login.vue")},
    {path: '/', name: 'home', component: () => import("./Pages/Welcome.vue")},

    {path: '/profile', name: 'profile', component: () => import("./Pages/ProfilePage.vue")},

    {path: '/settings/users', name: 'users', component: () => import("./Pages/Settings/Users.vue")},
    {path: '/settings/roles', name: 'roles', component: () => import("./Pages/Settings/RolesPage.vue")},
    {path: '/settings/settings', name: 'settings', component: () => import("./Pages/Settings/Settings.vue")},

];

export default routes;
