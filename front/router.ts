import {createRouter, createWebHistory} from 'vue-router';
import routes from "./App/routes";
import store from "./store";

const router = createRouter({
    history: createWebHistory(),
    routes: routes,
});

router.beforeEach((to, from, next) => {
    return new Promise((resolve) => {
        store.dispatch('user/refresh')
            .then(response => {
                const isAuthenticated = response['user'] !== null;

                if (to.name === 'login') {
                    resolve(!isAuthenticated ? next() : next({name: 'home'}));
                } else {
                    // todo check permissions
                    resolve(isAuthenticated ? next() : next({name: 'login'}));
                }

            })
            .catch(error => {
                throw error;
            });
    })
});

export default router
