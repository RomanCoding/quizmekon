<template>

    <section class="hero is-success is-fullheight">
        <div class="hero-body">
            <div class="container has-text-centered">
                <div class="columns is-centered">
                    <div class="column is-6-tablet is-4">
                        <h3 class="title has-text-grey">Login</h3>
                        <p class="subtitle has-text-grey">Please login to proceed.</p>
                        <div class="box">
                            <form method="POST" action="/login" ref="form">
                                <input type="hidden" name="_token" :value="csrf">
                                <b-field label="Email"
                                         style="text-align: left;"
                                         :type="inputType('email')"
                                         :message="messages.email">
                                    <b-input size="is-medium" type="email"
                                             name="email"
                                             v-model="email"
                                             maxlength="254"
                                             autocomplete="off">
                                    </b-input>
                                </b-field>

                                <b-field label="Password"
                                         style="text-align: left;"
                                         :type="inputType('password')"
                                         :message="messages.password">
                                    <b-input size="is-medium" type="password"
                                             v-model="password"
                                             name="password"
                                             minlength="8" maxlength="50">
                                    </b-input>
                                </b-field>
                                <div class="notification is-danger" v-if="this.errors && this.errors.length">
                                    <p v-for="error in errors" v-text="error"></p>
                                </div>
                                <button class="button is-block is-info is-large is-fullwidth" @click.prevent="login">
                                    Login
                                </button>
                            </form>
                        </div>
                        <p class="has-text-grey">
                            <a href="/register">Sign Up</a> &nbsp;·&nbsp;
                            <a href="../">Forgot Password</a> &nbsp;·&nbsp;
                            <a href="../">Need Help?</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
    import authMixin from '../mixins/auth'
    export default {
        props: ['errors'],
        mixins: [authMixin],
        data() {
            return {
                email: '',
                password: '',
                csrf: '',

                validation: {
                    email: null,
                    username: null,
                    password: null,
                    password_confirmation: null
                },
                messages: {
                    email: null,
                    username: null,
                    password: null,
                    password_confirmation: null
                }
            }
        },
        computed: {},
        created() {
            this.csrf = window.token;
        },
        watch: {
            email: function (value) {
                this.email = value;
                if (this.validateEmail(value)) {
                    this.validation.email = true;
                    this.messages.email = '';
                    // @todo check unique email
                } else {
                    this.validation.email = false;
                    this.messages.email = 'Invalid email!';
                }
            },
            password: function (value) {
                this.password = value;
                if (this.validatePassword(value)) {
                    this.validation.password = true;
                    this.messages.password = '';
                } else {
                    this.validation.password = false;
                    this.messages.password = 'Password is too short';
                }
            },
        },

        methods: {
            validateEmail(email) {
                let re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                return re.test(String(email).toLowerCase());
            },
            validatePassword(value) {
                return value.length > 7;
            },
            inputType(name) {
                switch (this.validation[name]) {
                    case null:
                        return '';
                    case true:
                        return 'is-success';
                    case false:
                        return 'is-danger';
                }
            },
            validateLogin() {
                return (this.email && this.email.length <= 254 )
            },
            login() {
                let self = this;
                if (this.validation['email'] && this.validation['password']) {
                    alert('Logging in!');
                    this.$refs['form'].submit();
                } else {
                    alert('Make correct input');
                }
            }
        }
    }
</script>
<style scoped>
    html, body {
        font-family: 'Open Sans', serif;
        font-size: 14px;
        font-weight: 300;
    }

    .hero.is-halfheight .hero-body, .hero.is-fullheight .hero-body {
        align-items: flex-start;
    }

    .hero.is-fullheight {
        min-height: 80vh;
    }

    .hero.is-success {
        background: #F2F6FA;
    }

    .hero .nav, .hero.is-success .nav {
        -webkit-box-shadow: none;
        box-shadow: none;
    }

    .box {
        margin-top: 1rem;
    }

    input {
        font-weight: 300;
    }

    p {
        font-weight: 700;
    }

    p.subtitle {
        padding-top: 1rem;
    }

    div.field:not(:last-of-type) {
        margin-bottom: 0;
    }

    .button-block {
        margin-top: 1rem;
    }
</style>