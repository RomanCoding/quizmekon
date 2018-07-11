<template>
    <div class="columns">
        <div class="column is-8-desktop is-10-tablet is-12-mobile">
            <!--<b-modal :active.sync="isComponentModalActive" scroll="keep">-->
            <!--<post-modal :content="expandedPost" @upvotePost="upvotePost" @downvotePost="downvotePost"></post-modal>-->
            <!--</b-modal>-->

            <p class="panel-tabs">
                <a :class="{'is-active': sort == 'new'}" @click="changeSort('new')">new</a>
                <a :class="{'is-active': sort == 'hot'}" @click="changeSort('hot')">hot</a>
                <a :class="{'is-active': sort == 'voted'}" @click="changeSort('voted')">most voted</a>
            </p>
            <post v-for="(post, index) in posts" @upvotePost="upvotePost"
                  @downvotePost="downvotePost"
                  :key="index" :content="post" @expanded="visitPostPage(post)">
            </post>
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
    import Post from './Post.vue'
    import PostModal from './PostModal.vue'
    export default {
        components: {
            'post': Post,
            'post-modal': PostModal
        },
        data() {
            return {
                sort: 'new',
                posts: [],
                page: 1,
                expandedPost: null,
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
                axios.get('/posts', {
                    params: {
                        s: this.sort,
                        page: this.page,
                        category: this.$route.params.subreddit
                    }
                }).then((r) => {
                    self.posts = replace ? r.data.data : self.posts.concat(r.data.data || []);
                    self.page++;
                });
            },
            visitPostPage(post) {
                this.$router.push({path: '/r/' + post.category.slug + '/' + post.id});
            },
            toHomepage() {
                this.$router.push({path: '/' + (this.$route.params.sort || 'new')});
            },
            upvotePost(post) {
                if (post.usersVote && post.usersVote.value === 1) {
                    return;
                }
                let self = this;
                axios.patch('/posts/' + post.id + '/vote', {
                    up: 1
                }).then((r) => {
                    let postIndex = self.posts.findIndex((p) => p.id === post.id);
                    if (!post.usersVote || post.usersVote.updated_at !== r.data.updated_at) {
                        post.votesTotal += (post.hasOwnProperty('usersVote') && post.usersVote) ? 2 : 1;
                    }
                    post.usersVote = r.data;
                    if (postIndex > -1) {
                        self.posts[postIndex] = post;
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
            downvotePost(post) {
                let self = this;
                if (post.usersVote && post.usersVote.value === -1) {
                    return;
                }
                axios.patch('/posts/' + post.id + '/vote').then((r) => {
                    let postIndex = self.posts.findIndex((p) => p.id === post.id);
                    if (!post.usersVote || post.usersVote.updated_at !== r.data.updated_at) {
                        post.votesTotal -= (post.hasOwnProperty('usersVote') && post.usersVote) ? 2 : 1;
                    }
                    post.usersVote = r.data;
                    if (postIndex > -1) {
                        self.posts[postIndex] = post;
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
