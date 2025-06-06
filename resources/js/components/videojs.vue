<template>
    <div class="max-w-7xl">
        <div class="mx-auto rounded">
            <div class="video border-grey-600 shadow bg-gray-900">
                <video id="ThePlayer" class="video-js vjs-16-9 rounded-lg" controls playsinline preload="none"></video>
            </div>
        </div>
    </div>
</template>
<script>
import 'videojs-sprite-thumbnails';
export default {
    name: 'video-vue',
    props: ['poster', 'url480', 'url720', 'url1080', 'url3840', 'vttfile', 'jump', 'quality', 'fourk'],
    data() {
        return {
            player: '',
            currentposn: 0,
            jumpvar: this.jump,
            playerPos: 0,
            qual: '',
            type1: true,
            type2: false,
            res: '',
            source: [],
            button_text: 'naff',
            thumbs: this.vttfile,
        };
    },
    created() {
        this.Mobile();
        this.getpos();
        setInterval(this.getpos, 1000);
        // requests and data from the comment system
        this.emitter.on('pausePlayer', item => {
            this.playerPause();
        });
        this.emitter.on('startPlayer', item => {
            this.playplayer();
        });
        // the user is making a new poster so use a splashplate
        // and reset the player
        this.emitter.on('poster', item => {
            this.playerSetPoster(item);
        });
        this.emitter.on('jumpPlayer', item => {
            this.jumptoLocation(item);
            this.$refs.player1.focus();
        });
        this.emitter.on('updateRes', item => {
            console.info('Source changed to %s', this.player.src())
        });
        this.emitter.on('fullscreen', item => {
            this.playerFullscreenToggle();
            this.playplayer();
        });
        if (this.jumpvar > 0) jumptoLocation(this.jumpvar);
        if (this.quality !== null) this.qual = this.quality; else this.qual = "720p"
    },
    mounted() {
        window.playerEvents = this;
        this.playerInitialize();
        this.playerSetupEvents();
    },
    methods: {
        Mobile() {
            if (this.isMobileDevice()) {
                this.button_text = "Mobile";
            }
            else {
                this.button_text = "";
            }
        },
        isMobileDevice() {
            return window
                .matchMedia("only screen and (max-width: 760px)").matches;
        },
        playerInitialize() {
            // set up sources
            if (this.fourk === 1) {
                this.source = [
                    {src: this.url3840, type: 'video/mp4', label: '4K', 'selected': null, res: '2160',},
                    {src: this.url1080, type: 'video/mp4', label: 'HD', 'selected': null, res: '1080'},
                    {src: this.url720,  type: 'video/mp4', label: '720p', 'selected': true, res: '720', spriteThumbnails: { url: this.vttfile,} },
                    {src: this.url480,  type: 'video/mp4', label: '480p', 'selected': null, res: '480'},
                ]
            } else {
                this.source = [
                    {src: this.url1080, type: 'video/mp4', label: '1080p', 'selected': null, res: '1080' },
                    {src: this.url720,  type: 'video/mp4', label: '720p', 'selected': true, res: '720', spriteThumbnails: { url: this.vttfile, }},
                    {src: this.url480,  type: 'video/mp4', label: '480p', 'selected': null, res: '480'},
                ]
            }
            this.player = videojs('ThePlayer', {
                suppressNotSupportedError: true,
                controls: true,
                controlBar: {
                    /* full list of controls
                    'playToggle',
                    58 :     'volumePanel',
                    59 :     'currentTimeDisplay',
                    60 :     'timeDivider',
                    61 :     'durationDisplay',
                    62 :     'progressControl',
                    63 :     'liveDisplay',
                    64 :     'seekToLive',
                    65 :     'remainingTimeDisplay',
                    66 :     'customControlSpacer',
                    67 :     'playbackRateMenuButton',
                    68 :     'chaptersButton',
                    69 :     'descriptionsButton',
                    70 :     'subsCapsButton',
                    71 :     'audioTrackButton',
                    72 :     'fullscreenToggle'
                    */
                    chaptersButton: {

                    },
                    volumePanel: {
                        inline: false,
                        volumeControl: {
                            vertical: true
                        }
                    },
                    children: [
                        'qualitySelector',

                        'currentTimeDisplay',
                        'timeDivider',
                        'durationDisplay',

                        'playToggle',
                        'progressControl',

                        'volumePanel',
                        'chaptersButton',
                        'fullscreenToggle'
                    ],
                },
                sources:  this.source,
                liveDisplay: false,
                poster: this.poster,
                muted: false,
                plugins: {
                    spriteThumbnails: {
                        url: this.vttfile,
                        interval: 3,
                        width: 160,
                        height: 90,
                        columns: 11,
                        rows: 11
                    },
                }
            });
        },
        playerDispose() {
            this.player.dispose();
        },
        playerPlay() {
            this.player.play();
            //this.$refs.player1.focus();
        },
        playerPause() {
            this.emitGlobalPos();
            this.player.pause();
        },
        playerSetVolume(float) {
            this.player.volume(float);
        },
        playerSetPoster(url) {
            // reset and set poster
            this.player.poster(url);
            this.player.initChildren();
        },
        playerSetTime(time) {
            this.player.currentTime(time);
        },
        playerFullscreenToggle() {
            this.player.requestFullscreen();
        },
        // events
        playerEventEnded() {
        },
        playerEventVolume() {
            this.volume = this.player.volume();
        },
        playerEventError() {
            var errors = this.player.error()
            var msg = 'Not sure what happen.'
            var body = 'Please reload to try again.'
            // 403 or code 4 the video has expired, code can't connect
            if (errors.code == '2') {
                msg = 'You session seems to have expired.';
            } else if (errors.code == '4') {
                msg = 'Having trouble making the video connection.';
            }
            this.$swal({
                title: msg,
                text: "Please refresh the page to reload the video!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#dd3333',
                confirmButtonText: 'Reload Page!'
            }).then((result) => {
                if (result.value) {
                    window.location.reload()
                }
            })
        },
        playerEventPaused() {
            // emit in seconds to comment system
            if (this.playerGetTime() > 0) {
                console.log('Time is :'+this.playerGetTime());
                this.emitter.emit('playerPosn', {'time': this.playerGetTime()});
            }
        },
        // end events
        playerGetPaused() {
            return this.player.paused();
        },
        playerGetTime() {
            if (this.player) return this.player.currentTime(); else return -1;
        },
        showLocation() {
            this.$toasted.show("We are at: " + formatTime(parseInt(this.playerGetTime())));
        },
        playerGetError() {
            return this.player.error().message;
        },
        // test methods only
        jumptoLocation(loc) {
            this.playerSetTime(loc);
            this.$toasted.show('Jumping to timecode: ' + formatTime(parseInt(loc)) + " seconds.");
            this.playerPlay();
        },
        pauseplayer() {
            var pausepoint = this.playerGetTime();
            this.emitTime(pausepoint);
            this.player.pause();
            this.$toasted.show('Pausing at ' + formatTime(parseInt(pausepoint)) + " seconds.");
        },
        playplayer() {
            this.player.play();
        },
        back5() {
            this.playerPause();
            var pausepoint = this.playerGetTime();
            var destination = pausepoint - 5;
            if (destination < 0) destination = 0;
            this.playerSetTime(destination);
            this.$toasted.show("Moved back 5 seconds.");
            this.player.play();
        },
        emitTime(point) {
            this.$toasted.show("We are at: " + formatTime(parseInt(point)));
        },
        getpos() {
            this.currentposn = formatTime(parseInt(this.playerGetTime()));
        },
        // not used?
        emitGlobalPos() {
            var globalPosn = this.playerGetTime();
            if(globalPosn > 0) {
                //console.log('Posn: ' + globalPosn )
                this.emitter.emit('playerPos', { 'posn': globalPosn });
            }
        },
        // end test methods
        playerSetupEvents() {
            this.player.on('volumechange', function () {
                window.playerEvents.playerEventVolume();
            });
            this.player.on('error', function () {
                window.playerEvents.playerEventError();
            });
            this.player.on('pause', function () {
                window.playerEvents.playerEventPaused();
            });
            // broken seems ot fire too early and make an init error
            // don't need it currently
            /*
            this.player.on('resolutionchange', function () {
               //this.emitter.emit('startPlayer', { 'Mag' : 'started' });
               //this.emitter.emit('updateRes', { 'Msg' : 'res change'});
            });
            */

        },
    },
    beforeDestroy() {
        this.playerDispose();
    }
}

//=====================================================================
function formatTime(seconds) {
    if (seconds == -1) return "00:00";
    const h = Math.floor(seconds / 3600);
    const m = Math.floor((seconds % 3600) / 60);
    const s = seconds % 60;
    return [
        h,
        m > 9 ? m : (h ? '0' + m : m || '0'),
        s > 9 ? s : '0' + s,
    ].filter(a => a).join(':');
}

//====================================================================
</script>
<style>

</style>
