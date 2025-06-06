<template>
   <div class="btn btn-accent" @click="goPage">{{ title }}</div>
</template>
<script>
export default {
    name: "test",
    props: {
        location: '',
        title: '',
    },
    data() {
     //
    },
    methods: {
        goPage() {
            window.location.replace(this.location);
            }
        }

}


</script>
