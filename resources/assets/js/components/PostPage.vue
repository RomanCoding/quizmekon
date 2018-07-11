<template>
    <div>
        <div class="columns is-centered">
            <div class="column is-6-desktop is-10-tablet is-12-mobile" v-if="post">
                <div class="columns">
                    <div class="column is-1">
                        <div class="vote-buttons is-pulled-right">
                            <div>
                                <font-awesome-icon :style="upArrow" @click="upvote" icon="arrow-circle-up"/>
                                <span>
                            {{ post.votesTotal }}
                        </span>
                                <font-awesome-icon :style="downArrow" @click="downvote" icon="arrow-circle-down"/>
                            </div>
                        </div>
                    </div>
                    <div class="column is-11">
                        <header class="card-header is-mobile columns">
                            <div class="column is-6-desktop is-9-mobile">
                                <p class="card-header-title">
                                    <span class="posted-by"> Posted by u/{{post.user.username}} {{ timeFromNow }}</span>
                                    <span class="post-title"> {{ post.title }}</span>
                                </p>
                            </div>
                        </header>
                        <div class="card-content">
                            <template v-if="post.type === 0">
                                {{ post.body }}
                            </template>
                            <div class="videoWrapper" v-if="post.type === 2">
                                <iframe width="560" height="349" :src="youtubeLink" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
                            </div>
                        </div>
                        <footer class="card-footer">
                            <div class="columns is-multiline">
                                <div class="column is-12 comments" v-if="this.$parent.user">
                                    <b-input type="textarea" size="is-small" rows="4" placeholder="What are your thoughts?"
                                             v-model="postComment"></b-input>
                                    <button class="button is-primary is-small is-pulled-right" @click="commentPost">
                                        Comment
                                    </button>
                                </div>
                                <div class="column is-12">
                                    <comment v-for="(comment, index) in post.comments" :key="index"
                                             :content="comment"></comment>
                                </div>
                            </div>
                            <!--<a href="#" class="card-footer-item">Save</a>-->
                            <!--<a href="#" class="card-footer-item">Edit</a>-->
                            <!--<a href="#" class="card-footer-item">Delete</a>-->
                        </footer>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Comment from './Comment.vue'
    import Login from './modals/Login.vue'
    export default {
        components: {
            'comment': Comment,
            'login': Login,
        },
        props: ['id'],
        data() {
            return {
                post: null,
                postComment: '',
                user: window.user,
            };
        },
        created() {
            let self = this;
            axios.get('/posts/' + this.$route.params.post)
                .then((r) => {
                    self.post = r.data;
                });
        },
        mounted() {
        },
        computed: {
            upArrow() {
                return {
                    color: (this.post && this.post.usersVote && this.post.usersVote.value === 1) ? 'green' : ''
                }
            },
            downArrow() {
                return {
                    color: (this.post && this.post.usersVote && this.post.usersVote.value === -1) ? 'red' : ''
                }
            },
            subredditUrl() {
                return this.post ? 'r/' + this.post.category.slug : '';
            },
            timeFromNow() {
                return moment(this.post.created_at).fromNow();
            },
            youtubeLink() {
                return this.post.type === 2 ? 'https://www.youtube.com/embed/' + this.post.link + '?rel=0' : '';
            }
        },
        watch: {},
        methods: {
            commentPost() {
                let self = this;
                axios.post('/comments/post/' + this.post.id, {
                    body: this.postComment
                }).then((r) => {
                    self.post.comments.unshift(r.data);
                    self.postComment = '';
                }).catch((e) => {
                    switch (e.response.status) {
                        case 400:
                        case 401: {
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
            upvote() {
                if (this.post.usersVote && this.post.usersVote.value === 1) {
                    return;
                }
                let self = this;
                axios.patch('/posts/' + this.post.id + '/vote', {
                    up: 1
                }).then((r) => {
                    if (!this.post.usersVote || this.post.usersVote.updated_at !== r.data.updated_at) {
                        this.post.votesTotal += (this.post.hasOwnProperty('usersVote') && this.post.usersVote) ? 2 : 1;
                    }
                    this.post.usersVote = r.data;
                }).catch((e) => {
                    switch (e.response.status) {
                        case 401: {
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
            downvote() {
                let self = this;
                if (this.post.usersVote && this.post.usersVote.value === -1) {
                    return;
                }
                axios.patch('/posts/' + this.post.id + '/vote').then((r) => {
                    if (!this.post.usersVote || this.post.usersVote.updated_at !== r.data.updated_at) {
                        this.post.votesTotal -= (this.post.hasOwnProperty('usersVote') && this.post.usersVote) ? 2 : 1;
                    }
                    this.post.usersVote = r.data;
                }).catch((e) => {
                    switch (e.response.status) {
                        case 401: {
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
        },
    }
</script>
<style scoped lang="scss">
    .videoWrapper {
        position: relative;
        padding-bottom: 56.25%; /* 16:9 */
        padding-top: 25px;
        height: 0;
    }
    iframe {
        position: absolute;
        left: 10%;
        width: 80%;
        height: calc(100%/1.1);
    }
    .vote-buttons div {
        padding-left: 2px;
        padding-right: 2px;
        display: flex;
        flex-direction: column;
        text-align: center;

        span {
            font-size: 80%;
        }
    }

    .card-content {
        padding: 0 0 1rem;
    }

    .card-header-title {
        padding-top: 0.25rem;
        padding-bottom: 0.25rem;
        padding-left: 0.25rem;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }

    .posted-by {
        font-size: 70%;
        color: grey;
    }
    .card-footer {
        padding-top: 1rem;

        .columns {
            width: 100%;
        }
        .button {
            margin-top: 0.25rem;
        }
    }
    .comments {
        margin-bottom: 0.5rem;
    }
</style>
