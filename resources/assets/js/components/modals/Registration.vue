<template>
    <div class="modal-card" style="width: auto">
        <header class="modal-card-head">
            <p class="modal-card-title">Registration</p>
        </header>
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
                         :message="messages.username"
                         v-if="step > 1">
                    <b-input size="is-medium" v-model="username" name="username" minlength="5" maxlength="40"></b-input>
                </b-field>

                <b-field label="Password"
                         :type="inputType('password')"
                         style="text-align: left;"
                         :message="messages.password"
                         v-if="step > 2">
                    <b-input size="is-medium" type="password"
                             v-model="password"
                             name="password"
                             minlength="8" maxlength="50">
                    </b-input>
                </b-field>

                <b-field label="Repeat password"
                         :type="inputType('password_confirmation')"
                         style="text-align: left;"
                         :message="messages.password_confirmation"
                         v-if="step > 2">
                    <b-input size="is-medium" type="password"
                             name="password_confirmation"
                             v-model="password_confirmation">
                    </b-input>
                </b-field>
                <div class="notification is-danger" v-if="errors && errors.length">
                    <p v-for="error in errors" v-text="error"></p>
                </div>
                <button class="button is-block is-info is-large is-fullwidth" @click.prevent="process"
                        v-text="step == 3 ? 'Sign Up' : 'Next'"></button>
            </form>
        </div>
    </div>
</template>
<script>
    export default {
        props: ['errors'],
        data() {
            return {
                step: 1,
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
        watch: {},
        methods: {
            process() {
                switch (this.step) {
                    case 1: {
                        if (this.validateEmail()) {
                            let self = this;
                            axios.post('/register/email', {
                                email: this.email
                            }).then(r => {
                                self.validation.email = true;
                                self.messages.email = 'Email is OK.';
                                self.step++;
                            }).catch(e => {
                                self.validation.email = false;
                                self.messages.email = 'This email is already taken';
                            });
                        } else {
                            this.validation.email = false;
                            this.messages.email = 'Invalid email!';
                        }
                        break;
                    }
                    case 2: {
                        if (this.validateUsername()) {
                            let self = this;
                            axios.post('/register/username', {
                                username: this.username
                            }).then(r => {
                                self.validation.username = true;
                                self.messages.username = 'Username is OK.';
                                self.step++;
                            }).catch(e => {
                                self.validation.username = false;
                                self.messages.username = 'This username is already taken';
                            });
                        } else {
                            this.validation.username = false;
                            this.messages.username = 'Invalid username!';
                        }
                        break;
                    }
                    case 3: {
                        if (!this.validatePassword()) {
                            this.validation.password = false;
                            this.messages.password = 'Minimum 8 characters, at least one letter and one number.';
                        }
                        else if (this.password_confirmation !== this.password) {
                            this.validation.password = true;
                            this.validation.password_confirmation = false;
                            this.messages.password_confirmation = 'Passwords are different!';
                        }
                        else {
                            this.$refs['form'].submit();
                        }
                        break;
                    }
                }
            },
            validateEmail() {
                let re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                return re.test(String(this.email).toLowerCase());
            },
            validateUsername() {
                let re = /^[a-zA-Z0-9.\-]{5,40}$/;
                return re.test(String(this.username).toLowerCase());
            },
            validatePassword() {
                let re = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/;
                return re.test(String(this.password).toLowerCase());
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

        }
    }
</script>