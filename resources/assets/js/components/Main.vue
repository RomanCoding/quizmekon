<template>
    <div class="wrapper">
        <site-header></site-header>
        <div class="sidebar">

            <div class="sidebar_top_box">
                <div class="field is-grouped is-grouped-centered">
                    <img src="images/add_content.png" alt="" style="cursor: pointer;" @click="createPost">
                </div>
            </div>

            <div class="sidebar_ad_box">
                <p class="is-size-7 has-text-info">ADVERTISEMENT</p>
                <img src="images/adsense300x250.PNG" alt="">
            </div>

            <div class="sidebar_ad_box" v-if="filteredByChannel" v-show="filteredByChannel">
                <p class="is-size-7 has-text-info">You are browsing <b>{{ $route.params.subreddit }}</b> channel.
                    <br>
                    If you want to go back, <a href="#" @click.prevent="toHomepage">click here.</a>
                </p>
            </div>

            <div class="sidebar_top_box2">
                <div class="tabs is-boxed is-small">
                    <ul>
                        <li class="is-active">
                            <a>Top Points</a>
                        </li>
                        <li>
                            <a>Most Content</a>
                        </li>
                        <li>
                            <a>Most Voted</a>
                        </li>
                    </ul>
                </div>
                <table class="table table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
                    <thead>
                    <tr>
                        <th>
                            <abbr title="Position">Pos</abbr>
                        </th>
                        <th>User</th>
                        <th>points</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>1</td>
                        <td>Fred</td>
                        <td>2005</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Jim</td>
                        <td>2000</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>Monty</td>
                        <td>1960</td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div class="sidebar_ad_box2">
                <p class="is-size-7 has-text-info">ADVERTISEMENT</p>
                <img src="images/300x250.png" alt="">
            </div>
        </div>
        <quizzes></quizzes>
    </div>
</template>

<script>
    import Quizzes from './Quizzes.vue'
    export default {
        components: {
            'quizzes': Quizzes,
        },
        data() {
            return {
            };
        },
        mounted() {
        },
        created() {

        },
        computed: {
            filteredByChannel() {
                return this.$route.params.subreddit;
            }
        },
        watch: {},
        methods: {
            toHomepage() {
                this.$router.push({path: '/' + (this.$route.params.sort || 'new')});
            },
            createPost() {
                if (this.$parent.user && !this.$parent.user.confirmed) {
                    this.$toast.open({
                        duration: 5000,
                        message: "Please confirm your registration by acknowledging the email we have sent to " + this.$parent.user.email,
                        position: 'is-top',
                        type: 'is-danger'
                    });
                } else {
                    this.$router.push({path: '/submit'});
                }
            },
        }
    }
</script>
<style scoped>

</style>