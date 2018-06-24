let authMixin = {
    methods: {
        checkIfLogged() {
            let vm = this;
            return new Promise((resolve, reject) => {
                axios.get('/sessionStatus')
                    .then(response => {
                        resolve(response.data.user);
                    })
                    .catch(error => {
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