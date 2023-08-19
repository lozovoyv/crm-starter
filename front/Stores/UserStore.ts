const getCurrentUserUrl = '/api/auth/current';

import {GetterTree, MutationTree, ActionTree} from "vuex"
import {http} from "@/Core/Http/Http";
import {User} from "@/Core/Types/User";

class State {
    user: User = undefined;
    permissions: { [index: number]: string } = [];
    loading: boolean = false;
    loaded: boolean = false;
}

const mutations = <MutationTree<State>>{
    setPermissions(state: State, permissions: { [index: number]: string }) {
        state.permissions = permissions;
    },
    setUser(state: State, user: User) {
        state.user = user;
    },
    setLoaded(state: State, loaded: boolean) {
        state.loaded = loaded;
    },
    setLoading(state: State, loading: boolean) {
        state.loading = loading;
    },
};

const getters = <GetterTree<State, any>>{
    loading(state: State): boolean {
        return state.loading;
    },
    loaded(state: State): boolean {
        return state.loaded;
    },
    user(state: State): User {
        return state.user;
    },
    permissions(state: State): { [index: number]: string } {
        return state.permissions;
    },
    can: (state: State) => (permission: string): boolean => {
        return Object.keys(state.permissions).some((key): boolean => {
            // @ts-ignore
            return state.permissions[key] === permission
        });
    },
};

const actions = <ActionTree<State, any>>{
    async refresh({commit}) {
        commit('setLoading', true);
        return new Promise((resolve: (value: any) => void, reject: (reason: string) => void) => {
            http.get<{}, { data: { [index: string]: any } }>(getCurrentUserUrl, {})
                .then(response => {
                    const data = response.data['data'];
                    commit('setUser', data['user']);
                    commit('setPermissions', data['permissions']);
                    commit('setLoaded', true);
                    resolve(data['user']);
                })
                .catch(error => {
                    const data = error.response.data['data'];
                    reject(data['message']);
                })
                .finally(() => {
                    commit('setLoading', false);
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
