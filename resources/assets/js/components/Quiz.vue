<template>
    <div class="card" v-if="!this.expand">
        <header class="card-header is-mobile columns is-gapless">
            <div class="column is-three-fifths-mobile is-two-thirds-tablet">
                <p class="card-header-title is-paddingless" @click="$emit('expanded')">
                    <img :src="thumbnail" alt="Thumbnail">
                    {{ quiz.question }}
                </p>
            </div>
            <div class="column is-gapless">
                <div class="vote-buttons is-pulled-right">
                    <span class="tag is-light pointer" v-text="quiz.category.name" @click="visitCategory"></span>

                    <div class="is-pulled-right">
                        <font-awesome-icon :style="upArrow" @click="upvote" icon="arrow-circle-up"/>
                        <span>
                            {{ quiz.votesTotal }}
                        </span>
                        <font-awesome-icon :style="downArrow" @click="downvote" icon="arrow-circle-down"/>
                    </div>
                </div>
            </div>
        </header>
        <footer class="card-footer">
            <span @click="$emit('expanded')"><font-awesome-icon size="xs" icon="comment-alt"/> {{ quiz.comments_count || 0 }} comments</span>
            <span title="Report" style="margin-left: auto;">
                <font-awesome-icon icon="flag"/> Report
            </span>
            <span class="posted-by" style="cursor: default;"> Posted by u/{{quiz.user.username}} {{ timeFromNow }}</span>
        </footer>
    </div>
</template>

<script>
    import authMixin from '../mixins/auth'
    export default {
        mixins: [authMixin],
        props: ['content'],
        data() {
            return {
                expand: false,
                quiz: null,
            };
        },
        computed: {
            thumbnail() {
                return 'https://i.ytimg.com/vi/' + this.quiz.video.youtube_id + '/default.jpg';
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
            timeFromNow() {
                return moment(this.quiz.created_at).fromNow();
            },
        },
        created() {
            this.quiz = this.content;
        },
        watch: {
            content: function (newVal) {
                this.quiz = newVal;
            }
        },
        methods: {
            upvote() {
                this.$emit('upvoteQuiz', this.quiz);
            },
            downvote() {
                this.$emit('downvoteQuiz', this.quiz);
            },
            visitCategory() {
                this.$router.push({ path: '/r/' + this.quiz.category.slug + '/' + (this.$route.params.sort || 'new')});
            }
        }
    }
</script>
<style scoped lang="scss">
    .columns.is-gapless:not(:last-child) {
        margin-bottom: 0.5rem;
    }
    .card-header {
        margin-bottom: 0.5rem;

        .card-header-title img {
            max-height: 50px;
            margin-right: 0.25rem;
        }
    }
    .card-footer {
        border: 0;
        padding-left: 0.75rem;
        justify-content: space-between;
        span {
            padding: 0.25rem;
            font-size: 0.75rem;
            cursor: pointer;

            &:not(.posted-by):hover {
                background-color: #b5b5b5 !important;
                color: white;
            }
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

    .red-icon {
        color: red;
    }

    .green-icon {
        color: green;
    }
</style>
