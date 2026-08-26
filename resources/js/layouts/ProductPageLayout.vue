<template>
    <div class="min-h-screen bg-gray-50 text-slate-800 font-sans">
        <PublicHeader @open-download-modal="showDownloadModal = true" />

        <slot></slot>

        <!-- Download App Modal -->
        <div v-if="showDownloadModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="showDownloadModal = false"></div>
            <div class="bg-white rounded-3xl w-full max-w-md relative z-10 overflow-hidden shadow-2xl animate-fade-in-up">
                <!-- Close Button -->
                <button  @click="showDownloadModal = false" class="absolute top-4 right-4 p-2 text-slate-400 hover:text-slate-600 transition-colors z-50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <div class="p-8 text-center">
                    <h3 class="text-xl font-bold text-slate-800 mb-1">
                        You can order products through the <span class="text-orange-500">Nilbox</span>
                    </h3>
                    <p class="text-xl font-bold text-slate-800 mb-8">app</p>

                    <!-- Phone Mockup (Smaller) -->
                    <div class="relative w-[180px] h-[360px] mx-auto bg-white rounded-[2.5rem] border-[6px] border-slate-900 shadow-xl overflow-hidden mb-8">
                        <!-- Phone Screen Content -->
                        <div class="w-full h-full bg-slate-50 relative">
                                <!-- Top Bar (Notch) -->
                                <div class="absolute top-0 left-0 right-0 h-4 bg-white z-20 flex justify-center items-end pb-0.5">
                                    <div class="w-12 h-3 bg-slate-900 rounded-b-lg"></div>
                                </div>

                                <!-- Slider -->
                            <swiper
                                :modules="modules"
                                :slides-per-view="1"
                                :space-between="0"
                                :loop="true"
                                :autoplay="{ delay: 2500, disableOnInteraction: false }"
                                class="h-full w-full"
                            >
                                <swiper-slide v-for="banner in banners" :key="banner.id" class="h-full w-full">
                                    <div class="w-full h-full">
                                            <img :src="banner.thumbnail" class="w-full h-full object-cover" />
                                    </div>
                                </swiper-slide>
                            </swiper>
                                
                                <!-- Bottom Indicator -->
                            <div class="absolute bottom-1.5 left-1/2 -translate-x-1/2 w-20 h-1 bg-slate-900/20 rounded-full z-20"></div>
                        </div>
                    </div>

                    <h4 class="text-lg font-bold text-slate-700 mb-4">Download <span class="text-orange-500">Nilbox</span> app</h4>

                    <!-- App Buttons -->
                    <div class="flex items-center justify-center gap-3">
                            <button v-if="master.appStoreLink" @click="openAppStore" class="bg-black text-white px-4 py-2 rounded-lg flex items-center gap-2 hover:bg-slate-800 transition-colors">
                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"><path d="M17.05 20.28c-.98.95-2.05.88-3.08.4-.36-.16-.7-.31-1.12-.31-.44 0-.79.15-1.15.31-1.03.45-2.1.51-3.08-.42-1.92-1.92-1.65-5.69.9-6.75 1.13-.53 2.05-.1 2.65-.1.57 0 1.25-.33 2.1-.33.88 0 1.63.4 2.22.8-1.55 1.05-1.4 3.7.35 4.5-.47 1.15-1 2.05-1.72 2.92-1.07 1.2-1.97 1.83-2.92 1.83zM12.03 7.25c-.15 2.23 1.88 4.02 1.88 4.02-2.55.15-4.47-1.95-4.32-4.17.14-1.97 2.15-3.8 4.35-3.63-.1 1.9-1.85 3.65-1.91 3.78z"/></svg>
                            <div class="text-left">
                                <div class="text-[8px] leading-none opacity-80 mb-0.5">Download on the</div>
                                <div class="text-sm font-bold leading-none font-sans">App Store</div>
                            </div>
                        </button>
                        <button v-if="master.playStoreLink" @click="openPlayStore" class="bg-black text-white px-4 py-2 rounded-lg flex items-center gap-2 hover:bg-slate-800 transition-colors">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M5.4 22.8L21.4 14c.7-.4.6-1.5 0-1.9L5.4 3.7c-.8-.5-1.8.1-1.8 1v17c0 1 1 1.6 1.8 1.1zM7 7.2l8.3 4.8L7 16.8V7.2z"/><path fill="#FFF" d="M3 20.5V3.5C3 2.7 3.9 2.2 4.5 2.7l12 7c.4.2.4.9 0 1.1l-12 7c-.6.5-1.5.1-1.5-.7z" opacity="0"/></svg>
                            <div class="text-left">
                                <div class="text-[8px] uppercase leading-none opacity-80 mb-0.5">Get it on</div>
                                <div class="text-sm font-bold leading-none font-sans">Google Play</div>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <PublicFooter />
        <LoginModal />
        <MobileBottomNav />

    </div>
</template>

<script setup>
import { ref } from 'vue';
import MobileBottomNav from '../components/MobileBottomNav.vue';
import PublicHeader from '../components/NewPublicHeader.vue';
import PublicFooter from '../components/NewPublicFooter.vue';
import LoginModal from '../components/LoginModal.vue';
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Autoplay, Pagination } from 'swiper/modules';
import { useMaster } from '../stores/MasterStore';
import 'swiper/css';
import 'swiper/css/pagination';

const master = useMaster();
const showDownloadModal = ref(false);
const modules = [Autoplay, Pagination];

const banners = ref([
    { id: 1, thumbnail: '/assets/app_screens/mobile_screen_1.jpeg' },
    { id: 2, thumbnail: '/assets/app_screens/mobile_screen_2.jpeg' }
]);

const openPlayStore = () => {
    if (master.playStoreLink) {
        window.open(master.playStoreLink, '_blank');
    }
}

const openAppStore = () => {
    if (master.appStoreLink) {
        window.open(master.appStoreLink, '_blank');
    }
}

const openDownloadModal = () => {
    showDownloadModal.value = true;
};

// Expose method so parent components (like ProductDetails) can call it
defineExpose({
    openDownloadModal
});
</script>
