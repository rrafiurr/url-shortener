import Vue from 'vue';
import VueRouter from 'vue-router';

import Shortener from './components/Shortener.vue'


Vue.use(VueRouter);


const router = new VueRouter({
    mode: 'history',
    linkExactActiveClass: 'active',
    routes: [
        {
            'name' : 'Home',
            'path' : '/',
            'component' : Shortener
        },
    ]
});

export default router;