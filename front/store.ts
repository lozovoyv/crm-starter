// configure store
import {createStore} from 'vuex';
// import DictionaryStore from "./Stores/DictionaryStore";
import UserStore from "./Stores/UserStore";

const store = createStore({
    modules: {
        // dictionary: DictionaryStore,
        user: UserStore,
    }
});

export default store;
