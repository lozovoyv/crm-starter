const getCurrentUserUrl = '/api/current';

import {GetterTree, MutationTree, ActionTree} from "vuex"
import {http} from "../Core/Http/Http";
import {User} from "../Core/Types/User";

class State {
    user: User = null;
    permissions: string[] = [];
}

const mutations = <MutationTree<State>>{
    setPermissions(state: State, permissions: string[]) {
        state.permissions = permissions;
    },
    setUser(state: State, user: User) {
        state.user = user;
    },
};

const getters = <GetterTree<State, any>>{
    user(state: State): User {
        return state.user;
    },
    permissions(state: State): string[] {
        return state.permissions;
    },
    can: (state: State) => (permission: string): boolean => {
        return state.permissions.indexOf(permission) !== -1;
    },
};

const actions = <ActionTree<State, any>>{
    refresh({commit}) {
        return new Promise((resolve: (value: any) => void, reject: (reason: string) => void) => {
            http.post<{}, { data: { [index: string]: any } }>(getCurrentUserUrl, {})
                .then(response => {
                    const data = response.data['data'];
                    commit('setUser', data['user']);
                    commit('setPermissions', data['permissions']);
                    resolve(data);
                })
                .catch(error => {
                    const data = error.response.data['data'];
                    reject(data['message']);
                });
        });
    },
};

const UserStore = {
    namespaced: true,
    state: new State(),
    mutations: mutations,
    getters: getters,
    actions: actions,
};

export default UserStore;
