<template>
    <div class="flex flex-col items-center py-4">
        <NewPost />
        
        <p v-if="loading">Now loading...</p>
        <Post v-else v-for="post in posts.data" :key="post.data.post_id" :post="post"/>
    </div>
</template>

<script>
import NewPost from "@/components/NewPost.vue";
import Post from "@/components/Post.vue";
import axios from "axios";

export default {
    name: "NewFeed",

    components: {
        NewPost,
        Post,
    },

    data() {
        return {
            posts: [],
            loading: true,
        }
    },

    mounted() {
        axios.get('/api/posts')
            .then(res => {
                this.posts = res.data;
            })
            .catch(error => {
                console.log('Unable to fetch posts');
            })
            .finally(() => {
                this.loading = false;
            });
    }
};
</script>