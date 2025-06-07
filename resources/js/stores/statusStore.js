import { defineStore } from 'pinia';

export const useStatusStore = defineStore('status', {
    state: () => ({
        hasInsurance: false,
        message: '',
        count: 0,
        videoIsOpen: false,
    }),
    actions: {
        updateInsuranceStatus(hasInsurance,message) {
            this.hasInsurance = hasInsurance;
            this.message = message;
            this.count++;
        },
    }
});
