import { defineStore } from 'pinia';

export const useStatusStore = defineStore('status', {
    state: () => ({
        hasInsurance: false,
        message: '',
        isLoading: false,
        count: 0,
        videoIsOpen: false,
    }),
    actions: {
        updateInsuranceStatus(message,count) {
            this.hasInsurance = true;
            this.message = message;
            this.count = count;
        },
        incrementCount() {
            this.count++;
            return this.count;
        },
        setLoading(status) {
            this.isLoading = status;
        },
        setVideoOpen(isOpen) {
            this.videoIsOpen = isOpen;
        }
    }
});
