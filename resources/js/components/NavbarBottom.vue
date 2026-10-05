<template>
    <div class="main-container flex items-center justify-between gap-4 relative pb-2 md:pb-3">



        <!-- Main menu -->
        <div class="hidden md:flex justify-start items-center gap-6 lg:gap-8 grow overflow-x-auto no-scrollbar px-2">
            <div v-for="(menu, index) in master.menus" :key="menu.id" class="flex-shrink-0">
                <router-link v-if="!menu.is_external" :to="menu.url" :target="menu.target"
                    class="relative py-2 text-sm lg:text-base font-semibold text-slate-700 hover:text-primary transition-colors group">
                    {{ menu.name }}
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-primary transition-all duration-300 group-hover:w-full rounded-full"></span>
                </router-link>
                <a v-else :href="menu.url" :target="menu.target"
                    class="relative py-2 text-sm lg:text-base font-semibold text-slate-700 hover:text-primary transition-colors group">
                    {{ menu.name }}
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-primary transition-all duration-300 group-hover:w-full rounded-full"></span>
                </a>
            </div>
        </div>

    </div>
</template>

<script setup>
import { useMaster } from "../stores/MasterStore";
const master = useMaster();

const appStore = () => {
    if (master.appStoreLink) {
        window.open(master.appStoreLink, '_blank');
    }
}

const playStore = () => {
    if (master.playStoreLink) {
        window.open(master.playStoreLink, '_blank');
    }
}

const hiddenPopover = () => {
   open = false
}

</script>

<style scoped>
.router-link-active {
    @apply border-b-2 border-slate-950 text-slate-950
}
</style>
