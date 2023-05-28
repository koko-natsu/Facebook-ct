import { createStore } from 'vuex';
import User from "@/store/modules/User";
import Title from './modules/Title';

export default createStore({
    modules: {
        User,
        Title,
    }
});