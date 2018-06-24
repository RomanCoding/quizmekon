<template>
    <div class="card" v-if="!this.expand">
        <header class="card-header is-mobile columns is-gapless">
            <div class="column is-three-fifths-mobile is-two-thirds-tablet is-half-desktop is-one-third-widescreen is-one-quarter-fullhd">
                <p class="card-header-title" v-text="post.title" @click="$emit('expanded')"></p>
            </div>
            <div class="column is-gapless">

                <div class="vote-buttons is-pulled-right">
                    <span class="tag is-light" v-text="post.category.name"></span>
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
        <!--<div class="card-content">-->

        <!--<div class="is-11">-->
        <!--<div class="content is-clearfix">-->
        <!--{{ post.body }}-->
        <!--<a href="#">@bulmaio</a>. <a href="#">#css</a> <a href="#">#responsive</a>-->
        <!--<br>-->
        <!--<time datetime="2016-1-1">11:09 PM - 1 Jan 2016</time>-->
        <!--</div>-->
        <!--</div>-->
        <!--</div>-->
        <!--<footer class="card-footer">-->
        <!--<a href="#" class="card-footer-item">Save</a>-->
        <!--<a href="#" class="card-footer-item">Edit</a>-->
        <!--<a href="#" class="card-footer-item">Delete</a>-->
        <!--</footer>-->
    </div>
    <!--<div class="card">-->
    <!--<header class="card-header is-mobile columns is-gapless">-->
    <!--<div class="column is-11">-->
    <!--<p class="card-header-title" v-text="post.title"></p>-->
    <!--</div>-->
    <!--<div class="column">-->
    <!--<div class="vote-buttons is-pulled-right">-->
    <!--<font-awesome-icon style="color: green;" @click="upvote" icon="arrow-circle-up"/>-->
    <!--<br>-->
    <!--<font-awesome-icon style="color: red;" @click="downvote" icon="arrow-circle-down"/>-->
    <!--</div>-->
    <!--</div>-->
    <!--</header>-->
    <!--<div class="card-content">-->

    <!--<div class="is-11">-->
    <!--<div class="content is-clearfix">-->
    <!--{{ post.body }}-->
    <!--<a href="#">@bulmaio</a>. <a href="#">#css</a> <a href="#">#responsive</a>-->
    <!--<br>-->
    <!--<time datetime="2016-1-1">11:09 PM - 1 Jan 2016</time>-->
    <!--</div>-->
    <!--</div>-->
    <!--</div>-->
    <!--<footer class="card-footer">-->
    <!--<a href="#" class="card-footer-item">Save</a>-->
    <!--<a href="#" class="card-footer-item">Edit</a>-->
    <!--<a href="#" class="card-footer-item">Delete</a>-->
    <!--</footer>-->
    <!--</div>-->
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
            content: function(newVal) {
                this.post = newVal;
            }
        },
        methods: {
            upvote() {
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

        }
    }
</script>
<style scoped lang="scss">
    /*.card-content {*/
    /*padding-left: 0.5rem;*/
    /*}*/
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
