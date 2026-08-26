<template>
    <header
        class="fixed top-0 left-0 right-0 z-50 bg-white/90 backdrop-blur-xl border-b border-gray-100"
    >
        <div
            class="max-w-7xl mx-auto px-4 lg:px-6 h-20 flex items-center justify-between gap-6"
        >
            <!-- Logo -->
            <a
                href="/"
                class="flex items-center shrink-0"
            >
                <img
                    v-if="master.logo"
                    :src="master.logo"
                    class="h-9 object-contain"
                />

                <div
                    v-else
                    class="text-3xl font-black tracking-tight text-slate-900"
                >
                    nil<span class="text-orange-500">box</span>
                </div>
            </a>


            <!-- Search -->
            <div class="hidden lg:flex flex-1 max-w-2xl">
                <div class="relative w-full overflow-hidden">

                    <input
                        type="text"
                        v-model="search"
                        :placeholder="$t('Search products, brands, styles...')"
                        class="w-full h-14 pl-14 pr-16 rounded-2xl bg-[#f7f7f7] border border-transparent focus:border-orange-400 focus:ring-4 focus:ring-orange-100 outline-none text-sm font-medium text-slate-700 placeholder:text-slate-400 transition-all"
                        @keydown.enter="searchProducts"
                    />

                    <!-- Search Icon -->
                    <MagnifyingGlassIcon
                        class="absolute left-5 top-1/2 -translate-y-1/2 h-5 w-5 text-slate-400"
                    />

                    <!-- Search Button -->
                    <button
                        @click="searchProducts()"
                        class="absolute right-2 top-1/2 -translate-y-1/2 h-10 px-5 rounded-xl bg-orange-500 hover:bg-orange-600 transition-all flex items-center justify-center"
                    >
                        <MagnifyingGlassIcon class="w-5 h-5 text-white" />
                    </button>

                </div>
            </div>

            <!-- Right Actions -->
            <div class="flex items-center gap-3 shrink-0">

                <!-- Download -->
                <button
                    @click="$emit('open-download-modal')"
                    class="flex items-center justify-center gap-2 px-6 h-12 rounded-2xl bg-orange-500 text-white text-sm font-bold hover:bg-orange-600 transition-all shadow-lg shadow-orange-500/20"
                ><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                   {{ $t("Download") }}
                </button>

                <!-- Sell -->
                <a
                    href="/shop/login"
                    class="hidden md:flex items-center justify-center px-6 h-12 rounded-2xl bg-orange-500 text-white text-sm font-bold hover:bg-orange-600 transition-all shadow-lg shadow-orange-500/20"
                >
                    {{ $t("Sell Now") }}
                </a>

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

                <!-- Profile -->
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
               <!--  <button
                    class="lg:hidden w-12 h-12 rounded-2xl bg-[#f7f7f7] flex items-center justify-center"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"
                        />
                    </svg>
                </button> -->
            </div>
        </div>

        <!-- Mobile Search -->
        <div class="lg:hidden px-4 pb-4">
            <div class="relative">
                <input
                    type="text"
                    v-model="search"
                    placeholder="Search products..."
                    class="w-full h-12 pl-12 pr-4 rounded-2xl bg-[#f7f7f7] border border-transparent focus:border-orange-400 focus:ring-4 focus:ring-orange-100 outline-none text-sm"
                    @keydown.enter="searchProducts"

                />

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-slate-400"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z"
                    />
                </svg>
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

const route = useRoute()
const router = useRouter()
const authStore = useAuth();
const showProfileMenu = ref(false);
const search = ref('')
const master = useMaster();

const logout = () => {
    authStore.logout();
    router.push("/");
};

const searchProducts = () => {
    master.search = search.value

    if (route.path != '/products') {
        search.value = ''
    }

    router.push({ name: 'products' })
}

const login = async () => {
    if (authStore.token === null) {
        return authStore.loginModal = true;
    }

    router.push({ name: 'profile' })
};

const showMyCart = () => {
    master.basketCanvas = true
    if (authStore.token === null) {
        return authStore.loginModal = true;
    }
    router.push('/wishlist')
}

const showWishlist = () => {
    if (authStore.token === null) {
        return authStore.loginModal = true;
    }
    router.push('/wishlist')
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