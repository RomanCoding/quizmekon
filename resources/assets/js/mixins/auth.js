let authMixin = {
    methods: {
        checkIfLogged() {
            let vm = this;
            return new Promise((resolve, reject) => {
                axios.get('/sessionStatus')
                    .then(response => {
                        window.user = response.data.user;
                        if (response.data.user && response.data.user.showFirstTime) {
                            this.$toast.open({
                                duration: 10000,
                                message: "Welcome, please confirm your registration by acknowledging the email we have sent to " + response.data.user.email,
                                position: 'is-top',
                                type: 'is-danger'
                            });
                        }
                        resolve(response.data.user);
                    })
                    .catch(error => {
                        window.user = null;
                        reject(error.response.data);
                    });
            });
        },
        out() {
            let vm = this;
            return new Promise((resolve, reject) => {
                axios.post('/logout')
                    .then(response => {
                        resolve(response.data.user);
                    })
                    .catch(error => {
                        reject(error.response.data);
                    });
            })
        },
    }
};

export default authMixin;