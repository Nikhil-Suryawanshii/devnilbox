<template>
    <footer class="bg-[#c2410c] pt-12 pb-4 text-white border-t border-orange-800">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-start gap-8">
                    <!-- Brand -->
                    <div class="max-w-xs">
                    <div class="text-white text-2xl font-black tracking-tighter mb-4 flex items-center gap-2">
                            <img :src="master.logo || '/assets/logo.png'" class="h-8 w-auto bg-white rounded-md p-0.5" />
                    </div>
                        <p class="text-orange-100 text-sm mb-6">Global shopping made local. Connect with personal shoppers worldwide.</p>
                        
                        <!-- Contact Info -->
                        <div class="flex flex-col gap-3">
                        <a v-if="footerContact.phone" :href="'tel:' + footerContact.phone" class="flex items-center gap-3 text-sm text-orange-50 hover:text-white transition-colors">
                            <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                            </div>
                            {{ footerContact.phone }}
                        </a>
                        <a v-if="footerContact.email" :href="'mailto:' + footerContact.email" class="flex items-center gap-3 text-sm text-orange-50 hover:text-white transition-colors">
                            <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                            </div>
                            {{ footerContact.email }}
                        </a>
                        </div>
                    </div>

                    <!-- Links -->
                    <div class="flex flex-col gap-2">
                        <h4 class="font-bold text-white mb-2">Company</h4>
                        <a href="/contact-us" class="text-orange-100 hover:text-white text-sm transition-colors">Contact Us</a>
                        <a href="/legal-pages/terms-and-conditions" class="text-orange-100 hover:text-white text-sm transition-colors">Terms of Use</a>
                        <a href="/legal-pages/privacy-policy" class="text-orange-100 hover:text-white text-sm transition-colors">Privacy Policy</a>
                    </div>

                    <!-- App Downloads -->
                    <div>
                    <h4 class="font-bold text-white mb-4">Get the App</h4>
                        <div class="flex flex-col gap-3">
                            <button  v-if="master.appStoreLink" @click="openAppStore" class="bg-black text-white px-4 py-2 rounded-lg flex items-center gap-3 transition-colors border border-black/10 hover:bg-gray-900 w-full md:w-auto shadow-lg shadow-orange-900/20">
                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"><path d="M17.05 20.28c-.98.95-2.05.88-3.08.4-.36-.16-.7-.31-1.12-.31-.44 0-.79.15-1.15.31-1.03.45-2.1.51-3.08-.42-1.92-1.92-1.65-5.69.9-6.75 1.13-.53 2.05-.1 2.65-.1.57 0 1.25-.33 2.1-.33.88 0 1.63.4 2.22.8-1.55 1.05-1.4 3.7.35 4.5-.47 1.15-1 2.05-1.72 2.92-1.07 1.2-1.97 1.83-2.92 1.83zM12.03 7.25c-.15 2.23 1.88 4.02 1.88 4.02-2.55.15-4.47-1.95-4.32-4.17.14-1.97 2.15-3.8 4.35-3.63-.1 1.9-1.85 3.65-1.91 3.78z"/></svg>
                            <div class="text-left">
                                <div class="text-[10px] font-medium leading-none opacity-80 mb-0.5">Download on the</div>
                                <div class="text-sm font-bold leading-none font-sans">App Store</div>
                            </div>
                        </button>
                        <button v-if="master.playStoreLink" @click="openPlayStore" class="bg-black text-white px-4 py-2 rounded-lg flex items-center gap-3 transition-colors border border-black/10 hover:bg-gray-900 w-full md:w-auto shadow-lg shadow-orange-900/20">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M5.4 22.8L21.4 14c.7-.4.6-1.5 0-1.9L5.4 3.7c-.8-.5-1.8.1-1.8 1v17c0 1 1 1.6 1.8 1.1zM7 7.2l8.3 4.8L7 16.8V7.2z"/><path fill="#FFF" d="M3 20.5V3.5C3 2.7 3.9 2.2 4.5 2.7l12 7c.4.2.4.9 0 1.1l-12 7c-.6.5-1.5.1-1.5-.7z" opacity="0"/></svg>
                            <div class="text-left">
                                <div class="text-[10px] uppercase font-medium leading-none opacity-80 mb-0.5">Get it on</div>
                                <div class="text-sm font-bold leading-none font-sans">Google Play</div>
                            </div>
                        </button>
                        </div>
                    </div>
            </div>
            
            <div class="mt-8 pt-4 border-t border-orange-800 text-center text-orange-200 text-xs">
                &copy; 2026 {{ master.appName || 'Nilbox' }} Inc. All rights reserved.
            </div>
        </div>
    </footer>
</template>

<script setup>
import { computed } from 'vue';
import { useMaster } from '../stores/MasterStore';

const master = useMaster();

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

const footerContact = computed(() => {
    let phone = master.mobile;
    let email = null;

    if (master.footers && master.footers.length > 0) {
        for (const footer of master.footers) {
            if (footer.items && footer.items.length > 0) {
                const foundPhone = footer.items.find(item => item.type === 'phone');
                if (foundPhone) phone = foundPhone.title;

                const foundEmail = footer.items.find(item => item.type === 'email');
                if (foundEmail) email = foundEmail.title;
            }
        }
    }
    return { phone, email };
});
</script>
