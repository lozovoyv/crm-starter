import {RouteRecordRaw} from 'vue-router'

const routes: Array<RouteRecordRaw> = [
    {path: '/login', name: 'login', component: () => import("./Login.vue")},
    {path: '/', name: 'home', component: () => import("./Pages/Welcome.vue")},

    {path: '/profile', name: 'profile', component: () => import("./Pages/ProfilePage.vue")},

    {path: '/system/staff', name: 'staff', component: () => import("./Pages/System/Staff/StaffListPage.vue")},
    {path: '/system/staff/create', name: 'staff_create', component: () => import("./Pages/System/Staff/StaffCreatePage.vue")},
    {path: '/system/staff/:id', name: 'staff_view', component: () => import("./Pages/System/Staff/StaffPage.vue")},

    {path: '/system/users', name: 'users', component: () => import("./Pages/System/Users/UsersPage.vue")},
    {path: '/system/users/create', name: 'user_create', component: () => import("./Pages/System/Users/UserCreatePage.vue")},
    {path: '/system/users/:id', name: 'user_view', component: () => import("./Pages/System/Users/UserPage.vue")},
    {path: '/system/users/:id/edit', name: 'user_edit', component: () => import("./Pages/System/Users/UserEditPage.vue")},

    {path: '/system/roles', name: 'roles', component: () => import("./Pages/System/Roles/RolesPage.vue")},
    {path: '/system/settings', name: 'settings', component: () => import("./Pages/System/Settings.vue")},
    {path: '/system/history', name: 'history', component: () => import("./Pages/System/HistoryPage.vue")},

    {path: '/test', name: 'test', component: () => import("./Pages/TestPage.vue")},
];

export default routes;
