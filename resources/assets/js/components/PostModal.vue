<template>
    <div class="card">
        <header class="card-header is-mobile columns is-gapless">
            <div class="column is-9-desktop is-6-mobile">
                <p class="card-header-title">
                    <a :href="subredditUrl" v-text="subredditUrl"></a>
                    <br>
                    <span>
                        {{ post.title }}
                    </span>
                </p>
            </div>
            <div class="column">
                <div class="vote-buttons is-pulled-right">
                    <div>
                        <font-awesome-icon :style="upArrow" @click="upvote" icon="arrow-circle-up"/>
                        <span>
                            {{ post.votesTotal }}
                        </span>
                        <font-awesome-icon :style="downArrow" @click="downvote" icon="arrow-circle-down"/>
                    </div>
                </div>
                <div class="posted-by is-pulled-right">
                    Posted by u/{{post.user.username}}
                </div>

            </div>
        </header>
        <div class="card-content">
            <div class="is-11">
                <div class="content is-clearfix">
                    {{ post.body }}
                    <a href="#">@bulmaio</a>. <a href="#">#css</a> <a href="#">#responsive</a>
                    <br>
                    <time datetime="2016-1-1">11:09 PM - 1 Jan 2016</time>
                </div>
            </div>
        </div>
        <footer class="card-footer">
            <div class="columns is-multiline">
                <div class="column is-12">
                    <b-input type="textarea" size="is-small" rows="4" placeholder="What are your thoughts?"
                             v-model="postComment"></b-input>
                    <button class="button is-primary is-small is-pulled-right" @click="commentPost">
                        Comment
                    </button>
                </div>
                <div class="column is-12">
                    <comment v-for="(comment, index) in post.comments" :key="index" :content="comment"></comment>
                </div>
            </div>
            <!--<a href="#" class="card-footer-item">Save</a>-->
            <!--<a href="#" class="card-footer-item">Edit</a>-->
            <!--<a href="#" class="card-footer-item">Delete</a>-->
        </footer>
    </div>
</template>

<script>
    import Comment from './Comment.vue'
    export default {
        components: {
            'comment': Comment
        },
        props: ['content'],
        data() {
            return {
                post: null,
                postComment: ''
            };
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
            }
        },
        created() {
            this.post = this.content;
        },
        watch: {},
        methods: {
            commentPost() {
                let self = this;
                axios.post('/comments/post/' + this.post.id, {
                    body: this.postComment
                }).then((r) => {
                    self.post.comments.push(r.data);
                }).catch((e) => {
                    alert('Error commenting!');
                });
            },
            upvote() {
                let self = this;
                this.$emit('upvotePost');
                axios.patch('/posts/' + this.post.id + '/vote', {
                    up: 1
                }).then((r) => {
                    if (self.post.usersVote.updated_at !== r.data.updated_at) {
                        self.post.votesTotal++;
                    }
                    self.post.usersVote = r.data;
                }).catch((e) => {

                });
            },
            downvote() {
                let self = this;
                axios.patch('/posts/' + this.post.id + '/vote')
                    .then((r) => {
                        if (self.post.usersVote.updated_at !== r.data.updated_at) {
                            self.post.votesTotal--;
                        }
                        self.post.usersVote = r.data;
                    })
                    .catch((e) => {

                    });
            },
        }
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
        /*margin-left: -0.5rem;*/
    }

    .card-header-title {
        font-size: 80%;
    }

    .posted-by {
        font-size: 80%;
        color: grey;
        margin-right: 1rem;
    }

    footer div {
        width: 100%;
        padding: 0.5rem;
    }
</style>
