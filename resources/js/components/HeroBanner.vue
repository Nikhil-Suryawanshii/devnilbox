<template>
    <div v-if="!isLoading" class="main-container mt-3 relative grid grid-cols-4 gap-3 lg:gap-8" :dir="master.langDirection || 'ltr'">
        <div class="col-span-4">
            <!-- Phone Mockup Overlay -->
            <div
            class="hidden md:block absolute z-10
                    left-14 md:left-20
                    top-1/2 -translate-y-1/2
                    w-[160px] md:w-[190px]
                    aspect-[9/19]
                    rounded-[28px]
                    bg-slate-900/90
                    shadow-xl
                    p-[5px]"
            >
                <!-- Phone Screen -->
                <div class="w-full h-full rounded-[24px] bg-white flex flex-col items-center justify-center gap-4 p-4">
                    <!-- Logo -->
                    <img
                    :src="master.logo || '/public/assets/logo.png'"
                    alt="Nilbox App"
                    class="h-10 object-contain"
                    />

                    <!-- Download Button -->
                   <div class="flex flex-grid gap-0 mt-4">
                    <button @click="playStore">
                        <img src="/public/assets/icons/playStore.png" alt="Play Store" class="block w-full" />
                    </button>

                    <button @click="appStore">
                        <img src="/public/assets/icons/appleStore.png" alt="App Store" class="block w-full" />
                    </button>
                </div>
                </div>
            </div>

            <!-- Main Banner Slider -->
            <swiper
                :navigation="true"
                :pagination="{ clickable: true }"
                :slides-per-view="1"
                :space-between="20"
                :modules="modules"
                class="mySwiper rounded-lg h-[360px] md:h-[420px]"
                :loop="false"
                :autoplay="{
                    delay: 4000,
                    disableOnInteraction: false
                }"
            >

                <swiper-slide v-for="banner in banners" :key="banner.id">
                    <img :src="banner.thumbnail" loading="lazy" class="w-full rounded-lg object-cover aspect-[16/7]" />
                </swiper-slide>
            </swiper>
        </div>
    </div>

    <!-- Skeleton loader -->
    <div v-else class="main-container mt-3 grid grid-cols-4 gap-3 lg:gap-8">
        <div class="col-span-4 lg:col-span-3">
            <div class="w-full aspect-[16/7] object-cover rounded-lg">
                <SkeletonLoader class="w-full h-full object-cover rounded-lg" />
            </div>
        </div>
    </div>
</template>

<script setup>
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Navigation, Pagination, A11y, Autoplay } from 'swiper/modules';
import { useMaster } from '../stores/MasterStore';
const master = useMaster();
const playStore = () => {
    if (master.playStoreLink) {
        window.open(master.playStoreLink, '_blank');
    }
}

const appStore = () => {
    if (master.appStoreLink) {
        window.open(master.appStoreLink, '_blank');
    }
}
import SkeletonLoader from './SkeletonLoader.vue';

// Import Swiper styles
import 'swiper/css';

import 'swiper/css/navigation';
import 'swiper/css/pagination';

const modules = [
    Navigation, Pagination, A11y, Autoplay
];

const props = defineProps({
    banners: Array,
    ads: Array,
    isLoading: {
        type: Boolean,
        default: true
    }
})

</script>

<style>
.mySwiper .swiper-button-prev,
.mySwiper .swiper-button-next {
    position: absolute;
    width: 28px;
    height: 28px;
    background-color: #fff;
    color: #475569 !important;
    border-radius: 8px !important;
    margin-top: auto;
}

.mySwiper .swiper-button-next {
    left: auto;
    right: 20px;
    bottom: 20px;
}

.mySwiper .swiper-button-prev {
    left: auto;
    right: 58px;
    bottom: 20px;
}

.mySwiper .swiper-button-prev:after,
.mySwiper .swiper-button-next:after {
    font-size: 16px !important;
}

.mySwiper .swiper-pagination-bullet-active {
    @apply bg-primary w-6 h-2 rounded;
}
</style>
