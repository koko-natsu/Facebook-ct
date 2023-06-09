import { createStore } from 'vuex';
import User from "@/store/modules/User";
import Title from './modules/Title';
import Profile from './modules/Profile';
import Post from './modules/Post';

export default createStore({
    modules: {
        User,
        Title,
        Profile,
        Post,
    }
});