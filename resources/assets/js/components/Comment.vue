<template>
    <div class="card">
        <p>
            <a :href="commentAuthorLink">{{content.user.username}}</a>
            <span class="points">
                X points
            </span>
            <span class="datetime">
                {{ timeFromNow }}
            </span>
        </p>
        <p>
            {{ content.body }}
        </p>
    </div>
</template>

<script>
    export default {
        props: ['content'],
        data() {
            return {

            };
        },
        computed: {
            commentAuthorLink() {
                return '/u/' + this.content.user.username;
            },
            timeFromNow() {
                return moment(this.content.created_at).fromNow();
            }
        },
        created() {

        },
        watch: {

        },
        methods: {

        }
    }
</script>
<style scoped>
    span.points {
        padding-left: 0.5rem;
    }
    span.datetime {
        padding-left: 1rem;
    }

    .red-icon {
        color: red;
    }

    .green-icon {
        color: green;
    }
</style>
