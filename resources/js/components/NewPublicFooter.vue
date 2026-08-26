<template>
    <footer class="bg-[#f5f5f5] border-t border-gray-200 mt-20">
        <div class="max-w-7xl mx-auto px-4 lg:px-6 py-16">

            <!-- Top Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10">

                <!-- Brand -->
                <div class="lg:col-span-2">
                    <a
                        href="/"
                        class="inline-flex items-center mb-5"
                    >
                        <img
                            v-if="master.logo"
                            :src="master.logo"
                            class="h-10 object-contain"
                        />

                        <div
                            v-else
                            class="text-3xl font-black tracking-tight text-slate-900"
                        >
                            nil<span class="text-orange-500">box</span>
                        </div>
                    </a>

                    <p class="text-gray-500 text-sm leading-7 max-w-md mb-6">
                        {{ $t('Global shopping made simple. Discover premium products, connect with trusted sellers, and shop worldwide with confidence.') }}
                    </p>

                    <!-- Contact -->
                    <div class="flex flex-col gap-4">

                        <a
                            v-if="footerContact.phone"
                            :href="'tel:' + footerContact.phone"
                            class="flex items-center gap-3 text-sm text-slate-700 hover:text-orange-500 transition-colors"
                        >
                            <div
                                class="w-10 h-10 rounded-2xl bg-white border border-gray-200 flex items-center justify-center"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"
                                    />
                                </svg>
                            </div>

                            {{ footerContact.phone }}
                        </a>

                        <a
                            v-if="footerContact.email"
                            :href="'mailto:' + footerContact.email"
                            class="flex items-center gap-3 text-sm text-slate-700 hover:text-orange-500 transition-colors"
                        >
                            <div
                                class="w-10 h-10 rounded-2xl bg-white border border-gray-200 flex items-center justify-center"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                    />
                                </svg>
                            </div>

                            {{ footerContact.email }}
                        </a>
                    </div>
                </div>

                <!-- Company -->
                <div>
                    <h4 class="text-lg font-bold text-slate-900 mb-5">
                        {{ $t("Company") }}
                    </h4>

                    <div class="flex flex-col gap-4">
                        <a
                            href="/about-us"
                            class="text-sm text-gray-500 hover:text-orange-500 transition-colors"
                        >
                            {{ $t("About") }}
                        </a>

                        <a
                            href="/contact-us"
                            class="text-sm text-gray-500 hover:text-orange-500 transition-colors"
                        >
                            {{ $t("Contact Us") }}
                        </a>

                        <a
                            href="/page/careers"
                            class="text-sm text-gray-500 hover:text-orange-500 transition-colors"
                        >
                            {{ $t("Careers") }}
                        </a>

                        <a
                            href="/blogs"
                            class="text-sm text-gray-500 hover:text-orange-500 transition-colors"
                        >
                            {{ $t("Blog") }}
                        </a>
                    </div>
                </div>

                <!-- Support -->
                <div>
                    <h4 class="text-lg font-bold text-slate-900 mb-5">
                        Support
                    </h4>

                    <div class="flex flex-col gap-4">
                        <a
                            href="/page/help-center"
                            class="text-sm text-gray-500 hover:text-orange-500 transition-colors"
                        >
                            {{ $t("Help Center") }}
                        </a>

                        <a
                            href="/privacy-policy"
                            class="text-sm text-gray-500 hover:text-orange-500 transition-colors"
                        >
                            {{ $t("Privacy Policy") }}
                        </a>

                        <a
                            href="/terms-and-conditions"
                            class="text-sm text-gray-500 hover:text-orange-500 transition-colors"
                        >
                            {{ $t("Terms & Conditions") }}
                        </a>

                        <a
                            href="/page/return-and-refund-policy"
                            class="text-sm text-gray-500 hover:text-orange-500 transition-colors"
                        >
                            {{ $t("Returns") }}
                        </a>
                    </div>
                </div>

                <!-- Download App -->
                <div>
                    <h4 class="text-lg font-bold text-slate-900 mb-5">
                        Download App
                    </h4>

                    <div class="flex flex-col gap-4">

                        <!-- App Store -->
                        <button v-if="master.appStoreLink"
                            @click="openAppStore"
                            class="bg-black text-white rounded-2xl px-4 py-3 flex items-center gap-4 hover:scale-[1.02] transition-all shadow-lg"
                        >
                            <svg
                                class="w-7 h-7"
                                viewBox="0 0 24 24"
                                fill="currentColor"
                            >
                                <path d="M17.05 20.28c-.98.95-2.05.88-3.08.4-.36-.16-.7-.31-1.12-.31-.44 0-.79.15-1.15.31-1.03.45-2.1.51-3.08-.42-1.92-1.92-1.65-5.69.9-6.75 1.13-.53 2.05-.1 2.65-.1.57 0 1.25-.33 2.1-.33.88 0 1.63.4 2.22.8-1.55 1.05-1.4 3.7.35 4.5-.47 1.15-1 2.05-1.72 2.92-1.07 1.2-1.97 1.83-2.92 1.83zM12.03 7.25c-.15 2.23 1.88 4.02 1.88 4.02-2.55.15-4.47-1.95-4.32-4.17.14-1.97 2.15-3.8 4.35-3.63-.1 1.9-1.85 3.65-1.91 3.78z"/>
                            </svg>

                            <div class="text-left">
                                <div class="text-[11px] opacity-70">
                                    {{ $t("Download on the") }}
                                </div>

                                <div class="font-bold text-sm">
                                    {{ $t("App Store") }}
                                </div>
                            </div>
                        </button>

                        <!-- Play Store -->
                        <button v-if="master.playStoreLink"
                            @click="openPlayStore"
                            class="bg-black text-white rounded-2xl px-4 py-3 flex items-center gap-4 hover:scale-[1.02] transition-all shadow-lg"
                        >
                            <svg
                                class="w-6 h-6"
                                viewBox="0 0 24 24"
                                fill="currentColor"
                            >
                                <path d="M5.4 22.8L21.4 14c.7-.4.6-1.5 0-1.9L5.4 3.7c-.8-.5-1.8.1-1.8 1v17c0 1 1 1.6 1.8 1.1z"/>
                            </svg>

                            <div class="text-left">
                                <div class="text-[11px] opacity-70">
                                    {{ $t("GET IT ON") }}
                                </div>

                                <div class="font-bold text-sm">
                                    {{ $t("Google Play") }}
                                </div>
                            </div>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Bottom -->
            <div
                class="mt-14 pt-6 border-t border-gray-200 flex flex-col md:flex-row items-center justify-between gap-4"
            >
                <p class="text-sm text-gray-500 text-center md:text-left">
                    © 2026 {{ master.appName || 'Nilbox' }}. All rights reserved.
                </p>

                <!-- Social -->
                <div class="flex items-center gap-3">
                   <div v-for="(footer, index) in master.footers" :key="footer.id">
                        <div v-for="item in footer.items" :key="item.id">
                            <div v-if="item.type == 'social_links'" class="flex justify-start pt-6 items-center gap-6">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <a v-for="socialLink in master.socialLinks" :key="socialLink.name" target="_blank"
                                        :href="socialLink.link" class="w-[30px] h-[30px] overflow-hidden" :title="socialLink.name">
                                        <img :src="socialLink.logo" alt="" class="w-full h-full object-cover">
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- <a
                        href="#"
                        class="w-10 h-10 rounded-2xl bg-white border border-gray-200 flex items-center justify-center hover:bg-orange-500 hover:text-white transition-all"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            fill="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path d="M22 12a10 10 0 10-11.56 9.88v-6.99H7.9V12h2.54V9.8c0-2.5 1.5-3.89 3.78-3.89 1.09 0 2.23.2 2.23.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56V12h2.77l-.44 2.89h-2.33v6.99A10 10 0 0022 12z"/>
                        </svg>
                    </a>

                    <a
                        href="#"
                        class="w-10 h-10 rounded-2xl bg-white border border-gray-200 flex items-center justify-center hover:bg-orange-500 hover:text-white transition-all"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            fill="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path d="M7.75 2C4.57 2 2 4.57 2 7.75v8.5C2 19.43 4.57 22 7.75 22h8.5C19.43 22 22 19.43 22 16.25v-8.5C22 4.57 19.43 2 16.25 2h-8.5zm0 1.8h8.5c2.18 0 3.95 1.77 3.95 3.95v8.5c0 2.18-1.77 3.95-3.95 3.95h-8.5A3.95 3.95 0 013.8 16.25v-8.5C3.8 5.57 5.57 3.8 7.75 3.8zm8.9 1.35a.95.95 0 100 1.9.95.95 0 000-1.9zM12 6.85A5.15 5.15 0 106.85 12 5.16 5.16 0 0012 6.85zm0 1.8A3.35 3.35 0 118.65 12 3.35 3.35 0 0112 8.65z"/>
                        </svg>
                    </a>

                    <a
                        href="#"
                        class="w-10 h-10 rounded-2xl bg-white border border-gray-200 flex items-center justify-center hover:bg-orange-500 hover:text-white transition-all"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            fill="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path d="M22.46 6c-.77.35-1.6.58-2.46.69a4.3 4.3 0 001.88-2.37 8.59 8.59 0 01-2.72 1.04 4.28 4.28 0 00-7.29 3.9A12.15 12.15 0 013 4.79a4.28 4.28 0 001.32 5.71 4.22 4.22 0 01-1.94-.54v.05a4.28 4.28 0 003.43 4.2 4.3 4.3 0 01-1.93.07 4.29 4.29 0 004 2.97A8.6 8.6 0 012 19.54 12.12 12.12 0 008.56 21c7.87 0 12.18-6.52 12.18-12.18 0-.19 0-.37-.01-.56A8.68 8.68 0 0022.46 6z"/>
                        </svg>
                    </a> -->

                </div>
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
};

const openAppStore = () => {
    if (master.appStoreLink) {
        window.open(master.appStoreLink, '_blank');
    }
};

const footerContact = computed(() => {
    let phone = master.mobile;
    let email = null;

    if (master.footers && master.footers.length > 0) {
        for (const footer of master.footers) {
            if (footer.items && footer.items.length > 0) {
                const foundPhone = footer.items.find(
                    item => item.type === 'phone'
                );

                if (foundPhone) {
                    phone = foundPhone.title;
                }

                const foundEmail = footer.items.find(
                    item => item.type === 'email'
                );

                if (foundEmail) {
                    email = foundEmail.title;
                }
            }
        }
    }

    return { phone, email };
});
</script>