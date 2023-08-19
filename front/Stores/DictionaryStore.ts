import {http} from "@/Core/Http/Http";
import {ActionTree, GetterTree, MutationTree} from "vuex";
import {AxiosResponse} from "axios";
import toaster from "@/Core/Toaster/Toaster";

const dictionariesUrl = '/api/dictionary/';

class State {
    dictionaries: { [index: string]: Array<{ [index: string]: any }> } = {};
    timestamps: { [index: string]: string } = {};
    editable: { [index: string]: boolean } = {};
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
    setDictionaryEditable(state: State, payload: { name: string, editable: boolean }) {
        state.editable[payload.name] = payload.editable;
    },
};

const getters = <GetterTree<State, any>>{
    dictionary: (state: State) => (name: string): Array<{ [index: string]: any }> | null => {
        return state.dictionaries[name] !== undefined ? state.dictionaries[name] : null;
    },
    ready: (state: State) => (name: string): boolean => {
        return state.states[name] !== undefined ? state.states[name] === true : false;
    },
    editable: (state: State) => (name: string): boolean => {
        return state.editable[name] !== undefined ? state.editable[name] : false;
    },
    item: (state: State) => (name: string, id: number): { [index: string]: any } | undefined => {
        if (state.dictionaries[name] === undefined) {
            return undefined;
        }
        return state.dictionaries[name].find(item => !!item['id'] && item['id'] === id);
    },
};

const actions = <ActionTree<State, any>>{
    refresh({commit, state}, dictionary: string) {

        return new Promise((resolve: (value: any) => void, reject: (reason: string) => void) => {
            let headers: { [index: string]: string } = {};
            if (state.timestamps[dictionary] !== undefined && state.timestamps[dictionary] !== null) {
                headers['if-modified-since'] = state.timestamps[dictionary];
            } else {
                headers['x-force-update'] = 'true';
            }
            http.get<{ dictionary: string }, AxiosResponse>(dictionariesUrl + dictionary, {headers: headers})
                .then(response => {
                    // set loading state
                    commit('setDictionary', {name: dictionary, dictionary: response.data['list']});
                    commit('setDictionaryTimestamp', {
                        name: dictionary,
                        timestamp: response.headers['last-modified'] !== undefined ? response.headers['last-modified'] : null
                    });
                    commit('setDictionaryState', {name: dictionary, state: true});
                    commit('setDictionaryEditable', {name: dictionary, editable: response.data['payload'] && response.data['payload']['is_editable']});
                    resolve(state.dictionaries[dictionary] !== undefined ? state.dictionaries[dictionary] : null);
                })
                .catch(error => {
                    if (error.status === 304) {
                        commit('setDictionaryState', {name: dictionary, state: true});
                        resolve(state.dictionaries[dictionary] !== undefined ? state.dictionaries[dictionary] : null);
                        return;
                    }
                    if(error.status !== 500) {
                        toaster.error(error.data.message);
                    }
                    reject(error.status);
                });
        });
    },

    refreshForce({commit, state}, dictionary: string) {
        return new Promise((resolve: (value: any) => void, reject: (reason: string) => void) => {
            http.post<{ dictionary: string }, AxiosResponse>(dictionariesUrl, {dictionary: dictionary})
                .then(response => {
                    // set loading state
                    commit('setDictionary', {name: dictionary, dictionary: response.data['data']});
                    commit('setDictionaryTimestamp', {
                        name: dictionary,
                        timestamp: response.headers['last-modified'] !== undefined ? response.headers['last-modified'] : null
                    });
                    commit('setDictionaryState', {name: dictionary, state: true});
                    commit('setDictionaryEditable', {name: dictionary, editable: response.data['payload'] && response.data['payload']['is_editable']});
                    resolve(state.dictionaries[dictionary] !== undefined ? state.dictionaries[dictionary] : null);
                })
                .catch(error => {
                    if (error.status === 304) {
                        commit('setDictionaryState', {name: dictionary, state: true});
                        resolve(state.dictionaries[dictionary] !== undefined ? state.dictionaries[dictionary] : null);
                        return;
                    }
                    if(error.status !== 500) {
                        toaster.error(error.data.message);
                    }
                    reject(error.status);
                });
        });
    }
};

const DictionaryStore = {
    namespaced: true,
    state: new State(),
    mutations: mutations,
    getters: getters,
    actions: actions,
};

export default DictionaryStore;
