import {RouteRecordRaw} from 'vue-router'

const routes: Array<RouteRecordRaw> = [
    {path: '/login', name: 'login', component: () => import("./Login.vue")},
    {path: '/', name: 'home', component: () => import("./Pages/Welcome.vue")},

    {path: '/profile', name: 'profile', component: () => import("./Pages/ProfilePage.vue")},

    {path: '/system/staff', name: 'staff', component: () => import("./Pages/System/StaffListPage.vue")},
    {path: '/system/staff/create', name: 'staff_create', component: () => import("./Pages/System/StaffCreatePage.vue")},
    {path: '/system/staff/:id', name: 'staff_view', component: () => import("./Pages/System/StaffPage.vue")},
    {path: '/system/users', name: 'users', component: () => import("./Pages/System/UsersPage.vue")},
    {path: '/system/roles', name: 'roles', component: () => import("./Pages/System/RolesPage.vue")},
    {path: '/system/settings', name: 'settings', component: () => import("./Pages/System/Settings.vue")},
    {path: '/system/history', name: 'history', component: () => import("./Pages/System/HistoryPage.vue")},

    {path: '/test', name: 'test', component: () => import("./Pages/TestPage.vue")},
];

export default routes;
