<template>
    <div class="card is-shadowless" style="width: 100%;">
        <div class="columns">
            <div class="column is-1">
                <div class="vote-buttons is-pulled-right">
                    <div>
                        <font-awesome-icon :style="upArrow" @click="upvote" icon="arrow-circle-up"/>
                        <span>
                            {{ comment.votesTotal }}
                        </span>
                        <font-awesome-icon :style="downArrow" @click="downvote" icon="arrow-circle-down"/>
                    </div>
                </div>
            </div>
            <div class="column is-11">
                <p>
                    <a :href="commentAuthorLink">{{comment.user.username}}</a>
                    <span class="points">
                {{ comment.votesTotal }} points
            </span>
                    <span class="datetime">
                {{ timeFromNow }}
            </span>
                </p>
                <p>
                    {{ comment.body }}
                </p>
                <div>
                    <span @click="toggleReplyForm" class="reply pointer"><font-awesome-icon size="xs"
                                                                                            icon="comment-alt"/> Reply</span>
                </div>
                <div class="column is-12 comments" v-if="this.user && this.reply">
                    <b-input type="textarea" size="is-small" rows="4" placeholder="What are your thoughts?"
                             v-model="replyBody"></b-input>
                    <button class="button is-primary is-small is-pulled-right" @click="commentReply">
                        Reply
                    </button>
                </div>
                <footer class="card-footer" v-if="comment.comments">
                    <div class="columns is-multiline comments">
                        <div class="column is-12">
                            <comment v-for="(comment, index) in comment.comments" :key="index"
                                     :content="comment" :style="recursiveComment"></comment>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        beforeCreate () {
            this.$options.components.Comment = require('./Comment.vue')
        },

        props: ['content'],
        data() {
            return {
                reply: false,
                replyBody: '',
                user: window.user,
                comment: null,
            };
        },
        computed: {
            commentAuthorLink() {
                return '/u/' + this.comment.user.username;
            },
            timeFromNow() {
                return moment(this.comment.created_at).fromNow();
            },
            recursiveComment() {
                return {
                    'margin-top': '1rem',
//                    'margin-left': '0.25rem'
                }
            },
            upArrow() {
                return {
                    color: (this.comment && this.comment.usersVote && this.comment.usersVote.value === 1) ? 'green' : ''
                }
            },
            downArrow() {
                return {
                    color: (this.comment && this.comment.usersVote && this.comment.usersVote.value === -1) ? 'red' : ''
                }
            }
        },
        created() {
            this.comment = this.content;
        },
        watch: {},
        methods: {
            toggleReplyForm() {
                this.replyBody = '';
                this.reply = !this.reply;
            },
            commentReply() {
                let self = this;
                axios.post('/comments/comment/' + this.comment.id, {
                    body: this.replyBody
                }).then((r) => {
                    self.comment.comments.unshift(r.data);
                    self.reply = false;
                    self.replyBody = '';
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
                if (this.comment.usersVote && this.comment.usersVote.value === 1) {
                    return;
                }
                let self = this;
                axios.patch('/comments/' + this.comment.id + '/vote', {
                    up: 1
                }).then((r) => {
                    if (!self.comment.usersVote || self.comment.usersVote.updated_at !== r.data.updated_at) {
                        self.comment.votesTotal += (self.comment.hasOwnProperty('usersVote') && self.comment.usersVote) ? 2 : 1;
                    }
                    self.comment.usersVote = r.data;
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
                if (this.comment.usersVote && this.comment.usersVote.value === -1) {
                    return;
                }
                let self = this;
                axios.patch('/comments/' + this.comment.id + '/vote').then((r) => {
                    if (!self.comment.usersVote || self.comment.usersVote.updated_at !== r.data.updated_at) {
                        self.comment.votesTotal -= (self.comment.hasOwnProperty('usersVote') && self.comment.usersVote) ? 2 : 1;
                    }
                    self.comment.usersVote = r.data;
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
        }
    }
</script>
<style scoped lang="scss">
    .reply {
        padding: 0.25rem;
        font-size: 0.75rem;

        &:hover {
            background-color: #b5b5b5 !important;
            color: white;
        }
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
        /*margin-left: -0.5rem;*/
    }

    .comments {
        width: 100%;
    }

    .card {
        border: 0;
        border-left: 2px solid rgb(237, 239, 241);
        margin-bottom: 1rem;
        padding-left: 0.25rem;
        padding-bottom: 0.5rem;

        .card-footer {
            border-top: 0;
        }
    }

    span.points {
        padding-left: 0.5rem;
    }

    span.datetime {
        padding-left: 1rem;
    }

    .red-icon {
        color: red;
    }

    .green-icon {
        color: green;
    }
</style>
