<script>
import axios from 'axios';
import Post from "@/components/Post.vue";
import { mapGetters } from 'vuex';

export default {
    name: "Show",

    components: {
        Post,
    },

    data() {
        return {
            posts: {},
            postLoading: true,
        }
    },

    mounted() {
        this.$store.dispatch('fetchUser',  this.$route.params.userId);

        axios.get('/api/users/' + this.$route.params.userId + '/posts')
            .then(res => {
                this.posts = res.data;
            })
            .catch(error => {
                console.log('Unable to fetch posts');
            })
            .finally(() => {
                this.postLoading = false;
            });
    },

    computed: {
        ...mapGetters({
            user: 'user',
        }),
    }
}

</script>


<template>
    <div class="flex flex-col items-center">
        <div class="relative mb-8">

            <div class="w-100 h-64 overflow-hidden z-10">
                <img src="https://th.bing.com/th/id/R.4eb01b6d1de8180fc16a7ea457df2dd0?rik=mDmVmiPWKjG19Q&riu=http%3a%2f%2fwallup.net%2fwp-content%2fuploads%2f2016%2f01%2f102787-nature-mountain-river-landscape.jpg&ehk=tKQDONLRX3EXEvzzdRuXB5UBE3a0IgFKug46zeMojOg%3d&risl=&pid=ImgRaw&r=0" alt="user background image" class="object-cover w-full">
            </div>

            <div class="absolute flex items-center bottom-0 left-0 -mb-8 ml-12 z-20">
                <div>
                    <img src="https://th.bing.com/th/id/OIP.UghT0woG1H9eTHb_F0LXyAHaFA?w=226&h=180&c=7&r=0&o=5&dpr=1.2&pid=1.7" alt="profile image for user" class="w-32 h-32 border-4 border-gray-200 shadow-lg object-cover rounded-full">
                </div>

                <p class="text-2xl text-gray-200 ml-4">{{ user.data.attributes.name }}</p>
            </div>

            <div class="absolute flex items-center bottom-0 right-0 mb-8 mr-12 z-20">
                <button class="py-1 px-3 bg-gray-400 rounded  focus:outline-none focus:ring focus:ring-blue-400">
                    Add Friend
                </button>
            </div>

        </div>

        <p v-if="postLoading">Loading posts...</p>
        <Post v-else v-for="post in posts.data" :key="post.id" :post="post" />

        <p v-if=" !postLoading && posts.data.length < 1">NO posts found. Get started...</p>
    </div>
</template>