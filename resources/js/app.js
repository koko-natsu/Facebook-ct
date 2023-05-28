
import './bootstrap';
import { createApp } from 'vue';
import router from '@/router';
import App from '@/components/App.vue';
import store from '@/store';

const app = createApp({
    components: {
        App
    },
});

app.use(router).use(store).mount('#app');
