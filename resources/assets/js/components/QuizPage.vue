<template>
    <div>
        <site-header></site-header>
        <div class="columns is-centered" v-if="quiz">
            <div class="column is-6-desktop is-10-tablet is-12-mobile">
                <div class="columns is-multiline">
                    <div class="column is-1">
                        <div class="vote-buttons is-pulled-right">
                            <div>
                                <font-awesome-icon :style="upArrow" @click="upvote" icon="arrow-circle-up"/>
                                <span>
                            {{ quiz.votesTotal }}
                        </span>
                                <font-awesome-icon :style="downArrow" @click="downvote" icon="arrow-circle-down"/>
                            </div>
                        </div>
                    </div>
                    <div class="column is-11">
                        <header class="card-header is-mobile columns">
                            <div class="column is-6-desktop is-9-mobile">
                                <p class="card-header-title">
                                    <span class="posted-by"> Posted by u/{{quiz.user.username}} {{ timeFromNow }}</span>
                                    <span class="post-title"> {{ quiz.title }}</span>
                                </p>
                            </div>
                        </header>
                        <div class="card-content">
                            <h4 class="is-size-4" v-text="quiz.title"></h4>
                            <h4 class="is-size-4" v-text="quiz.question" v-if="showQuiz"></h4>
                            <div class="media" v-if="showVideo">
                                <youtube :video-id="quiz.video.youtube_id" :player-vars="playerVars" @ended="ended"
                                         ref="youtube" style="margin-top: 1rem;"/>
                            </div>
                            <div class="content" v-show="showQuiz">
                                <template v-if="!quiz.usersPoll">
                                    <div class="field" v-for="(a, key) in JSON.parse(quiz.answers)" v-if="showQuiz">
                                        <b-radio v-model="answer" :native-value="key">
                                            {{ a }}
                                        </b-radio>
                                    </div>
                                    <a class="button is-primary" @click="poll" v-if="answer !== null">Vote</a>
                                </template>
                                <template v-else>
                                    <div class="field" v-for="(a, key) in JSON.parse(quiz.answers)" v-if="showQuiz">
                                        <b-radio :style="pollCss(key)" disabled v-model="quiz.usersPoll.index"
                                                 :native-value="key" :type="pollStyle(key)">
                                            {{ a }}
                                        </b-radio>
                                    </div>
                                </template>

                            </div>
                        </div>
                        <footer class="card-footer">
                            <div class="columns is-multiline" v-if="this.quiz.comments">
                                <div class="column is-12 comments" v-if="this.$parent.user">
                                    <b-input type="textarea" size="is-small" rows="4"
                                             placeholder="What are your thoughts?"
                                             v-model="quizComment"></b-input>
                                    <button class="button is-primary is-small is-pulled-right" @click="commentQuiz">
                                        Comment
                                    </button>
                                </div>
                                <div class="column is-12">
                                    <comment v-for="(comment, index) in quiz.comments" :key="index"
                                             :content="comment"></comment>
                                </div>
                            </div>
                            <article class="message" v-else>
                                <div class="message-body">
                                    Comments will be available after answering the question.
                                </div>
                            </article>
                        </footer>
                    </div>
                    <div class="column is-12">
                        <b-pagination
                                :total="series.length"
                                per-page="1"
                                :current.sync="quizOrder"
                                order="is-centered"
                                :rounded="true">
                        </b-pagination>
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
                quiz: null,
                series: [],
                quizOrder: 1,
                stage: 1,
                quizComment: '',
                user: window.user,
                answer: null,
            };
        },
        created() {
            let self = this;
            axios.get('/quizzes/' + this.$route.params.quiz)
                .then((r) => {
                    if (r.data.quizzes) {
                        self.series = r.data.quizzes;
                    } else {
                        self.series.push(r.data);
                    }
                    self.quiz = self.series.find((s => s.id == self.$route.params.quiz)) || self.series[0];
                    self.quizOrder = self.quiz.order;
                });
        },
        mounted() {
        },
        computed: {
            showVideo() {
                if (this.quiz.usersPoll) {
                    return true;
                }
                switch (this.quiz.type) {
                    case 1:
                        return (this.stage === 1 || this.stage === 3);
                    case 2:
                        return this.stage === 2;
                    case 3:
                        return (this.stage === 1 || this.stage === 3)
                }
            },
            showQuiz() {
                if (this.quiz.usersPoll) {
                    return true;
                }
                switch (this.quiz.type) {
                    case 1:
                        return this.stage === 2;
                    case 2:
                        return this.stage === 1;
                    case 3:
                        return this.stage === 2;
                }
            },
            playerVars() {
                return {
                    autoplay: 0,
                    start: (this.quiz.video.pause_time && this.stage === 3) ? this.quiz.video.pause_time : this.quiz.video.start_time,
                    end: (this.quiz.video.pause_time && this.stage === 1 && !this.quiz.usersPoll) ? this.quiz.video.pause_time : this.quiz.video.end_time,
                    rel: 0,
                    controls: 0,
                    showinfo: 1,
                }
            },
            upArrow() {
                return {
                    color: (this.quiz && this.quiz.usersVote && this.quiz.usersVote.value === 1) ? 'green' : ''
                }
            },
            downArrow() {
                return {
                    color: (this.quiz && this.quiz.usersVote && this.quiz.usersVote.value === -1) ? 'red' : ''
                }
            },
            subredditUrl() {
                return this.quiz ? 'r/' + this.quiz.category.slug : '';
            },
            timeFromNow() {
                return moment(this.series[0].created_at).fromNow();
            },
        },
        watch: {
            quizOrder: function (newVal, oldVal) {
                this.stage = 1;
                let self = this;
                axios.get('/quizzes/' + this.series[newVal - 1].id + '?single=1')
                    .then((r) => {
                        self.quiz = r.data;
                    });
            }
        },
        methods: {
            pollStyle(key) {
                return (this.quiz.usersPoll.index === this.quiz.correct) ? 'is-success' : 'is-danger';
            },
            pollCss(key) {
                return {
                    opacity: 1,
                    color: this.quiz.correct === key ? 'green' : 'black'
                }
            },
            alert(message, duration = 5000) {
                this.$toast.open({
                    duration: duration,
                    message: message,
                    position: 'is-top',
                    type: 'is-danger'
                });
            },
            poll() {
                let self = this;
                axios.post('/polls/' + this.quiz.id, {
                    index: this.answer
                }).then(r => {
                    self.quiz.usersPoll = r.data.poll;
                    self.quiz.comments = r.data.comments;
                    self.quiz.correct = r.data.poll.correct;
                    switch (self.quiz.type) {
                        case 1:
                            self.stage = 3;
                            break;
                        case 2:
                            self.stage = 2;
                            break;
                        case 3:
                            self.stage = 3; // @todo to think
                            break;
                    }
                }).catch(e => {
                    switch (e.response.status) {
                        case 400:
                        case 401: {
                            self.alert(e.response.data.message);
                            break;
                        }
                        default: {
                            self.alert('Error');
                        }
                    }
                });
            },
            ended() {
                this.stage = 2;
                //this.showQuiz = true;
            },
            commentQuiz() {
                let self = this;
                axios.post('/comments/quiz/' + this.quiz.id, {
                    body: this.quizComment
                }).then((r) => {
                    self.quiz.comments.unshift(r.data);
                    self.quizComment = '';
                }).catch((e) => {
                    switch (e.response.status) {
                        case 400:
                        case 401: {
                            self.alert(e.response.data.message);
                            break;
                        }
                        default: {
                            self.alert('Error');
                        }
                    }
                });
            },
            upvote() {
                if (this.quiz.usersVote && this.quiz.usersVote.value === 1) {
                    return;
                }
                let self = this;
                axios.patch('/quizzes/' + this.quiz.id + '/vote', {
                    up: 1
                }).then((r) => {
                    if (!this.quiz.usersVote || this.quiz.usersVote.updated_at !== r.data.updated_at) {
                        this.quiz.votesTotal += (this.quiz.hasOwnProperty('usersVote') && this.quiz.usersVote) ? 2 : 1;
                    }
                    this.quiz.usersVote = r.data;
                }).catch((e) => {
                    switch (e.response.status) {
                        case 400:
                        case 401: {
                            self.alert(e.response.data.message);
                            break;
                        }
                        default: {
                            self.alert('Error');
                        }
                    }
                });
            },
            downvote() {
                let self = this;
                if (this.quiz.usersVote && this.quiz.usersVote.value === -1) {
                    return;
                }
                axios.patch('/quizzes/' + this.quiz.id + '/vote').then((r) => {
                    if (!this.quiz.usersVote || this.quiz.usersVote.updated_at !== r.data.updated_at) {
                        this.quiz.votesTotal -= (this.quiz.hasOwnProperty('usersVote') && this.quiz.usersVote) ? 2 : 1;
                    }
                    this.quiz.usersVote = r.data;
                }).catch((e) => {
                    switch (e.response.status) {
                        case 400:
                        case 401: {
                            self.alert(e.response.data.message);
                            break;
                        }
                        default: {
                            self.alert('Error');
                        }
                    }
                });
            },
        },
    }
</script>
<style scoped lang="scss">


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
