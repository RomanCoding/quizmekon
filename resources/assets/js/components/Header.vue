<template>
    <nav class="navbar">
        <b-modal :active.sync="showLoginModal" has-modal-card>
            <login></login>
        </b-modal>
        <b-modal :active.sync="showSignUpModal" has-modal-card>
            <registration></registration>
        </b-modal>
        <div class="container">
            <div class="navbar-brand">
                <a class="navbar-item" href="/">
                    Meddit
                </a>
                <span class="navbar-burger burger" data-target="navMenu">
            <span></span>
            <span></span>
            <span></span>
          </span>
            </div>
            <div id="navMenu" class="navbar-menu">
                <div class="navbar-end">
                    <template v-if="this.$parent.user === false">
                        <a class="navbar-item" @click="toggleLoginModal">Login</a>
                        <a class="navbar-item" @click="toggleSignUpModal">Register</a>
                    </template>
                    <template v-else-if="this.$parent.user">
                        <b-dropdown>
                            <a class="navbar-item" slot="trigger">
                                {{ this.$parent.user.username }}
                                <b-icon icon="menu-down"></b-icon>
                            </a>

                            <b-dropdown-item>Preferences</b-dropdown-item>
                            <b-dropdown-item @click="createPost">Add new</b-dropdown-item>
                            <b-dropdown-item @click="logout">Logout</b-dropdown-item>
                        </b-dropdown>
                        <form action="/logout" method="POST" style="display: none;" ref="logoutForm">
                            <input type="hidden" name="_token" :value="csrf">
                        </form>
                    </template>
                </div>
            </div>
        </div>
    </nav>
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
            let burger = document.querySelector('.burger');
            let nav = document.querySelector('#' + burger.dataset.target);
            burger.addEventListener('click', function () {
                burger.classList.toggle('is-active');
                nav.classList.toggle('is-active');
            });
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
            createPost() {
                this.$router.push({path: '/submit'});
            },
            logout() {
                this.$refs.logoutForm.submit();
            }
        }
    }
</script>
<style scoped>

</style>