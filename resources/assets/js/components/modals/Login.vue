<template>
    <form action="/login" ref="form" method="POST">
        <div class="modal-card" style="width: auto">
            <header class="modal-card-head">
                <p class="modal-card-title">Login</p>
            </header>
            <section class="modal-card-body">
                <input type="hidden" name="_token" :value="csrf">
                <input type="hidden" name="redirect_url" :value="currentUrl">
                <b-field label="Email">
                    <b-input
                            type="email"
                            v-model="email"
                            name="email"
                            placeholder="Your email"
                            required>
                    </b-input>
                </b-field>

                <b-field label="Password">
                    <b-input
                            type="password"
                            v-model="password"
                            name="password"
                            password-reveal
                            placeholder="Your password"
                            required>
                    </b-input>
                </b-field>

                <b-checkbox>Remember me</b-checkbox>
            </section>
            <footer class="modal-card-foot">
                <button class="button" type="button" @click="$parent.close()">Close</button>
                <button class="button is-primary" @click.prevent="login">Login</button>
            </footer>
        </div>
    </form>
</template>
<script>
    export default {
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
        computed: {
            currentUrl() {
                return window.location.href;
            }
        },
        created() {
            this.csrf = window.token;
        },
        watch: {
            email: function (value) {
                this.email = value;
                if (this.validateEmail(value)) {
                    this.validation.email = true;
                    this.messages.email = '';
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
                    axios.post('/login/check', {
                        email: this.email,
                        password: this.password
                    }).then(r => {
                        if (r.data.result === 'success') {
                            self.$refs['form'].submit();
                        } else {
                            self.$toast.open({
                                duration: 5000,
                                message: 'Wrong credentials',
                                position: 'is-top',
                                type: 'is-danger'
                            });
                        }
                    }).catch(e => {
                        self.$toast.open({
                            duration: 5000,
                            message: 'Error occured...',
                            position: 'is-top',
                            type: 'is-danger'
                        });
                    });
                } else {
                    self.$toast.open({
                        duration: 5000,
                        message: 'Make correct input!',
                        position: 'is-top',
                        type: 'is-danger'
                    });
                }
            }
        }
    }
</script>