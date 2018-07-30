<template>
    <div class="header">
        <b-modal :active.sync="showLoginModal" has-modal-card>
            <login></login>
        </b-modal>
        <b-modal :active.sync="showSignUpModal" has-modal-card>
            <registration></registration>
        </b-modal>
        <nav class="navbar" role="navigation" aria-label="main navigation">
            <div class="navbar-brand">
                <a class="navbar-item" href="/">
                    <img src="/images/quizmekon_logo.png" alt="quizmekon" height="50">
                    <p class="titlefont">Quizmekon</p>
                </a>
            </div>
            <div id="searchbox" class="navbar-item">
                <p class="control has-icons-left">
                    <input class="input" type="text" placeholder="Search">
                    <span class="icon is-medium is-left"><i class="fas fa-search"></i></span>
                </p>
            </div>
            <div class="navbar-end">
                <div class="navbar-item">
                    <template v-if="this.$parent.$parent.user === false">
                        <a class="navbar-item" @click="toggleLoginModal">Login</a>
                        <a class="navbar-item" @click="toggleSignUpModal">Register</a>
                    </template>
                    <template v-else-if="this.$parent.$parent.user">
                        <b-dropdown>
                            <a class="navbar-item" slot="trigger">
                                {{ this.$parent.$parent.user.username }}
                                <b-icon icon="menu-down"></b-icon>
                            </a>
                            <b-dropdown-item title="Poll quizzes to get experience">Experience: {{ this.$parent.$parent.user.experience }}</b-dropdown-item>
                            <b-dropdown-item @click="logout">Logout</b-dropdown-item>
                        </b-dropdown>
                        <form action="/logout" method="POST" style="display: none;" ref="logoutForm">
                            <input type="hidden" name="_token" :value="csrf">
                        </form>
                    </template>
                </div>
            </div>
        </nav>
    </div>


    <!--<div class="container">-->
    <!--<div class="navbar-brand">-->
    <!--<a class="navbar-item" href="/">-->
    <!--Quizmekon-->
    <!--</a>-->
    <!--<span class="navbar-burger burger" data-target="navMenu">-->
    <!--<span></span>-->
    <!--<span></span>-->
    <!--<span></span>-->
    <!--</span>-->
    <!--</div>-->
    <!--<div id="navMenu" class="navbar-menu">-->
    <!--<div class="navbar-end">-->
    <!--<template v-if="this.$parent.user === false">-->
    <!--<a class="navbar-item" @click="toggleLoginModal">Login</a>-->
    <!--<a class="navbar-item" @click="toggleSignUpModal">Register</a>-->
    <!--</template>-->
    <!--<template v-else-if="this.$parent.user">-->
    <!--<b-dropdown>-->
    <!--<a class="navbar-item" slot="trigger">-->
    <!--{{ this.$parent.user.username }}-->
    <!--<b-icon icon="menu-down"></b-icon>-->
    <!--</a>-->
    <!--<b-dropdown-item title="Poll quizzes to get experience">Experience: {{ this.$parent.user.experience }}</b-dropdown-item>-->
    <!--<b-dropdown-item @click="createPost">Add new</b-dropdown-item>-->
    <!--<b-dropdown-item @click="logout">Logout</b-dropdown-item>-->
    <!--</b-dropdown>-->
    <!--<form action="/logout" method="POST" style="display: none;" ref="logoutForm">-->
    <!--<input type="hidden" name="_token" :value="csrf">-->
    <!--</form>-->
    <!--</template>-->
    <!--</div>-->
    <!--</div>-->
    <!--</div>-->
</template>

<script>
    import authMixin from '../mixins/auth'
    import Login from './modals/Login.vue'
    import Registration from './modals/Registration.vue'
    export default {
        components: {
            'login': Login,
            'registration': Registration,
        },
        mixins: [authMixin],
        data() {
            return {
                csrf: '',
                showLoginModal: false,
                showSignUpModal: false,
            };
        },
        computed: {},
        watch: {},
        mounted() {
//            let burger = document.querySelector('.burger');
//            let nav = document.querySelector('#' + burger.dataset.target);
//            burger.addEventListener('click', function () {
//                burger.classList.toggle('is-active');
//                nav.classList.toggle('is-active');
//            });
            window.Event.$on('login-modal', () => {
                this.showLoginModal = true;
            });
            window.Event.$on('signup-modal', () => {
                this.showSignUpModal = true;
            });
        },
        created() {
            this.csrf = window.token;
        },
        methods: {
            toggleLoginModal(event) {
                event.preventDefault();
                window.Event.$emit('login-modal');
            },
            toggleSignUpModal(event) {
                event.preventDefault();
                window.Event.$emit('signup-modal');
            },
            logout() {
                this.$refs.logoutForm.submit();
            }
        }
    }
</script>
<style scoped>


</style>