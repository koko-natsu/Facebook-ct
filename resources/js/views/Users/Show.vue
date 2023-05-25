<template>
    <div>
        <div class="w-100 h-64 overflow-hidden">
            <img src="https://th.bing.com/th/id/R.4eb01b6d1de8180fc16a7ea457df2dd0?rik=mDmVmiPWKjG19Q&riu=http%3a%2f%2fwallup.net%2fwp-content%2fuploads%2f2016%2f01%2f102787-nature-mountain-river-landscape.jpg&ehk=tKQDONLRX3EXEvzzdRuXB5UBE3a0IgFKug46zeMojOg%3d&risl=&pid=ImgRaw&r=0" alt="user background image" class="object-cover w-full">
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: "Show",

    data: () => {
        return {
            user: [],
            loading: true,
        }
    },

    mounted() {
        axios.get('/api/users/' + this.$route.params.userId)
            .then(res => {
                this.user = res.data;
            })
            .catch(error => {
                console.log('Unable to fetch the user from the server.');
            })
            .finally(() => {
                this.loading = false;
            });


        axios.get('/api/posts/' + this.$route.params.userId)
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
}
</script>