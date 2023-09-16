/*
 * This file is part of Opxx Starter project
 *
 * (c) Viacheslav Lozovoy <vialoz@yandex.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import store from "@/store";
import {NavigationGuardNext, RouteLocationNormalized,} from 'vue-router';

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

export async function auth(to: RouteLocationNormalized, from: RouteLocationNormalized, next: NavigationGuardNext) {
    const user = await getUser();
    if (user === null) {
        next({name: 'login'});
    } else {
        next();
    }
}

export async function guest(to: RouteLocationNormalized, from: RouteLocationNormalized, next: NavigationGuardNext) {
    const user = await getUser();
    if (user !== null) {
        next({name: 'home'});
    } else {
        next();
    }
}
