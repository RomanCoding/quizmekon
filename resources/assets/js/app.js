
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
window.moment = require('moment');

import VueRouter from 'vue-router';
import VueYoutube from 'vue-youtube';

import Vue from 'vue';
export const EventBus = new Vue();
window.Event = EventBus;

Vue.use(VueYoutube)
Vue.use(VueRouter)

import Buefy from 'buefy'
import 'buefy/lib/buefy.css'

import { library } from '@fortawesome/fontawesome-svg-core'
import { faArrowCircleDown } from '@fortawesome/free-solid-svg-icons'
import { faArrowCircleUp } from '@fortawesome/free-solid-svg-icons'
import { faCommentAlt } from '@fortawesome/free-solid-svg-icons'
import { faFlag } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

library.add(faArrowCircleDown)
library.add(faArrowCircleUp)
library.add(faCommentAlt)
library.add(faFlag)

window.Vue.use(Buefy)


Vue.component('font-awesome-icon', FontAwesomeIcon)




Vue.component('registration', require('./components/Registration.vue'));
Vue.component('login', require('./components/Login.vue'));
Vue.component('site-main', require('./components/Main.vue'));
Vue.component('site-header', require('./components/Header.vue'));

import authMixin from './mixins/auth'

import Main from './components/Main.vue'
import QuizPage from './components/QuizPage.vue'
import CreatePost from './components/CreatePost.vue'
import Login from './components/Login.vue'

const routes = [
    { path: '/submit', component: CreatePost, meta: { requiresAuth: true}},
    { path: '/login', component: Login },
    { path: '/:sort?', component: Main },
    { path: '/r/:subreddit/:quiz(\\d+)', component: QuizPage },
    { path: '/r/:subreddit/:sort?', component: Main },
];
const router = new VueRouter({
    mode: 'history',
    routes
});


router.beforeEach((to, from, next) => {
    if (to.matched.some(record => record.meta.requiresAuth)) {
        axios.get('/sessionStatus')
            .then(response => {
                if (response.data.user) {
                    next();
                } else {
                    next({
                        path: '/login',
                        query: {
                            redirect: to.fullPath,
                        },
                    });
                }
            })
            .catch(error => {
                next({
                    path: '/login',
                    query: {
                        redirect: to.fullPath,
                    },
                });
            });
    } else {
        next();
    }
});

const app = new Vue({
    mixins: [authMixin],
    el: '#app',
    router,
    data: {
        user: null
    },
    created(){
        this.checkIfLogged()
            .then(response => {
                this.user = response ? response : false;
            })
            .catch(error => console.log(error));
    },
});

