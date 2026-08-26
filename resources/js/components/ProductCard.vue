<template>
    <div 
        class="group relative bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm hover:shadow-xl hover:shadow-slate-200/50 hover:-translate-y-1.5 transition-all duration-300 flex flex-col h-full"
        :class="props.product?.quantity > 0 ? 'hover:border-orange-500/30' : ''"
    >
    <div class="relative w-full aspect-square bg-slate-50 overflow-hidden flex items-center justify-center">
        <div 
            class="w-full h-full cursor-pointer flex items-center justify-center" 
            @click="showProductDetails"
            :class="props.product?.quantity > 0 ? '' : 'brightness-75 grayscale opacity-60'"
        >
            <img
                :src="props.product?.thumbnail"
                class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-500"
                loading="lazy"
                alt="Product preview"
            />
        </div>

        <div 
            v-if="props.product?.discount_percentage > 0"
            class="absolute top-3 left-3 px-2.5 py-1 bg-red-500 text-white text-[11px] font-black uppercase tracking-wider rounded-full shadow-sm z-10"
        >
            {{ props.product?.discount_percentage }}% {{ $t('OFF') }}
        </div>

        <button 
            class="absolute top-3 right-3 w-9 h-9 rounded-full bg-white border border-slate-100 shadow-sm flex items-center justify-center cursor-pointer hover:scale-110 active:scale-95 transition-all duration-200 z-10"
            :class="props.product?.is_favorite ? 'opacity-100' : 'sm:opacity-0 group-hover:opacity-100 focus:opacity-100'"
            @click="favoriteAddOrRemove"
        >
            <HeartIcon v-if="props.product?.is_favorite" class="w-5 h-5 text-red-500" />
            <HeartIconOutline v-else class="w-5 h-5 text-slate-500 hover:text-red-500" />
        </button>

        <span 
            v-if="props.product?.is_digital"
            class="absolute bottom-3 right-3 inline-flex gap-1 items-center rounded-full bg-slate-900/90 backdrop-blur-md px-2.5 py-1 text-[10px] font-bold text-white uppercase tracking-wider shadow-md animate-badgePulse z-10"
        >
            <ArrowDownTrayIcon class="w-3 h-3 text-emerald-400" />
            {{ $t('Digital') }}
        </span>
    </div>

        <div class="p-4 flex flex-col flex-grow justify-between">
            <div class="space-y-2 cursor-pointer" @click="showProductDetails">
                <h3 
                    class="text-slate-900 text-sm font-semibold tracking-tight line-clamp-2 min-h-[40px] group-hover:text-orange-500 transition-colors"
                    :class="props.product?.quantity > 0 ? '' : 'opacity-50'"
                >
                    {{ props.product?.name }}
                </h3>

                <div class="flex items-center gap-1.5 text-xs">
                    <div class="flex items-center gap-0.5 font-bold text-slate-800">
                        <StarIcon class="w-3.5 h-3.5 text-amber-500" />
                        <span>{{ props.product?.rating }}</span>
                    </div>
                    <span class="text-slate-400">({{ props.product?.total_reviews }})</span>
                    <span class="text-slate-200">|</span>
                    <span 
                        v-if="props.product?.quantity > 0" 
                        class="text-slate-500 font-medium"
                    >
                        {{ props.product?.total_sold }} {{ $t('Sold') }}
                    </span>
                    <span v-else class="text-red-500 font-bold uppercase tracking-wider text-[10px]">
                        {{ $t('Out of Stock') }}
                    </span>
                </div>

                <div class="flex items-baseline gap-2 pt-1">
                    <span class="text-slate-900 text-lg font-black tracking-tight">
                        {{ masterStore.showCurrency(props.product?.discount_price > 0 ? props.product?.discount_price : props.product?.price) }}
                    </span>
                    <span 
                        v-if="props.product?.discount_price > 0"
                        class="text-slate-400 text-xs font-medium line-through decoration-slate-300"
                    >
                        {{ masterStore.showCurrency(props.product?.price) }}
                    </span>
                </div>
            </div>

            <div class="pt-4 mt-auto">
                <div v-if="props.product?.quantity > 0" class="flex items-center gap-2 w-full">
                    <button 
                        v-if="!props.product?.is_digital"
                        class="cursor-pointer w-11 h-11 bg-slate-50 border border-slate-100 text-slate-800 hover:text-orange-500 hover:bg-orange-50 hover:border-orange-200 rounded-2xl flex items-center justify-center transition-all duration-200 flex-shrink-0"
                        @click="addToBasket(props.product)"
                        :title="$t('Add to Basket')"
                    >
                        <BagIcon class="w-5 h-5" />
                    </button>

                    <button
                        class="flex-grow flex items-center justify-center h-11 rounded-2xl bg-orange-500 hover:bg-orange-600 text-white text-sm font-bold tracking-wide transition-all duration-200 shadow-sm active:scale-[0.99]"
                        @click="buyNow"
                    >
                        {{ $t('Buy Now') }}
                    </button>
                </div>

                <button 
                    v-else
                    class="w-full flex items-center justify-center gap-1 border border-slate-200 bg-slate-50 text-slate-400 font-semibold py-2.5 rounded-2xl text-xs cursor-not-allowed"
                    disabled
                >
                    {{ $t('Request Restock') }}
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes badgePulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

