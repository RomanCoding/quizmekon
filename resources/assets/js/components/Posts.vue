<template>
    <div class="column is-8-desktop is-10-tablet is-12-mobile">
        <b-modal :active.sync="isComponentModalActive" scroll="keep">
            <post-modal :content="expandedPost" @upvotePost="upvote"></post-modal>
        </b-modal>

        <p class="panel-tabs">
            <a :class="{'is-active': sort == 'new'}" @click="sort = 'new'">new</a>
            <a :class="{'is-active': sort == 'hot'}" @click="sort = 'hot'">hot</a>
            <a :class="{'is-active': sort == 'voted'}" @click="sort = 'voted'">most voted</a>
        </p>
        <post v-for="(post, index) in posts" :key="index" :content="post" @expanded="showPostModal(post)"></post>
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
                expandedPost: null,
                isComponentModalActive: false,
            };
        },
        created() {
            let self = this;
            axios.get('/posts?s=' + this.sort).then((r) => {
                self.posts = r.data;
            });
        },
        computed: {},
        watch: {
            'sort': function () {
                let self = this;
                axios.get('/posts?s=' + this.sort).then((r) => {
                    self.posts = r.data;
                });
            }
        },
        methods: {
            showPostModal(post) {
                let self = this;
                axios.get('/posts/' + post.id)
                    .then((r) => {
                        post = r.data;
                        self.expandedPost = post;
                        self.isComponentModalActive = true;
                    });
            },
            upvotePost(post) {
                let self = this;
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
        },
    }
</script>
<style scoped>
    .panel-tabs {
        justify-content: start;
    }
</style>
