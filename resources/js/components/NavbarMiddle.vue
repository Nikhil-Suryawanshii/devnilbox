<template>
    <div class="main-container py-3 flex items-center justify-between gap-4 md:gap-8">
        
        <!-- LOGO & DESKTOP SEARCH -->
        <div class="flex items-center gap-6 md:gap-8 grow">
            <router-link to="/" class="flex-shrink-0 transition-transform hover:scale-105">
                <div class="bg-white p-1.5 rounded-2xl shadow-sm mix-blend-normal">
                    <img :src="master.logo" alt="Logo" class="h-8 md:h-11 object-contain">
                </div>
            </router-link>
            
            <!-- Desktop Search -->
            <div class="relative overflow-hidden grow max-w-[800px] hidden md:block group">
                <input type="text" v-model="search" :placeholder="$t('Search product')"
                    class="px-5 py-3 block w-full rounded-2xl border-2 border-transparent bg-white/60 backdrop-blur-md focus:bg-white focus:border-primary-500 placeholder:text-slate-400 outline-none text-slate-700 text-base font-medium transition-all shadow-sm group-hover:bg-white/80"
                    @keyup.enter="searchProducts()">
                <button class="bg-primary hover:bg-primary-600 transition-colors h-full px-6 absolute top-0 flex items-center justify-center shadow-md"
                    :class="master.langDirection == 'rtl' ? 'left-0 rounded-l-2xl' : 'right-0 rounded-r-2xl'"
                    @click="searchProducts()">
                    <MagnifyingGlassIcon class="w-5 h-5 text-white" />
                </button>
            </div>
        </div>

        <!-- DESKTOP ACTIONS -->
        <div class="hidden md:flex items-center justify-end gap-3 lg:gap-6">
            <div class="flex items-center gap-2">
                <button class="p-2.5 rounded-full hover:bg-white/50 transition-colors group" @click="showWishlist()">
                    <div class="w-6 h-6 relative">
                        <img :src="'/assets/icons/heart.svg'" class="w-6 h-6 text-slate-700 group-hover:scale-110 transition-transform" />
                        <span class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-rose-500 rounded-full flex items-center justify-center text-white text-[10px] font-bold shadow-sm ring-2 ring-white">
                            {{ AuthStore.favoriteProducts }}
                        </span>
                    </div>
                </button>

                <button class="p-2.5 rounded-full hover:bg-white/50 transition-colors group" @click="master.basketCanvas = true">
                    <div class="w-6 h-6 relative">
                        <img :src="'/assets/icons/bag.svg'" class="w-6 h-6 text-slate-700 group-hover:scale-110 transition-transform" />
                        <span class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-rose-500 rounded-full flex items-center justify-center text-white text-[10px] font-bold shadow-sm ring-2 ring-white">
                            {{ basketStore.total }}
                        </span>
                    </div>
                </button>
            </div>

            <div class="h-8 w-px bg-slate-200/50 mx-1"></div>

            <button v-if="!AuthStore.user" class="flex items-center gap-2.5 px-4 py-2 rounded-xl text-slate-800 font-medium hover:bg-white/60 transition-all shadow-sm backdrop-blur-sm"
                @click="showLoginDialog">
                <UserIcon class="w-5 h-5" />
                <span>{{ $t('Login') }}</span>
            </button>
            <div v-else>
                <AuthUserDropdown />
            </div>
        </div>

        <!-- MOBILE NAV -->
        <div class="md:hidden flex items-center gap-2.5 relative">
            
            <button class="w-10 h-10 flex items-center justify-center bg-white/70 hover:bg-white rounded-full shadow-sm backdrop-blur-sm transition-all text-slate-800" @click="toggleSearch">
                <MagnifyingGlassIcon class="w-5 h-5" />
            </button>

            <button class="w-10 h-10 flex items-center justify-center bg-white/70 hover:bg-white rounded-full shadow-sm backdrop-blur-sm transition-all relative text-slate-800" @click="master.basketCanvas = true">
                <img :src="'/assets/icons/bag.svg'" class="w-5 h-5" />
                <span class="absolute -top-1 -right-1 w-4.5 h-4.5 bg-rose-500 rounded-full flex items-center justify-center text-white text-[10px] font-bold shadow-sm ring-2 ring-white" style="width: 18px; height: 18px;">
                    {{ basketStore.total }}
                </span>
            </button>

            <button class="w-10 h-10 flex items-center justify-center bg-white/70 hover:bg-white rounded-full shadow-sm backdrop-blur-sm transition-all text-slate-800" @click="mobileMenuOpen = true">
                <Bars3Icon class="w-5 h-5" />
            </button>

            <!-- Search Modal (Mobile) -->
            <TransitionRoot as="template" :show="showSearch">
                <Dialog class="relative z-[100]" @close="showSearch = false">
                    <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0"
                        enter-to="opacity-100" leave="ease-in duration-200" leave-from="opacity-100"
                        leave-to="opacity-0">
                        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" />
                    </TransitionChild>

                    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                        <div class="flex min-h-full items-start justify-center p-4 pt-20 text-center sm:p-0">
                            <TransitionChild as="template" enter="ease-out duration-300"
                                enter-from="opacity-0 -translate-y-4"
                                enter-to="opacity-100 translate-y-0" leave="ease-in duration-200"
                                leave-from="opacity-100 translate-y-0"
                                leave-to="opacity-0 -translate-y-4">
                                <DialogPanel class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all w-full max-w-md border border-slate-100">
                                    <div class="p-4 border-b border-slate-100 flex items-center gap-3">
                                        <div class="relative grow">
                                            <MagnifyingGlassIcon class="w-5 h-5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" />
                                            <input type="text" v-model="search" :placeholder="$t('Search product')"
                                                class="pl-10 pr-4 py-3 block w-full rounded-xl bg-slate-50 border-transparent focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 placeholder:text-slate-400 outline-none text-base transition-all"
                                                @keyup.enter="showSearch = false; searchProducts()" autofocus />
                                        </div>
                                        <button type="button" class="p-2 rounded-full hover:bg-slate-100 text-slate-500 transition-colors" @click="showSearch = false">
                                            <XMarkIcon class="w-6 h-6" />
                                        </button>
                                    </div>
                                </DialogPanel>
                            </TransitionChild>
                        </div>
                    </div>
                </Dialog>
            </TransitionRoot>

            <!-- Mobile Menu Drawer -->
            <TransitionRoot as="template" :show="mobileMenuOpen">
                <Dialog as="div" class="relative z-[100]" @close="mobileMenuOpen = false">
                    <TransitionChild as="template" enter="ease-in-out duration-300" enter-from="opacity-0"
                        enter-to="opacity-100" leave="ease-in-out duration-300" leave-from="opacity-100"
                        leave-to="opacity-0">
                        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" />
                    </TransitionChild>

                    <div class="fixed inset-0 overflow-hidden">
                        <div class="absolute inset-0 overflow-hidden">
                            <div class="pointer-events-none fixed inset-y-0 flex max-w-full"
                                :class="master.langDirection == 'rtl' ? 'left-0' : 'right-0'">
                                <TransitionChild as="template"
                                    enter="transform transition ease-out duration-300"
                                    :enter-from="master.langDirection == 'rtl' ? '-translate-x-full' : 'translate-x-full'"
                                    enter-to="translate-x-0"
                                    leave="transform transition ease-in duration-300"
                                    leave-from="translate-x-0"
                                    :leave-to="master.langDirection == 'rtl' ? '-translate-x-full' : 'translate-x-full'">
                                    <DialogPanel class="pointer-events-auto relative w-screen max-w-[85vw] sm:max-w-sm">
                                        
                                        <div class="flex h-full flex-col overflow-y-auto bg-white shadow-2xl pb-6">
                                            <!-- Header -->
                                            <div class="flex justify-between items-center px-5 py-4 border-b border-slate-100 sticky top-0 bg-white z-10">
                                                <div class="text-slate-900 text-lg font-bold tracking-tight">{{ $t('Menu') }}</div>
                                                <button class="p-2 rounded-full bg-slate-100 hover:bg-slate-200 transition-colors" @click="mobileMenuOpen = false">
                                                    <XMarkIcon class="w-5 h-5 text-slate-700" />
                                                </button>
                                            </div>

                                            <div class="px-5">
                                                <!-- Auth Section -->
                                                <div v-if="!AuthStore.user" class="mt-6">
                                                    <button @click="showLoginDialog" class="w-full flex items-center justify-between p-4 bg-primary/10 hover:bg-primary/15 rounded-2xl transition-colors border border-primary/20">
                                                        <div class="flex items-center gap-3">
                                                            <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center shadow-sm">
                                                                <UserIcon class="w-5 h-5 text-white" />
                                                            </div>
                                                            <div class="text-primary-700 font-semibold">{{ $t('Login / Register') }}</div>
                                                        </div>
                                                        <ChevronRightIcon class="w-5 h-5 text-primary" />
                                                    </button>
                                                </div>
                                                <div v-else class="mt-6">
                                                    <AuthUserDropdown />
                                                </div>

                                                <!-- Quick Actions -->
                                                <div class="grid grid-cols-2 gap-3 mt-6">
                                                    <button @click="showWishlist()" class="flex flex-col items-center gap-2 p-4 bg-slate-50 rounded-2xl hover:bg-slate-100 transition-colors border border-slate-100">
                                                        <div class="relative">
                                                            <img :src="'/assets/icons/heart.svg'" class="w-6 h-6 text-slate-700" />
                                                            <span class="absolute -top-1.5 -right-1.5 w-4.5 h-4.5 bg-rose-500 rounded-full flex items-center justify-center text-white text-[10px] font-bold shadow-sm ring-2 ring-white" style="width: 18px; height: 18px;">
                                                                {{ AuthStore.favoriteProducts }}
                                                            </span>
                                                        </div>
                                                        <span class="text-sm font-medium text-slate-700">{{ $t('Wishlist') }}</span>
                                                    </button>

                                                    <button @click="showMyCart()" class="flex flex-col items-center gap-2 p-4 bg-slate-50 rounded-2xl hover:bg-slate-100 transition-colors border border-slate-100">
                                                        <div class="relative">
                                                            <img :src="'/assets/icons/bag.svg'" class="w-6 h-6 text-slate-700" />
                                                            <span class="absolute -top-1.5 -right-1.5 w-4.5 h-4.5 bg-rose-500 rounded-full flex items-center justify-center text-white text-[10px] font-bold shadow-sm ring-2 ring-white" style="width: 18px; height: 18px;">
                                                                {{ basketStore.total }}
                                                            </span>
                                                        </div>
                                                        <span class="text-sm font-medium text-slate-700">{{ $t('My Cart') }}</span>
                                                    </button>
                                                </div>

                                                <!-- Navigation Links -->
                                                <div class="mt-8">
                                                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">{{ $t('Navigation') }}</h3>
                                                    <div class="flex flex-col gap-1">
                                                        <div v-for="menu in master.menus" :key="menu.id">
                                                            <router-link v-if="!menu.is_external" :to="menu.url"
                                                                class="flex items-center justify-between px-4 py-3.5 rounded-xl font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                                                                {{ menu.name }}
                                                                <ChevronRightIcon class="w-4 h-4 text-slate-300" />
                                                            </router-link>
                                                            <a v-else :href="menu.url" :target="menu.target"
                                                                class="flex items-center justify-between px-4 py-3.5 rounded-xl font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                                                                {{ menu.name }}
                                                                <ChevronRightIcon class="w-4 h-4 text-slate-300" />
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </DialogPanel>
                                </TransitionChild>
                            </div>
                        </div>
                    </div>
                </Dialog>
            </TransitionRoot>
        </div>

    </div>
    <!-- Login Dialog Modal -->
    <LoginModal />
    <!-- End Login Dialog Modal -->
