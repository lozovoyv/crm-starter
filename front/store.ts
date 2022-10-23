import {createStore} from 'vuex';
import UserStore from "./Stores/UserStore";
import DictionaryStore from "@/Stores/DictionaryStore";

const store = createStore({
    modules: {
        user: UserStore,
        dictionaries: DictionaryStore,
    }
});

export default store;
