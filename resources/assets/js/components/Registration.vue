<template>
    <section class="hero is-success is-fullheight">
        <div class="hero-body">
            <div class="container has-text-centered">
                <div class="columns is-centered">
                    <div class="column is-6-tablet is-4">
                        <h3 class="title has-text-grey">Register</h3>
                        <p class="subtitle has-text-grey">Please fill the form.</p>
                        <div class="box">
                            <form method="POST" action="/register" ref="form">
                                <input type="hidden" name="_token" :value="csrf">
                                <b-field label="Email"
                                         :type="inputType('email')"
                                         style="text-align: left;"
                                         :message="messages.email">
                                    <b-input size="is-medium" type="email"
                                             v-model="email"
                                             name="email"
                                             maxlength="254"
                                             autocomplete="off">
                                    </b-input>
                                </b-field>

                                <b-field label="Username"
                                         :type="inputType('username')"
                                         style="text-align: left;"
                                         :message="messages.username">
                                    <b-input size="is-medium" v-model="username" name="username" minlength="5" maxlength="40"></b-input>
                                </b-field>

                                <b-field label="Password"
                                         :type="inputType('password')"
                                         style="text-align: left;"
                                         :message="messages.password">
                                    <b-input size="is-medium" type="password"
                                             v-model="password"
                                             name="password"
                                             minlength="8" maxlength="50">
                                    </b-input>
                                </b-field>

                                <b-field label="Repeat password"
                                         :type="inputType('password_confirmation')"
                                         style="text-align: left;"
                                         :message="messages.password_confirmation">
                                    <b-input size="is-medium" type="password"
                                             name="password_confirmation"
                                             v-model="password_confirmation">
                                    </b-input>
                                </b-field>
                                <div class="notification is-danger" v-if="this.errors.length">
                                    <p v-for="error in errors" v-text="error"></p>
                                </div>
                                <button class="button is-block is-info is-large is-fullwidth" @click.prevent="signUp">Sign Up</button>
                            </form>
                            <p class="has-text-grey">
                                <a href="/register">Sign Up</a> &nbsp;·&nbsp;
                                <a href="../">Forgot Password</a> &nbsp;·&nbsp;
                                <a href="../">Need Help?</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
    export default {
        props: ['errors'],
        data() {
            return {
                email: '',
                username: '',
                csrf: '',
                password: '',
                password_confirmation: '',

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
        created() {
            this.csrf = window.token;
        },
        computed: {},
        watch: {
            email: function (value) {
                this.email = value;
                if (this.validateEmail(value)) {
                    this.validation.email = true;
                    this.messages.email = 'Email is OK.';
                    // @todo check unique email
                } else {
                    this.validation.email = false;
                    this.messages.email = 'Invalid email!';
                }
            },
            username: function (value) {
                this.username = value;
                if (value.length < 5) {
                    this.validation.username = false;
                    this.messages.username = 'Minimum 5 characters!';
                }
                else if (this.validateUsername(value)) {
                    this.validation.username = true;
                    this.messages.username = 'Username is OK.';
                    // @todo check unique username
                } else {
                    this.validation.username = false;
                    this.messages.username = 'Username can only contain letters and numbers!';
                }
            },
            password: function (value) {
                this.password = value;
                if (this.validatePassword(value)) {
                    this.validation.password = true;
                    this.messages.password = 'Password is OK.';
                } else {
                    this.validation.password = false;
                    this.messages.password = 'Minimum 8 characters, at least one letter and one number.';
                }
            },
            password_confirmation: function (value) {
                this.password_confirmation = value;
                if (this.password_confirmation === this.password) {
                    this.validation.password_confirmation = true;
                    this.messages.password_confirmation = 'Fine!';
                } else {
                    this.validation.password_confirmation = false;
                    this.messages.password_confirmation = 'Passwords are different!';
                }
            },
        },
        methods: {
            validateEmail(email) {
                let re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                return re.test(String(email).toLowerCase());
            },
            validateUsername(value) {
                let re = /^[a-zA-Z0-9.\-]{5,40}$/;
                return re.test(String(value).toLowerCase());
            },
            validatePassword(value) {
                let re = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/;
                return re.test(String(value).toLowerCase());
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
            signUp() {
                let self = this;
                if (this.validation['email'] && this.validation['password']
                && this.validation['username'] && this.validation['password_confirmation']) {
                    alert('Signing up!');
                    this.$refs['form'].submit();
                } else {
                    alert('Make correct input');
                }
            }

        }
    }
</script>
<style scoped>
    html,body {
        font-family: 'Open Sans', serif;
        font-size: 14px;
        font-weight: 300;
    }
    .hero.is-success {
        background: #F2F6FA;
    }
    .hero.is-halfheight .hero-body, .hero.is-fullheight .hero-body {
        align-items: flex-start;
    }

    .hero.is-fullheight {
        min-height: 80vh;
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