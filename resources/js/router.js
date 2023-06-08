import { createRouter, createWebHashHistory } from "vue-router";
import NewsFeed from "@/views/NewsFeed.vue";
import UserShow from "@/views/Users/Show.vue";


const routes = [
    {
        path: '/',
        name: 'home',
        component: NewsFeed,
        meta: {
            title: 'News Feed',
        }
    },
    {
        path: '/users/:userId(\\d+)?',
        name: 'user.show',
        component: UserShow,
        meta: {
            title: 'Profile',
        }
    },
];


export default createRouter({
    history: createWebHashHistory(),
    routes,
})