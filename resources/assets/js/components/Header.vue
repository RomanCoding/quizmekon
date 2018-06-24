<template>
    <nav class="navbar">
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
                        <a class="navbar-item" href="/login">Login</a>
                        <a class="navbar-item" href="/register">Register</a>
                    </template>
                    <template v-else-if="this.$parent.user">
                        <b-dropdown>
                            <a class="navbar-item" slot="trigger">
                                {{ this.$parent.user.username }}
                                <b-icon icon="menu-down"></b-icon>
                            </a>

                            <b-dropdown-item>Preferences</b-dropdown-item>
                            <b-dropdown-item>Add new</b-dropdown-item>
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
    export default {
        mixins: [authMixin],
        data() {
            return {
                csrf: '',
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
        },
        created() {
            this.csrf = window.token;
        },
        methods: {
            logout() {
                this.$refs.logoutForm.submit();
            }
        }
    }
</script>
<style scoped>

</style>