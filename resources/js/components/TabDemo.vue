<script setup lang="ts">
import { ref } from 'vue';
const activeTab = ref(1);
const setActiveTab = (tabIndex: number) => {
    activeTab.value = tabIndex;
};
</script>

<template>
    <div class="tabs tabs-lift">
        <button
            class="tab"
            :class="{ 'tab-active': activeTab === 0 }"
            @click="setActiveTab(0)"
        >
            Tab 1
        </button>
        <button
            class="tab"
            :class="{ 'tab-active': activeTab === 1 }"
            @click="setActiveTab(1)"
        >
            Tab 2
        </button>
        <button
            class="tab"
            :class="{ 'tab-active': activeTab === 2 }"
            @click="setActiveTab(2)"
        >
            Tab 3
        </button>
        <button
            class="tab"
            :class="{ 'tab-active': activeTab === 3 }"
            @click="setActiveTab(3)"
        >
            Tab 4
        </button>
    </div>
    <div class=" p-4 bg-base-100 mb-4 border border-accent h-[270px] rounded-xl">
        <div v-if="activeTab === 0">But I must explain to you how all this mistaken idea of denouncing pleasure and praising pain was born and I will give you a complete account of the system, and expound the actual teachings of the great explorer of the truth, the master-builder of human happiness. No one rejects, dislikes, or avoids pleasure itself, because it is pleasure, but because those who do not know how to pursue pleasure rationally encounter consequences that are extremely painful. Nor again is there anyone who loves or pursues or desires to obtain pain of itself, because it is pain, but because occasionally circumstances occur in which toil and pain can procure him some great pleasure. To take a trivial example, which of us ever undertakes laborious physical exercise, except to obtain some advantage from it? But who has any right to find fault with a man who chooses to enjoy a pleasure that has no annoying consequences, or one who avoids a pain that produces no resultant pleasure?"
        </div>
        <div v-if="activeTab === 1">At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga.</div>
        <div v-if="activeTab === 2">Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat.</div>
        <div v-if="activeTab === 3">Content for Tab 4 - nothing to see, please move on.</div>
    </div>
</template>