</template>

<script setup>
import { Dialog, DialogPanel, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { Bars3Icon, ChevronRightIcon, UserIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { MagnifyingGlassIcon } from '@heroicons/vue/24/solid'
import { ref, watch, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import AuthUserDropdown from './AuthUserDropdown.vue'
import LoginModal from './LoginModal.vue'

import { useAuth } from '../stores/AuthStore'
import { useBasketStore } from '../stores/BasketStore'
import { useMaster } from '../stores/MasterStore'

const route = useRoute();
const router = useRouter();
const basketStore = useBasketStore();

const AuthStore = useAuth();
const master = useMaster();

const search = ref('');
const showSearch = ref(false);

const toggleSearch = () => {
    showSearch.value = !showSearch.value
}

const showMyCart = () => {
    mobileMenuOpen.value = false;
    master.basketCanvas = true
}

const showWishlist = () => {
    mobileMenuOpen.value = false;
    if (!AuthStore.token) {
        return showLoginDialog();
    }
    router.push('/wishlist')
}

watch(() => route.path, () => {
    mobileMenuOpen.value = false;
    if (route.path == '/products') {
        search.value = master.search
    } else {
        search.value = ''
    }
});

onMounted(() => {
    if (route.path == '/products') {
        search.value = master.search
    } else {
        search.value = ''
    }
});

const mobileMenuOpen = ref(false);

const showLoginDialog = () => {
    mobileMenuOpen.value = false;
    AuthStore.showLoginModal();
}

const searchProducts = () => {
    master.search = search.value
    if (route.path != '/products') {
        search.value = '';
    }
    router.push({ name: 'products' })
}

</script>

<style scoped>
.router-link-active {
    @apply border-primary text-primary
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
