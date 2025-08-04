<script setup lang="ts">
import { ref, nextTick } from 'vue';
import { useStatusStore } from '../stores/statusStore';

const store = useStatusStore();

const props = defineProps<{
    videoUrl: string,
    showtitle?: string,
}>()

const isOpen = ref(false)
const played = ref('')
const hasError = ref(false)
const videoPlayer = ref<HTMLVideoElement | null>(null)

const logVideoEvent = (eventType: string, additionalData = {}) => {
    store.addVideoEvent({
        type: eventType,
        videoUrl: props.videoUrl,
        currentTime: videoPlayer.value?.currentTime || 0,
        duration: videoPlayer.value?.duration || 0,
        ...additionalData
    });
}

const toggleVideo = async () => {
    try {
        isOpen.value = !isOpen.value
        store.videoIsOpen = isOpen.value

        if (isOpen.value && videoPlayer.value) {
            logVideoEvent('video_opened');
            await nextTick()
            try {
                await videoPlayer.value.play()
            } catch (err) {
                console.error('Error playing video:', err)
                logVideoEvent('play_error', { error: err.message });
            }
        } else if (videoPlayer.value) {
            await videoPlayer.value.pause()
            store.videoPlaying = fasle;
            logVideoEvent('video_closed');
        }
    } catch (error) {
        console.error('Error toggling video:', error)
    }
}

const handleVideoEnd = () => {
    isOpen.value = false
    store.videoIsOpen = false
    played.value = ' - played.'
    logVideoEvent('video_ended');
}

const handleVideoError = (error: Event) => {
    hasError.value = true
    store.videoIsOpen = false
    console.error('Video error:', error)
    logVideoEvent('video_error', { error: error.type });
}

// Additional event handlers
const handlePlay = () => {
    logVideoEvent('play');

}
const handlePause = () => logVideoEvent('pause');


const handleLoadedMetadata = () => {
    const duration = videoPlayer.value?.duration || 0;
    store.videoDuration = duration;

}
const handleTimeUpdate = () => {
    // Log every 10 seconds to avoid too many events
    const currentTime = videoPlayer.value?.currentTime || 0;
    if (Math.floor(currentTime) % 10 === 0) {
        store.videoTime = currentTime;
        logVideoEvent('time_update', { currentTime });
    }
}
</script>

<template>
    <div class="video-wrapper">
        <button
            class="btn btn-accent mb-2"
            @click="toggleVideo">
            {{ isOpen ? 'Close Video' : 'Open Video' + played }}
        </button>
        <div v-show="isOpen" class="video-container border mb-2">
            <span v-if="showtitle && showtitle !== ''">Playing {{ showtitle }}</span>
            <video
                ref="videoPlayer"
                controls
                poster="/img/videos/splashPlate.png"
                preload="metadata"
                @ended="handleVideoEnd"
                @error="handleVideoError"
                @play="handlePlay"
                @pause="handlePause"
                @timeupdate="handleTimeUpdate"
                @loadedmetadata="handleLoadedMetadata"
            >
                <source :src="videoUrl" type="video/mp4" />
                Your browser does not support the video tag.
            </video>
        </div>
    </div>
</template>