.animate-badgePulse {
    animation: badgePulse 2.5s infinite ease-in-out;
}
</style>



<script setup>
import { HeartIcon as HeartIconOutline } from '@heroicons/vue/24/outline';
import { HeartIcon, StarIcon } from '@heroicons/vue/24/solid';
import { ArrowDownTrayIcon } from '@heroicons/vue/20/solid';
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useToast } from 'vue-toastification';
import BagIcon from '../icons/Bag.vue';
import { useAuth } from '../stores/AuthStore';
import { useBasketStore } from '../stores/BasketStore';
import { useMaster } from '../stores/MasterStore';

const router = useRouter();

const masterStore = useMaster();

const basketStore = useBasketStore();
const authStore = useAuth();

const toast = useToast();

const props = defineProps({
    product: Object
});

const orderData = {
    is_buy_now: false,
    product_id: props.product?.id,
    quantity: 1,
    size: null,
    color: null,
    unit: null
};

const addToBasket = (product) => {
    // add product to basket
    basketStore.addToCart(orderData, product);
};

const buyNow = async () => {
    if (authStore.token === null) {
        return authStore.loginModal = true;
    }

  await basketStore.addToCart({
        product_id: props.product?.id,
        is_buy_now: true,
        quantity: 1,
        size: null,
        color: null,
        unit: null
    }, props.product);

    basketStore.buyNowShopId = props.product?.shop.id;
    router.push({ name: 'buynow' })
};

const isFavorite = ref(props.product?.is_favorite);

const favoriteAddOrRemove = () => {
    if (authStore.token === null) {
        return authStore.loginModal = true;
    }
    axios.post('/favorite-add-or-remove', {
        product_id: props.product.id
    }, {
        headers: {
            Authorization: authStore.token
        }
    }).then((response) => {
        props.product.is_favorite = !props.product.is_favorite
        isFavorite.value = response.data.data.product.is_favorite
        if (isFavorite.value === false) {
            toast.warning('Product removed from favorite', {
               position: masterStore.langDirection === 'rtl' ? "bottom-right" : "bottom-left",
            });
        } else {
            toast.success('Product added to favorite', {
               position: masterStore.langDirection === 'rtl' ? "bottom-right" : "bottom-left",
            });
        }
        authStore.favoriteRemove = true
        authStore.fetchFavoriteProducts();
    });
}

const showProductDetails = () => {
    if (props.product.quantity > 0) {
        router.push({ name: 'productDetails', params: { id: props.product.id } })
    }
}

</script>
