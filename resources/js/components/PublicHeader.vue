<template>
    <header class="fixed top-0 left-0 right-0 z-50 bg-white/90 backdrop-blur-md border-b border-orange-100 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 lg:px-6 h-20 flex items-center justify-between gap-6">
            <!-- Left: Logo -->
            <div class="flex items-center gap-2">
                <a href="/" class="text-slate-900 text-2xl font-black tracking-tighter flex items-center gap-1">
                    <img v-if="master.logo" :src="master.logo" class="h-8 object-contain" />
                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-orange-500" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2L2 7l10 5 10-5-10-5zm0 9l2.5-1.25L12 8.5l-2.5 1.25L12 11zm0 2.25l-5-2.5-5 2.5L12 22l10-8.75-5-2.5-5 2.5z"/>
                    </svg>
                </a>
            </div>

            <!-- Center: Navigation Links -->
            <nav class="hidden md:flex items-center gap-8 text-sm font-bold text-slate-700">
                <a href="/shop/register" class="flex items-center gap-2 hover:text-orange-600 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    Become A Seller
                </a>
                <a href="/shop/login" class="flex items-center gap-2 hover:text-orange-600 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                    Seller Login datase
                </a>
            </nav>
            
            <!-- Right: Actions -->
            <div class="flex items-center gap-3">
                <button @click="$emit('open-download-modal')" class="flex items-center gap-2 px-5 py-2.5 bg-orange-500 text-white text-sm font-bold rounded-lg hover:bg-orange-600 transition-colors shadow-lg shadow-orange-500/20">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                    {{ $t("Download") }} 
                </button>
            

               <!-- Wishlist -->
                <button
                    @click="showWishlist()"
                    class="relative w-12 h-12 rounded-2xl bg-[#f7f7f7] hidden md:flex items-center justify-center text-slate-700 hover:bg-gray-100 transition-all"
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
                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
                        />
                    </svg>

                    <span
                        class="absolute -top-1 -right-1 w-5 h-5 rounded-full bg-orange-500 text-white text-[10px] font-bold flex items-center justify-center"
                    >
                        {{ authStore.favoriteProducts }}
                    </span>
                </button>

                <!-- Cart -->
                <button @click="showMyCart()"
                    class="relative w-12 h-12 rounded-2xl bg-[#f7f7f7] flex items-center justify-center text-slate-700 hover:bg-gray-100 transition-all"
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
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4m1.6 8L5.4 5M7 13l-1.2 6h12.4M10 21a1 1 0 100-2 1 1 0 000 2zm8 0a1 1 0 100-2 1 1 0 000 2z"
                        />
                    </svg>

                    <span
                        class="absolute -top-1 -right-1 w-5 h-5 rounded-full bg-orange-500 text-white text-[10px] font-bold flex items-center justify-center"
                    > {{ basketStore.total }}
                    </span>
                </button>

                <div
                    class="relative"
                    @mouseenter="showProfileMenu = true"
                    @mouseleave="showProfileMenu = false"
                >
                    <button
                        @click="login"
                        class="w-12 h-12 rounded-2xl bg-[#f7f7f7] overflow-hidden flex items-center justify-center hover:bg-gray-100 transition"
                    >
                        <img
                            v-if="authStore.token && authStore.user?.image"
                            :src="authStore.user.image"
                            class="w-full h-full object-cover"
                        />

                        <span
                            v-else-if="authStore.token"
                            class="font-semibold text-orange-500"
                        >
                            {{ authStore.user?.name?.charAt(0).toUpperCase() }}
                        </span>

                        <svg
                            v-else
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
                                d="M5.121 17.804A9 9 0 1118.88 17.8M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                            />
                        </svg>
                    </button>

                    <!-- Dropdown -->
                    <transition
                        enter-active-class="transition duration-200"
                        leave-active-class="transition duration-150"
                        enter-from-class="opacity-0 scale-95"
                        enter-to-class="opacity-100 scale-100"
                        leave-from-class="opacity-100 scale-100"
                        leave-to-class="opacity-0 scale-95"
                    >
                        <div
                            v-if="authStore.token && showProfileMenu"
                            class="absolute right-0 mt-3 w-52 bg-white rounded-2xl shadow-xl border z-50"
                        >
                            <router-link
                                to="/profile"
                                class="block px-5 py-3 hover:bg-gray-50"
                            >
                                👤 My Profile
                            </router-link>

                            <router-link
                                to="/order-history"
                                class="block px-5 py-3 hover:bg-gray-50"
                            >
                                📦 My Orders
                            </router-link>
                            
                            <button
                                @click="logout"
                                class="w-full text-left px-5 py-3 text-red-600 hover:bg-red-50 rounded-b-2xl"
                            >
                                🚪 Logout
                            </button>
                        </div>
                    </transition>
                </div>
                <!-- Mobile Menu -->


                </div>
        </div>
    </header>
</template>

<script setup>
import { useMaster } from '../stores/MasterStore';
import { MagnifyingGlassIcon } from '@heroicons/vue/24/solid';
import { ref, watch, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuth } from '../stores/AuthStore';
import { useBasketStore } from '../stores/BasketStore'
const basketStore = useBasketStore();
const master = useMaster();

const route = useRoute()
const router = useRouter()
const authStore = useAuth();
const showProfileMenu = ref(false);

const search = ref('')

const logout = () => {
    authStore.logout();
    router.push("/");
};

const login = async () => {
    if (authStore.token === null) {
        console.log(authStore.token);
        return authStore.loginModal = true;
    }

    router.push({ name: 'profile' })
};

const showMyCart = () => {
    master.basketCanvas = true
    if (authStore.token === null) {
        return authStore.loginModal = true;
    }
    router.push('/dashboard')
}

const showWishlist = () => {
    if (authStore.token === null) {
        return authStore.loginModal = true;
    }
    router.push('/wishlist')
}

</script>
