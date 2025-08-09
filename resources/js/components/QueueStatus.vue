<template>
    <div class="flex items-center gap-2" style="min-height:20px">
        <span :style="dotStyle" class="inline-block w-3 h-3 rounded-full"></span>
        <span class="text-sm">{{ text }}</span>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'

const text = ref('Checking…')
const color = ref('gray')
const dotStyle = computed(() => ({ background: color.value }))

let timer = null

async function checkQueue() {
    try {
        const res = await fetch('/api/health/queue?ts=' + Date.now(), {
            headers: { 'Accept': 'application/json' },
            cache: 'no-store',
            credentials: 'same-origin'
        })
        if (!res.ok) throw new Error(`HTTP ${res.status}`)
        const data = await res.json()
        console.log('Queue health:', data)

        if (data.running) {
            color.value = 'green'
            text.value = 'Queue running'
        } else {
            color.value = 'red'
            text.value = 'No active workers'
        }
    } catch (err) {
        console.error('Queue status error:', err)
        color.value = 'orange'
        text.value = 'Error checking status'
    }
}

onMounted(() => {
    // Show something immediately
    color.value = 'gray'
    text.value = 'Checking…'
    checkQueue()
    timer = setInterval(checkQueue, 20000)
})
onBeforeUnmount(() => { if (timer) clearInterval(timer) })
</script>
