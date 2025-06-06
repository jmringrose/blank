<script setup lang="ts">
import { ref, nextTick } from 'vue';
import { useStatusStore } from '../stores/statusStore';
import { useToast } from '../composables/useToast';

const store = useStatusStore();

const props = defineProps<{
    videoUrl: string,
    showtitle?: string,
}>()

const toast = useToast();
const isOpen = ref(false)
const played = ref('')
const hasError = ref(false)
const videoPlayer = ref<HTMLVideoElement | null>(null)

const toggleVideo = async () => {
    try {
        isOpen.value = !isOpen.value
        store.setVideoOpen(isOpen.value)

        if (isOpen.value && videoPlayer.value) {
            await nextTick()
            try {
                await videoPlayer.value.play()
            } catch (err) {
                console.error('Error playing video:', err)
            }
        } else if (videoPlayer.value) {
            await videoPlayer.value.pause()
        }
    } catch (error) {
        console.error('Error toggling video:', error)
    }
}

const handleVideoEnd = () => {
    isOpen.value = false
    store.setVideoOpen(false)
    played.value = ' - played.'
    toast.info('video played');
}

const handleVideoError = (error: Event) => {
    hasError.value = true
    store.setVideoOpen(false)
    console.error('Video error:', error)
}
</script>
<template>
     <div class="video-wrapper">
         <button
             class="btn btn-accent mb-2"
             @click="toggleVideo">
             {{ isOpen ? 'Close Video' : 'Open Video'+played }}
         </button>
            <div v-show="isOpen" class="video-container">
                <span v-if="showtitle>''">Playing {{ props.videoUrl }}</span>
                <video
                    ref="videoPlayer"
                    controls
                    preload="metadata"
                    @ended="handleVideoEnd"
                    @error="handleVideoError"
                >
                    <source :src="props.videoUrl" type="video/mp4" />
                    Your browser does not support the video tag.
                </video>
            </div>

    </div>
</template>
