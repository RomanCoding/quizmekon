<template>
    <div class="card" v-if="!this.expand">
        <header class="card-header is-mobile columns is-gapless">
            <div class="column is-three-fifths-mobile is-two-thirds-tablet is-half-desktop is-one-third-widescreen is-one-quarter-fullhd">
                <p :class="{'card-header-title':true, 'is-paddingless': post.type == 2}" @click="$emit('expanded')">
                    <img v-if="post.type == 2" :src="ytThumnnail" alt="">
                    {{ post.title }}
                </p>
            </div>
            <div class="column is-gapless">

                <div class="vote-buttons is-pulled-right">
                    <span class="tag is-light pointer" v-text="post.category.name" @click="visitCategory"></span>
                    <div class="is-pulled-right">
                        <font-awesome-icon :style="upArrow" @click="upvote" icon="arrow-circle-up"/>
                        <span>
                            {{ post.votesTotal }}
                        </span>
                        <font-awesome-icon :style="downArrow" @click="downvote" icon="arrow-circle-down"/>
                    </div>
                </div>
            </div>
        </header>
        <footer class="card-footer">
            <span @click="$emit('expanded')"><font-awesome-icon size="xs" icon="comment-alt"/> {{ post.comments_count || 0 }} comments</span>
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
                post: null,
            };
        },
        computed: {
            ytThumnnail() {
                return 'https://img.youtube.com/vi/' + this.post.link + '/1.jpg';
            },
            upArrow() {
                return {
                    color: (this.post && this.post.usersVote && this.post.usersVote.value === 1) ? 'green' : ''
                }
            },
            downArrow() {
                return {
                    color: (this.post && this.post.usersVote && this.post.usersVote.value === -1) ? 'red' : ''
                }
            }
        },
        created() {
            this.post = this.content;
        },
        watch: {
            content: function (newVal) {
                this.post = newVal;
            }
        },
        methods: {
            upvote() {
                this.$emit('upvotePost', this.post);
            },
            downvote() {
                this.$emit('downvotePost', this.post);
            },
            visitCategory() {
                this.$router.push({ path: '/r/' + this.post.category.slug + '/' + (this.$route.params.sort || 'new')});
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
        span {
            padding: 0.25rem;
            font-size: 0.75rem;
            cursor: pointer;

            &:hover {
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
