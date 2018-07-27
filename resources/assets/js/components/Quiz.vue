<template>
    <div class="postbox">
        <article class="media">
            <div class="votebox">
                <span class="upvote" style="font-size: 2rem;" @click="upvote" :style="upArrow">
                    <i class="fas fa-caret-up"></i>
                </span>
                <span class="is-size-7 votes" v-text="quiz.votesTotal"></span>
                <span class="downvote" style="font-size: 2rem;" @click="downvote" :style="downArrow">
                    <i class="fas fa-caret-down"></i>
                </span>
            </div>
            <figure class="media-left">
                <p class="thumb_box">
                    <img :src="thumbnail" @click="$emit('expanded')">
                </p>
            </figure>
            <div class="media-content">
                <div class="content">
                    <p>
                        <strong class="is-size-5" v-text="quiz.question" @click="$emit('expanded')"></strong>
                        <br>
                        <span class="is-size-7 has-text-grey-light"> posted to: </span>
                        <span class="has-text-weight-bold pointer" v-text="quiz.category.name" @click="visitCategory"></span>
                        <span class="is-size-7 has-text-grey-light">, posted by :</span>
                        <span class="has-text-weight-bold" v-text="quiz.user.username"></span>
                        <span class="is-size-7 has-text-grey-light" v-text="timeFromNow"></span>
                    </p>
                </div>
                <nav class="level is-mobile">
                    <div class="level-left">
                        <a class="level-item">
                  <span class="icon is-small">
                    <i class="fas fa-heart"></i>
                  </span>
                            <span class="is-size-7 has-text-grey-light">&nbsp, Save to favorites </span>
                        </a>
                        <a class="level-item">
                  <span class="icon is-small">
                    <i class="fas fa-flag-checkered"></i>
                  </span>
                            <span class="is-size-7 has-text-grey-light">&nbsp, Report </span>
                        </a>
                    </div>
                </nav>
            </div>
            <div class="media-right">
            </div>
        </article>
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
                this.$router.push({path: '/r/' + this.quiz.category.slug + '/' + (this.$route.params.sort || 'new')});
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
