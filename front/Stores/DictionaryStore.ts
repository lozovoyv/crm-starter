import {http} from "@/Core/Http/Http";
import {ActionTree, GetterTree, MutationTree} from "vuex";
import {AxiosResponse} from "axios";

const dictionariesUrl = '/api/dictionaries';

class State {
    dictionaries: { [index: string]: Array<{ [index: string]: any }> } = {};
    timestamps: { [index: string]: string } = {};
    states: { [index: string]: boolean | null } = {};
}

const mutations = <MutationTree<State>>{
    setDictionary(state: State, payload: { name: string, dictionary: Array<{ [index: string]: any }> }) {
        state.dictionaries[payload.name] = payload.dictionary;
    },
    setDictionaryState(state: State, payload: { name: string, state: boolean | null }) {
        state.states[payload.name] = payload.state;
    },
    setDictionaryTimestamp(state: State, payload: { name: string, timestamp: string }) {
        state.timestamps[payload.name] = payload.timestamp;
    },
};

const getters = <GetterTree<State, any>>{
    dictionary: (state: State) => (name: string): Array<{ [index: string]: any }> | null => {
        return state.dictionaries[name] !== undefined ? state.dictionaries[name] : null;
    },
    ready: (state: State) => (name: string): boolean => {
        return state.states[name] !== undefined ? state.states[name] === true : false;
    },
};

const actions = <ActionTree<State, any>>{
    refresh({commit, state}, dictionary: string) {
        return new Promise((resolve: (value: any) => void, reject: (reason: string) => void) => {
            let headers: { [index: string]: string } = {};
            if (state.timestamps[dictionary] !== undefined && state.timestamps[dictionary] !== null) {
                headers['if-modified-since'] = state.timestamps[dictionary];
            }
            http.post<{dictionary: string}, AxiosResponse>(dictionariesUrl, {dictionary: dictionary}, {
                headers: headers,
            })
                .then(response => {
                    // set loading state
                    commit('setDictionary', {name: dictionary, dictionary: response.data['data']});
                    commit('setDictionaryTimestamp', {
                        name: dictionary,
                        timestamp:  response.headers['last-modified'] !== undefined ? response.headers['last-modified'] : null
                    });
                    commit('setDictionaryState', {name: dictionary, state: true});
                    resolve( state.dictionaries[dictionary] !== undefined ? state.dictionaries[dictionary] : null);
                })
                .catch(error => {
                    if (error.status === 304) {
                        commit('setDictionaryState', {name: dictionary, state: true});
                        resolve( state.dictionaries[dictionary] !== undefined ? state.dictionaries[dictionary] : null);
                        return;
                    }
                    reject(error.response.status);
                });
        });
    },
};

const DictionaryStore = {
    namespaced: true,
    state: new State(),
    mutations: mutations,
    getters: getters,
    actions: actions,
};

export default DictionaryStore;
