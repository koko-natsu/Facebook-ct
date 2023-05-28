import { createRouter, createWebHashHistory } from "vue-router";
import NewsFeed from "@/views/NewsFeed.vue";
import UserShow from "@/views/Users/Show.vue";

export default createRouter({
    history: createWebHashHistory(),
    routes: [
        {
            path: '/', name: 'home', component: NewsFeed,
            meta: {
                title: 'News Feed',
            }
        },
        {
            path: '/users/:userId', name: 'user.show', component: UserShow,
            meta: {
                title: 'Profile',
            }
        },
    ]
})