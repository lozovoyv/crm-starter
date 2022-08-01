import {RouteRecordRaw} from 'vue-router'

const routes: Array<RouteRecordRaw> = [
    {path: '/login', name: 'login', component: () => import("./Login.vue")},
    {path: '/', name: 'home', component: () => import("./Pages/Welcome.vue")},
    {path: '/test', name: 'test', component: () => import("./Pages/Test.vue")},

    // testing
    {path: '/test1', name: 'test1', component: () => import("./Pages/Test.vue")},
    {path: '/test2', name: 'test2', component: () => import("./Pages/Test.vue")},
    {path: '/test3', name: 'test3', component: () => import("./Pages/Test.vue")},
    {path: '/test4', name: 'test4', component: () => import("./Pages/Test.vue")},
    {path: '/test5', name: 'test5', component: () => import("./Pages/Test.vue")},
    {path: '/test6', name: 'test6', component: () => import("./Pages/Test.vue")},
    {path: '/test7', name: 'test7', component: () => import("./Pages/Test.vue")},
    {path: '/test8', name: 'test8', component: () => import("./Pages/Test.vue")},
    {path: '/test9', name: 'test9', component: () => import("./Pages/Test.vue")},
    {path: '/test10', name: 'test10', component: () => import("./Pages/Test.vue")},
    {path: '/test11', name: 'test11', component: () => import("./Pages/Test.vue")},
    {path: '/test12', name: 'test12', component: () => import("./Pages/Test.vue")},
    {path: '/test13', name: 'test13', component: () => import("./Pages/Test.vue")},
    {path: '/test14', name: 'test14', component: () => import("./Pages/Test.vue")},
    {path: '/test15', name: 'test15', component: () => import("./Pages/Test.vue")},
    {path: '/test16', name: 'test16', component: () => import("./Pages/Test.vue")},
    {path: '/test17', name: 'test17', component: () => import("./Pages/Test.vue")},
    {path: '/test18', name: 'test18', component: () => import("./Pages/Test.vue")},
    {path: '/test19', name: 'test19', component: () => import("./Pages/Test.vue")},
    {path: '/test20', name: 'test20', component: () => import("./Pages/Test.vue")},
    {path: '/test21', name: 'test21', component: () => import("./Pages/Test.vue")},
    {path: '/test22', name: 'test22', component: () => import("./Pages/Test.vue")},
    {path: '/test23', name: 'test23', component: () => import("./Pages/Test.vue")},
    {path: '/test24', name: 'test24', component: () => import("./Pages/Test.vue")},
    {path: '/test25', name: 'test25', component: () => import("./Pages/Test.vue")},
    {path: '/test26', name: 'test26', component: () => import("./Pages/Test.vue")},
    {path: '/test27', name: 'test27', component: () => import("./Pages/Test.vue")},
    {path: '/test28', name: 'test28', component: () => import("./Pages/Test.vue")},
    {path: '/test29', name: 'test29', component: () => import("./Pages/Test.vue")},
    {path: '/test30', name: 'test30', component: () => import("./Pages/Test.vue")},
    {path: '/test31', name: 'test31', component: () => import("./Pages/Test.vue")},
    {path: '/test32', name: 'test32', component: () => import("./Pages/Test.vue")},
    {path: '/test33', name: 'test33', component: () => import("./Pages/Test.vue")},
    {path: '/test34', name: 'test34', component: () => import("./Pages/Test.vue")},
    {path: '/test35', name: 'test35', component: () => import("./Pages/Test.vue")},

];

export default routes;
