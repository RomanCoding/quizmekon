<template>
    <div>
        <site-header></site-header>

    <section class="section">
        <template v-if="series.length">
            <b-table :data="series">
                <template slot-scope="props">
                    <b-table-column field="question" label="Question">
                        {{ props.row.question }}
                    </b-table-column>

                    <b-table-column field="correct_answer" label="Correct Answer">
                        {{ props.row.correct_answer }}
                    </b-table-column>

                    <b-table-column field="false_answer" label="False Answer 1">
                        {{ props.row.false_answer }}
                    </b-table-column>

                    <b-table-column field="false_answer_2" label="False Answer 2">
                        {{ props.row.false_answer_2 }}
                    </b-table-column>
                </template>
            </b-table>
            <a class="button is-primary" @click="submit">Create series</a>
        </template>
        <h3 class="is-size-5 has-text-info">Start by pasting the Youtube URL you'd like to use below</h3>
        <input @input="onInput" v-model="url" class="input is-rounded" type="text" placeholder="YouTube URL">

        <article class="media">
            <figure class="media-left">
                <p class="image">
                    <img :src="thumb">
                </p>
            </figure>
            <div class="media-content" v-if="title || description">
                <div class="content">
                    <b-notification ref="element" :closable="false">
                        <p>
                            <strong v-text="title"></strong>
                            <br>
                            {{ description }}
                        </p>
                    </b-notification>
                </div>
            </div>
        </article>

        <div class="questionblock" v-if="stage2">
            <b-field>
                <b-select placeholder="Choose a category" v-model="category">
                    <option :value="category.id" v-for="category in categories" v-text="category.name"></option>
                </b-select>
            </b-field>
            <div class="heading">Please select the question type.</div>
            <button v-for="type in types" @click="questionType(type.type)" v-text="type.text"
                    :class="buttonTypeClass(type.type)"></button>
        </div>

        <div v-if="stage3" style="margin-top: 1rem;">
            <b-field label="Title">
                <b-input placeholder="Title" v-model="quizTitle"></b-input>
            </b-field>
            <b-field label="Question to ask">
                <b-input placeholder="Question" v-model="questionValue"></b-input>
            </b-field>
            <b-field label="The correct answer">
                <b-input placeholder="Correct answer" v-model="correctAnswer"></b-input>
            </b-field>
            <b-field label="False answer one">
                <b-input placeholder="False answer one" v-model="falseOneValue"></b-input>
            </b-field>
            <b-field label="False answer two">
                <b-input placeholder="False answer two" v-model="falseTwoValue"></b-input>
            </b-field>
            <b-field label="Start time">
                <b-select placeholder="Hour" v-if="lengthHours > 1" v-model="startTime.hour">
                    <option
                            v-for="hour in lengthHours"
                            :value="hour-1">
                        {{ hour - 1 }}
                    </option>
                </b-select>
                <b-select placeholder="Minute" v-if="lengthMinutes" v-model="startTime.minute">
                    <option
                            v-for="minute in lengthMinutes"
                            :value="minute-1">
                        {{ minute - 1 }}
                    </option>
                </b-select>
                <b-select placeholder="Second" v-if="lengthSeconds" v-model="startTime.second">
                    <option
                            v-for="second in lengthSeconds"
                            :value="second-1">
                        {{ second - 1 }}
                    </option>
                </b-select>
            </b-field>
            <b-field label="Pause time" v-if="type === 1">
                <b-select placeholder="Hour" v-if="lengthHours > 1" v-model="pauseTime.hour">
                    <option
                            v-for="hour in lengthHours"
                            :value="hour-1">
                        {{ hour - 1 }}
                    </option>
                </b-select>
                <b-select placeholder="Minute" v-if="lengthMinutes" v-model="pauseTime.minute">
                    <option
                            v-for="minute in lengthMinutes"
                            :value="minute-1">
                        {{ minute - 1 }}
                    </option>
                </b-select>
                <b-select placeholder="Second" v-if="lengthSeconds" v-model="pauseTime.second">
                    <option
                            v-for="second in lengthSeconds"
                            :value="second-1">
                        {{ second - 1 }}
                    </option>
                </b-select>
            </b-field>
            <b-field label="End time">
                <b-select placeholder="Hour" v-if="lengthHours > 1" v-model="endTime.hour">
                    <option
                            v-for="hour in lengthHours"
                            :value="hour-1">
                        {{ hour - 1 }}
                    </option>
                </b-select>
                <b-select placeholder="Minute" v-if="lengthMinutes" v-model="endTime.minute">
                    <option
                            v-for="minute in lengthMinutes"
                            :value="minute-1">
                        {{ minute - 1 }}
                    </option>
                </b-select>
                <b-select placeholder="Second" v-if="lengthSeconds" v-model="endTime.second">
                    <option
                            v-for="second in lengthSeconds"
                            :value="second-1">
                        {{ second - 1 }}
                    </option>
                </b-select>
            </b-field>
            <youtube :video-id="videoID" :player-vars="playerVars" @ended="ended" ref="youtube"
                     style="margin-top: 1rem;"/>
            <br>
            <a class="button is-primary" @click="appendToSeries">Add more quizzes to series</a>
            <a class="button is-primary" @click="submit" v-if="!series.length">Create</a>
        </div>

    </section>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                types: [
                    {type: 1, text: 'What happens next'},
                    {type: 3, text: 'Video then question'},
                    {type: 2, text: 'Question then video'},
                ],
                category: null,
                url: '',
                categories: [],
                series: [],
                title: '',
                description: '',
                videoID: null,
                thumb: '',
                stage2: false,
                stage3: false,
                type: '',
                isFullPage: false,
                quizTitle: '',
                questionValue: '',
                correctAnswer: '',
                falseOneValue: '',
                falseTwoValue: '',
                duration: '',

                playerVars: {
                    autoplay: 0,
                    start: 0,
                    end: 0,
                    rel: 0,
                    controls: 1,
                    showinfo: 1,
                },
                startTime: {
                    hour: 0,
                    minute: 0,
                    second: 0,
                },
                endTime: {
                    hour: 0,
                    minute: 0,
                    second: 0,
                },
                pauseTime: {
                    hour: 0,
                    minute: 0,
                    second: 0,
                }

            }
        },
        created() {
            let self = this;
            axios.get('/categories').then(r => self.categories = r.data);
        },
        computed: {
            player () {
                return this.$refs.youtube.player
            },
            startInSeconds() {
                return this.startTime.hour * 3600 + this.startTime.minute * 60 + this.startTime.second;
            },
            pauseInSeconds() {
                return this.pauseTime.hour * 3600 + this.pauseTime.minute * 60 + this.pauseTime.second;
            },
            endInSeconds() {
                return this.endTime.hour * 3600 + this.endTime.minute * 60 + this.endTime.second;
            },
            lengthHours() {
                return ~~(this.duration / 3600) + 1;
            },
            lengthMinutes() {
                return this.startTime.hour < (this.lengthHours - 1) ? 60 : ~~((this.duration - (this.lengthHours - 1) * 3600) / 60) + 1;
            },
            lengthSeconds() {
                return this.startTime.minute < (this.lengthMinutes - 1) ? 60 : this.duration % 60 + 1;
            },
        },
        watch: {
            startTime: {
                handler() {
                    this.player.seekTo(this.startTime.hour * 3600 + this.startTime.minute * 60 + this.startTime.second);
                },
                deep: true
            },
            endTime: {
                handler() {
                    this.playerVars.end = this.endTime.hour * 3600 + this.endTime.minute * 60 + this.endTime.second;
                },
                deep: true
            },
        },
        methods: {
            buttonTypeClass(type) {
                return this.type === type ? 'button is-success' : 'button is-primary';
            },
            alert(message, duration = 5000) {
                this.$toast.open({
                    duration: duration,
                    message: message,
                    position: 'is-top',
                    type: 'is-danger'
                });
            },
            onInput() {
                this.title = '';
                this.description = '';
                this.thumb = '';
                let regExp = /^.*(youtu\.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
                let match = this.url.match(regExp);
                if (match && match[2].length == 11) {
                    let self = this;
                    this.searchYT(match[2]).then(r => {
                        self.open();
                        self.onChange();
                        self.videoID = match[2];
                        self.searchYTDuration(match[2]);
                    }).catch(e => {
                        self.alert(e.response.data.message);
                        this.onChange();
                        this.stage2 = false;
                        this.stage3 = false;
                    });
                } else {
                    this.alert('Sorry - unable to search against that term');
                    this.onChange();
                    this.stage2 = false;
                    this.stage3 = false;
                }
            },
            searchYT(VideoID) {
                let self = this;
                return new Promise(function (resolve, reject) {
                    axios.post('/youtube/search', {
                        id: VideoID,
                    }).then(response => {
                        const videoData = response.data.items;
                        const items = [];
                        for (let key in videoData) {
                            const item = videoData[key];
                            item.id = key;
                            items.push(item);
                        }
                        self.title = items[0].snippet.title;
                        self.description = items[0].snippet.description;
                        self.thumb = items[0].snippet.thumbnails.default.url;
                        resolve();
                    }).catch(e => {
                        reject(e);
                    });
                });
            },
            searchYTDuration(VideoID) {
                axios.post('/youtube/videos', {
                    id: VideoID,
                }).then(response => {
                    const videoData = response.data.items;
                    const items = [];
                    for (let key in videoData) {
                        const item = videoData[key];
                        item.id = key;
                        items.push(item);
                    }
                    this.duration = this.convert_time(items[0].contentDetails.duration);
                });
            },
            convert_time(duration) {
                let a = duration.match(/\d+/g);

                if (duration.indexOf('M') >= 0 && duration.indexOf('H') == -1 && duration.indexOf('S') == -1) {
                    a = [0, a[0], 0];
                }

                if (duration.indexOf('H') >= 0 && duration.indexOf('M') == -1) {
                    a = [a[0], 0, a[1]];
                }
                if (duration.indexOf('H') >= 0 && duration.indexOf('M') == -1 && duration.indexOf('S') == -1) {
                    a = [a[0], 0, 0];
                }

                duration = 0;

                if (a.length == 3) {
                    duration = duration + parseInt(a[0]) * 3600;
                    duration = duration + parseInt(a[1]) * 60;
                    duration = duration + parseInt(a[2]);
                }

                if (a.length == 2) {
                    duration = duration + parseInt(a[0]) * 60;
                    duration = duration + parseInt(a[1]);
                }

                if (a.length == 1) {
                    duration = duration + parseInt(a[0]);
                }
                return duration
            },
            open() {
                const loadingComponent = this.$loading.open({
                    container: this.isFullPage ? null : this.$refs.element.$el
                });
                setTimeout(() => loadingComponent.close(), 2 * 1000);
                this.stage2 = true;
            },
            questionType(typeID) {
                this.type = typeID;
                this.stage3 = true;
                this.questionValue = typeID === 1 ? 'What happens next' : '';
            },
            isCorrectTime() {
                if (this.timeToSeconds(this.endTime) > this.duration ||
                    this.timeToSeconds(this.startTime) >= this.duration) {
                    this.alert('Time can not be greater than video duration!');
                    return false;
                }
                if (this.timeToSeconds(this.endTime) <= this.timeToSeconds(this.startTime)) {
                    this.alert('End time should be greater than start time!');
                    return false;
                }
                if (this.timeToSeconds(this.endTime) - this.timeToSeconds(this.startTime) > 15) {
                    this.alert('Video should be less than 15 seconds!');
                    return false;
                }
                if (this.type === 1) {
                    if (this.timeToSeconds(this.pauseTime) <= this.timeToSeconds(this.startTime)) {
                        this.alert('Pause time should be greater than start time!');
                        return false;
                    }
                    if (this.timeToSeconds(this.pauseTime) > (this.timeToSeconds(this.endTime) - 1)) {
                        this.alert('Pause time should be less than end time!');
                        return false;
                    }
                }
                return true;
            },
            timeToSeconds(time) {
                return time.hour * 3600 + time.minute * 60 + time.second;
            },
            submit() {
                if (this.series.length) {
                    return this.submitSeries();
                }
                if (this.isCorrectTime()) {
                    let self = this;
                    let data = {
                        type: this.type,
                        category_id: this.category,
                        youtube_id: this.videoID,
                        question: this.questionValue,
                        title: this.quizTitle,
                        correct_answer: this.correctAnswer,
                        false_answer: this.falseOneValue,
                        false_answer_2: this.falseTwoValue,
                        start_time: this.startInSeconds,
                        end_time: this.endInSeconds,
                    };
                    if (this.type === 1) {
                        data.pause_time = this.pauseInSeconds;
                    }
                    axios.post('/quizzes', data).then(r => {
                        self.$router.push({path: '/r/' + r.data.category.slug + '/' + r.data.id});
                    }).catch(e => {
                        self.alert(e.response.data.errors ? e.response.data.errors[Object.keys(e.response.data.errors)[0]][0] : e.response.data.message);
                    });
                }
            },
            submitSeries() {
                let self = this;
                axios.post('/quizzes/bulk', {series: this.series}).then(r => {
                    self.$router.push({path: '/r/' + r.data.category.slug + '/' + r.data.id});
                }).catch(e => {
                    self.alert(e.response.data.errors ? e.response.data.errors[Object.keys(e.response.data.errors)[0]][0] : e.response.data.message);
                });
            },
            appendToSeries() {
                if (this.isCorrectTime()) {
                    let data = {
                        type: this.type,
                        category_id: this.category,
                        youtube_id: this.videoID,
                        question: this.questionValue,
                        title: this.quizTitle,
                        correct_answer: this.correctAnswer,
                        false_answer: this.falseOneValue,
                        false_answer_2: this.falseTwoValue,
                        start_time: this.startInSeconds,
                        end_time: this.endInSeconds,
                    };
                    if (this.type === 1) {
                        data.pause_time = this.pauseInSeconds;
                    }
                    this.series.push(data);
                    this.resetAll();
                }
            },
            resetAll() {
                this.category = null;
                this.title = '';
                this.description = '';
                this.videoID = null;
                this.thumb = '';
                this.stage2 = false;
                this.stage3 = false;
                this.type = '';
                this.isFullPage = false;
                this.questionValue = '';
                this.correctAnswer = '';
                this.falseOneValue = '';
                this.falseTwoValue = '';
                this.duration = '';
                this.url = '';
                this.playerVars = {
                    autoplay: 0,
                    start: 0,
                    end: 0,
                    rel: 0,
                    controls: 1,
                    showinfo: 1,
                };
                this.startTime = {
                    hour: 0,
                    minute: 0,
                    second: 0,
                };
                this.endTime = {
                    hour: 0,
                    minute: 0,
                    second: 0,
                };
                this.pauseTime = {
                    hour: 0,
                    minute: 0,
                    second: 0,
                };
            },
            onChange() {
                this.questionValue = '';
                this.correctAnswer = '';
                this.falseOneValue = '';
                this.falseTwoValue = '';
            },
            ended() {
                //
            }
        },
    }
</script>
<style scoped lang="scss">
    .questionblock,
    article.media {
        margin-top: 1rem;
    }

    .questionblock button {
        margin-right: 0.5em;
    }
</style>
