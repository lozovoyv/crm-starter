import {createRouter, createWebHistory} from 'vue-router';
import routes from "./App/routes";

const router = createRouter({
    history: createWebHistory(),
    routes: routes,
});

export default router
