import { createStore } from 'vuex';
import User from "@/store/modules/User";
import Title from './modules/Title';
import Profile from './modules/Profile';

export default createStore({
    modules: {
        User,
        Title,
        Profile,
        

    }
});