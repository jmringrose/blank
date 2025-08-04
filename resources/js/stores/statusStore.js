import { defineStore } from 'pinia';

export const useStatusStore = defineStore('status', {
    state: () => ({
        hasInsurance: false,
        message: '',
        count: 0,
        videoIsOpen: false,
        videoPlaying: false,
        videoTime: 0,
        videoDuration: 0,
        videoVolume: 1,
        videoMuted: false,
        videoFullscreen: false,
        videoControls: false,
        videoLoop: false,
        videoPlaybackRate: 1,
        videoQuality: 'auto',
        videoEvents: [], // Add this to track events
    }),
    actions: {
        updateInsuranceStatus(hasInsurance,message) {
            this.hasInsurance = hasInsurance;
            this.message = message;
            this.count++;
        },
   /*     addVideoEvent(event) {
            this.videoEvents.push({
                type: event.type,
                videoUrl: event.videoUrl || '',
                currentTime: event.currentTime || 0
            });
        },*/
    }
});
