<template>
    <div class="columns">
        <div class="column is-8-desktop is-10-tablet is-12-mobile">

            <p class="panel-tabs">
                <a :class="{'is-active': sort == 'new'}" @click="changeSort('new')">new</a>
                <a :class="{'is-active': sort == 'hot'}" @click="changeSort('hot')">hot</a>
                <a :class="{'is-active': sort == 'voted'}" @click="changeSort('voted')">most voted</a>
            </p>
            <quiz v-for="(quiz, index) in quizzes" @upvoteQuiz="upvoteQuiz"
                  @downvoteQuiz="downvoteQuiz"
                  :key="index" :content="quiz" @expanded="visitQuizPage(quiz)">
            </quiz>
        </div>
        <div class="column is-4-desktop is-2-tablet is-12-mobile" v-if="filteredByChannel">
            <article class="message is-info">
                <div class="message-header">
                    <p v-text="this.$route.params.subreddit"></p>
                </div>
                <div class="message-body">
                    You are browsing <b>{{ $route.params.subreddit }}</b> channel.
                    <br>
                    If you want to go back, <a href="#" @click.prevent="toHomepage">click here.</a>
                </div>
            </article>
        </div>
    </div>


</template>

<script>
    import Quiz from './Quiz.vue'
    export default {
        components: {
            'quiz': Quiz,
        },
        data() {
            return {
                sort: 'new',
                quizzes: [],
                page: 1,
                expandedQuiz: null,
                isComponentModalActive: false,
            };
        },
        created() {
            this.changeSort(this.$route.params.sort || this.sort);
            this.loadMore();
            let self = this;
            $(window).on('scroll', function () {
                if ($(window).scrollTop() > $(document).height() - $(window).height() - 1) {
                    self.loadMore();
                }
            });


        },
        computed: {
            filteredByChannel() {
                return this.$route.params.subreddit;
            }
        },
        watch: {
            'sort': function () {
                this.page = 1;
                this.loadMore(true);
            },
            '$route.params.sort': function (to, from) {
                this.sort = to;
            },
            '$route.params.subreddit': function (to, from) {
                this.page = 1;
                this.loadMore(true);
            }
        },
        methods: {
            changeSort(sort) {
                this.$router.push({ params: Object.assign({}, this.$route.params, { sort: sort }) });
                this.sort = sort;
            },
            loadMore(replace = false) {
                let self = this;
                axios.get('/quizzes', {
                    params: {
                        s: this.sort,
                        page: this.page,
                        category: this.$route.params.subreddit
                    }
                }).then((r) => {
                    self.quizzes = replace ? r.data.data : self.quizzes.concat(r.data.data || []);
                    if (r.data.data.length) {
                        self.page++;
                    }
                });
            },
            visitQuizPage(quiz) {
                this.$router.push({path: '/r/' + quiz.category.slug + '/' + quiz.id});
            },
            toHomepage() {
                this.$router.push({path: '/' + (this.$route.params.sort || 'new')});
            },
            upvoteQuiz(quiz) {
                if (quiz.usersVote && quiz.usersVote.value === 1) {
                    return;
                }
                let self = this;
                axios.patch('/quizzes/' + quiz.id + '/vote', {
                    up: 1
                }).then((r) => {
                    let quizIndex = self.quizzes.findIndex((q) => q.id === quiz.id);
                    if (!quiz.usersVote || quiz.usersVote.updated_at !== r.data.updated_at) {
                        quiz.votesTotal += (quiz.hasOwnProperty('usersVote') && quiz.usersVote) ? 2 : 1;
                    }
                    quiz.usersVote = r.data;
                    if (quizIndex > -1) {
                        self.quizzes[quizIndex] = quiz;
                    }
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
            downvoteQuiz(quiz) {
                let self = this;
                if (quiz.usersVote && quiz.usersVote.value === -1) {
                    return;
                }
                axios.patch('/quizzes/' + quiz.id + '/vote').then((r) => {
                    let quizIndex = self.quizzes.findIndex((p) => p.id === quiz.id);
                    if (!quiz.usersVote || quiz.usersVote.updated_at !== r.data.updated_at) {
                        quiz.votesTotal -= (quiz.hasOwnProperty('usersVote') && quiz.usersVote) ? 2 : 1;
                    }
                    quiz.usersVote = r.data;
                    if (quizIndex > -1) {
                        self.quizzes[quizIndex] = quiz;
                    }
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
<style scoped>
    .panel-tabs {
        justify-content: start;
        margin-bottom: 1px;
    }

    .card {
        margin-bottom: 2rem;
    }
</style>
