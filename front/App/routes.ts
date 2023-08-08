import {NavigationGuardNext, RouteLocationNormalized, RouteRecordRaw} from 'vue-router'
import store from "@/store";

function getUser() {
    return new Promise((resolve) => {
        const user = store.getters['user/user'];
        if (user === undefined) {
            store.dispatch('user/refresh').then(user => {
                resolve(user);
            });
        } else {
            resolve(user);
        }
    });
}

async function auth(to: RouteLocationNormalized, from: RouteLocationNormalized, next: NavigationGuardNext) {
    const user = await getUser();
    if (user === null) {
        next({name: 'login'});
    } else {
        next();
    }
}

async function guest(to: RouteLocationNormalized, from: RouteLocationNormalized, next: NavigationGuardNext) {
    const user = await getUser();
    if (user !== null) {
        next({name: 'home'});
    } else {
        next();
    }
}

const routes: Array<RouteRecordRaw> = [
    {path: '/login', name: 'login', component: () => import("./Login.vue"), beforeEnter: [guest]},
    {path: '/', name: 'home', component: () => import("./Pages/Welcome.vue"), beforeEnter: [auth]},

    {path: '/confirm/email', component: () => import("./Pages/Confirm/ConfirmEmailPage.vue")},

    {path: '/profile', name: 'profile', component: () => import("./Pages/ProfilePage.vue"), beforeEnter: [auth]},

    {path: '/system/staff', name: 'staff', component: () => import("./Pages/System/Staff/StaffListPage.vue"), beforeEnter: [auth]},
    {path: '/system/staff/create', name: 'staff_create', component: () => import("./Pages/System/Staff/StaffCreatePage.vue"), beforeEnter: [auth]},
    {path: '/system/staff/:id', name: 'staff_view', component: () => import("./Pages/System/Staff/StaffPage.vue"), beforeEnter: [auth]},

    {path: '/system/users', name: 'users', component: () => import("./Pages/System/Users/UsersPage.vue"), beforeEnter: [auth]},
    {path: '/system/users/create', name: 'user_create', component: () => import("./Pages/System/Users/UserCreatePage.vue"), beforeEnter: [auth]},
    {path: '/system/users/:id', name: 'user_view', component: () => import("./Pages/System/Users/UserPage.vue"), beforeEnter: [auth]},
    {path: '/system/users/:id/edit', name: 'user_edit', component: () => import("./Pages/System/Users/UserEditPage.vue"), beforeEnter: [auth]},

    {path: '/system/permissions', name: 'permissions', component: () => import("./Pages/System/Permissions/PermissionsPage.vue"), beforeEnter: [auth]},
    {path: '/system/settings', name: 'settings', component: () => import("./Pages/System/Settings.vue"), beforeEnter: [auth]},
    {path: '/system/history', name: 'history', component: () => import("./Pages/System/HistoryPage.vue"), beforeEnter: [auth]},

    {path: '/system/dictionaries', name: 'dictionaries', component: () => import("./Pages/System/Dictionaries/DictionariesPage.vue"), beforeEnter: [auth]},

    {path: '/test', name: 'test', component: () => import("./Pages/TestPage.vue"), beforeEnter: [auth]},
];

export default routes;
