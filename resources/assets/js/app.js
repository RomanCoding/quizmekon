
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
window.moment = require('moment');

import Buefy from 'buefy'
import 'buefy/lib/buefy.css'



import { library } from '@fortawesome/fontawesome-svg-core'
import { faArrowCircleDown } from '@fortawesome/free-solid-svg-icons'
import { faArrowCircleUp } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

library.add(faArrowCircleDown)
library.add(faArrowCircleUp)

window.Vue.use(Buefy)

Vue.component('font-awesome-icon', FontAwesomeIcon)

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */




Vue.component('registration', require('./components/Registration.vue'));
Vue.component('login', require('./components/Login.vue'));
Vue.component('site-main', require('./components/Main.vue'));
Vue.component('site-header', require('./components/Header.vue'));

import authMixin from './mixins/auth'

const app = new Vue({
    mixins: [authMixin],
    el: '#app',
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
