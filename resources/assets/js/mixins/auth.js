let authMixin = {
    methods: {
        checkIfLogged() {
            let vm = this;
            return new Promise((resolve, reject) => {
                axios.get('/sessionStatus')
                    .then(response => {
                        window.user = response.data.user;
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