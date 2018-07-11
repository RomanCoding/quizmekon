<template>
    <div class="columns is-centered">
        <div class="column is-6">
            <b-field>
                <b-select placeholder="Choose a category" rounded v-model="category">
                    <option :value="category.id" v-for="category in categories" v-text="category.name"></option>
                </b-select>
            </b-field>
            <b-tabs type="is-toggle" v-model="type" expanded>
                <b-tab-item label="Post" icon="book-open"></b-tab-item>
                <b-tab-item label="Link" icon="link"></b-tab-item>
                <b-tab-item label="YouTube video" icon="video"></b-tab-item>
            </b-tabs>
            <section v-if="type == 0">
                <b-field label="Title">
                    <b-input placeholder="Title"
                             maxlength="255"
                             v-model="title">
                    </b-input>
                </b-field>

                <b-field label="Body">
                    <b-input type="textarea"
                             placeholder="What you want to say?"
                             v-model="body">
                    </b-input>
                </b-field>
                <a class="button is-info is-rounded" @click="submitPost">Submit</a>
            </section>
            <section v-if="type == 2">
                <b-field>
                    <b-input placeholder="Title"
                             maxlength="255"
                             v-model="title">
                    </b-input>
                </b-field>

                <b-field label="YouTube link"
                         :type="youtubeField"
                         :message="youtubeMessage">
                    <b-input type="url"
                             v-model="youtube">
                    </b-input>
                </b-field>
                <a class="button is-info is-rounded" @click="submitYoutube">Submit</a>
            </section>
        </div>
    </div>

</template>

<script>
    export default {
        data() {
            return {
                categories: [],
                category: null,
                youtube: null,
                title: null,
                body: null,
                type: 0
            };
        },
        created() {
            let self = this;
            axios.get('/categories').then(r => self.categories = r.data);
        },
        computed: {
            youtubeField() {
                return (this.youtube && this.validateYoutube(this.youtube)) ? 'is-success' : 'is-danger'
            },
            youtubeMessage() {
                return (this.youtube && this.validateYoutube(this.youtube)) ? 'Nice video!' : 'Wrong link...'
            }
        },
        watch: {

        },
        methods: {
            validateYoutube(val) {
                return /^(http(s)?:\/\/)?((w){3}.)?youtu(be|.be)?(\.com)?\/.+/.test(val);
            },
            submitPost() {
                if (!this.category) {
                    this.$toast.open({
                        duration: 3000,
                        message: 'Choose category!',
                        position: 'is-top',
                        type: 'is-danger'
                    });
                    return false;
                }
                let self = this;
                axios.post('/posts', {
                    'type': 'post',
                    'body': this.body,
                    'title': this.title,
                    'category_id': this.category,
                }).then(r => {
                    self.$router.push({path: '/r/'+r.data.category.slug+'/' + r.data.id});
                }).catch(e => {
                    switch (e.response.status) {
                        case 400:
                        case 401: 
                        case 422: {
                            self.$toast.open({
                                duration: 5000,
                                message: e.response.data.message,
                                position: 'is-top',
                                type: 'is-danger'
                            });
                            break;
                        }
                        default: {
                            alert('Error');
                        }
                    }
                });
            },
            submitYoutube() {
                if (!this.category) {
                    this.$toast.open({
                        duration: 3000,
                        message: 'Choose category!',
                        position: 'is-top',
                        type: 'is-danger'
                    });
                    return false;
                }
                let self = this;
                axios.post('/posts', {
                    'type': 'youtube',
                    'link': this.youtube,
                    'title': this.title,
                    'category_id': this.category,
                }).then(r => {
                    self.$router.push({path: '/r/'+r.data.category.slug+'/' + r.data.id});
                }).catch(e => {
                    switch (e.response.status) {
                        case 400:
                        case 401:
                        case 422: {
                            self.$toast.open({
                                duration: 5000,
                                message: e.response.data.message,
                                position: 'is-top',
                                type: 'is-danger'
                            });
                            break;
                        }
                        default: {
                            alert('Error');
                        }
                    }
                });
            }
        },
    }
</script>
<style scoped lang="scss">

</style>
