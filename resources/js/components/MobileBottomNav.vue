<template>
    <div class="fixed bottom-0 left-0 z-50 w-full h-16 bg-white border-t border-gray-200 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)] rounded-t-2xl md:hidden">
        <div class="grid h-full max-w-lg grid-cols-5 mx-auto font-medium">
            
            <!-- Home -->
            <router-link to="/" class="inline-flex flex-col items-center justify-center px-5 group" active-class="text-primary">
                <HomeIcon class="w-6 h-6 mb-1 text-gray-500 group-hover:text-primary group-active:text-primary" :class="$route.path === '/' ? 'text-primary' : ''" />
                <span class="text-xs text-gray-500 group-hover:text-primary" :class="$route.path === '/' ? 'text-primary' : ''">{{ $t('Home') }}</span>
            </router-link>

            <!-- Chats (Placeholder link) -->
            <button  @click="showMessages"  class="inline-flex flex-col items-center justify-center px-5 group" active-class="text-primary">
                <ChatBubbleLeftRightIcon class="w-6 h-6 mb-1 text-gray-500 group-hover:text-primary" />
                <span class="text-xs text-gray-500 group-hover:text-primary">{{ $t('Chats') }}</span>
            </button>

            <!-- Sell (Middle Floating Button) -->
            <div class="flex items-center justify-center relative">
                 <a
                    href="/shop/login"
                    class="inline-flex items-center justify-center w-14 h-14 font-medium bg-primary rounded-full hover:bg-primary-600 group focus:ring-4 focus:ring-primary-300 focus:outline-none absolute -top-5 border-4 border-white shadow-lg"
                >
                    <CameraIcon class="w-6 h-6 text-white" />
                    <span class="sr-only">{{ $t('Sell') }}</span>
                </a>
                <span class="text-xs text-gray-500 absolute bottom-1">{{ $t('Sell') }}</span>
            </div>

            <!-- Shops -->
            <router-link to="/shops" class="inline-flex flex-col items-center justify-center px-5 group" active-class="text-primary">
                <BuildingStorefrontIcon class="w-6 h-6 mb-1 text-gray-500 group-hover:text-primary" />
                <span class="text-xs text-gray-500 group-hover:text-primary">{{ $t('Shops') }}</span>
            </router-link>

            <!-- Favourites -->
            <button @click="showWishlist"
                    class="inline-flex flex-col items-center justify-center px-5 group" active-class="text-primary">

                <HeartIcon class="w-6 h-6 mb-1 text-gray-500 group-hover:text-primary" />
                <span class="text-xs text-gray-500 group-hover:text-primary">{{ $t('Favourites') }}</span>
                <span
                        class="absolute -top-1 right-[1.5rem] w-5 h-5 rounded-full bg-orange-500 text-white text-[10px] font-bold flex items-center justify-center"
                    >
                        {{ authStore.favoriteProducts }}
                    </span>
            </button>

        </div>
    </div>
</template>

<script setup>
import { 
    HomeIcon, 
    ChatBubbleLeftRightIcon, 
    CameraIcon, 
    BuildingStorefrontIcon, 
    HeartIcon 
} from '@heroicons/vue/24/outline';
import { useRoute, useRouter } from 'vue-router';
import { useAuth } from "../stores/AuthStore";

const route = useRoute();
const authStore = useAuth();
const router = useRouter()

const showMessages = () => {
    if (authStore.token === null) {
        return authStore.loginModal = true;
    }
    router.push('/massages')
}

const showWishlist = () => {
    if (authStore.token === null) {
        return authStore.loginModal = true;
    }
    router.push('/wishlist')
}
</script>
