<template>
    <PublicLayout ref="publicLayoutRef">
        <div class="bg-slate-50 min-h-screen pt-28 pb-20 selection:bg-orange-500 selection:text-white">
            <div v-if="!isLoading" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <nav class="text-xs md:text-sm font-medium text-slate-500 mb-8 flex items-center gap-2">
                    <router-link to="/" class="hover:text-orange-500 transition-colors">{{ $t("Home") }}</router-link>
                    <span class="text-slate-300">/</span>
                    <span class="text-slate-800 line-clamp-1">{{ product.name }}</span>
                </nav>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">

                <div class="lg:col-span-7 xl:col-span-8">
                    <div class="group relative aspect-square rounded-3xl overflow-hidden bg-slate-50 cursor-pointer">
                            <img
                                :src="selectedThumbnail || product.thumbnails?.[0]?.thumbnail"
                                class="w-full h-full object-contain transition duration-500"
                                alt="Product presentation"
                            />
                        <button
                            v-if="product.thumbnails?.length > 1"
                            @click="showGallery = true"
                            class="absolute bottom-4 right-4 bg-orange-500 hover:bg-orange-600 text-white backdrop-blur-md px-4 py-2.5 rounded-full text-xs font-semibold tracking-wide shadow-sm transition flex items-center gap-1.5"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/>
                            </svg>
                            {{ $t('View Gallery') }} ({{ product.thumbnails.length }})
                        </button>
                    </div>

                    <!-- Thumbnail selectors remain crisp underneath -->
                     <div v-if="product.thumbnails?.length > 1" class="flex items-center gap-3 overflow-x-auto pb-2 scrollbar-thin mt-2">
                        <button 
                            v-for="(thumb, index) in product.thumbnails" 
                            :key="thumb.id"
                            @click="selectedThumbnail = thumb.thumbnail"
                            class="w-20 h-20 flex-shrink-0 bg-slate-50 border rounded-2xl p-1.5 overflow-hidden transition-all duration-200"
                            :class="[selectedThumbnail === thumb.thumbnail || (!selectedThumbnail && index === 0) ? 'border-orange-500 ring-2 ring-orange-500/10' : 'border-slate-200 hover:border-slate-400']"
                        >
                            <img :src="thumb.thumbnail" class="w-full h-full object-contain rounded-xl" />
                        </button>
                    </div> 
                </div>

                    <div class="lg:col-span-5 xl:col-span-4 space-y-6 lg:sticky lg:top-28">

                        <div class="bg-white rounded-3xl p-5 flex items-center justify-between border border-slate-100 shadow-sm transition hover:shadow-md duration-300">
                            <div class="flex items-center gap-4">
                                <img
                                    :src="product.shop?.logo" 
                                    loading="lazy"
                                    class="w-12 h-12 rounded-full object-cover ring-2 ring-slate-100"
                                    alt="Shop Logo"
                                />
                                <div>
                                    <router-link :to="`/shops/${product.shop?.id}`" class="group">
                                        <div class="font-bold text-slate-900 group-hover:text-orange-500 transition-colors text-base flex items-center gap-1">
                                            {{ product.shop?.name }}
                                        </div>
                                    </router-link>
                                    <div class="text-xs font-semibold text-slate-500 flex items-center gap-1 mt-0.5">
                                        <span class="text-amber-500 text-sm">★</span> {{ product.shop?.rating.toFixed(1) }} 
                                        <span class="text-slate-300 font-normal">|</span> 
                                        <span class="font-normal">{{ $t("Verified Seller") }}</span>
                                    </div>
                                </div>
                            </div>

                            <button 
                                @click="showMessages"  
                                class="w-12 h-12 rounded-2xl bg-slate-900 hover:bg-orange-500 text-white flex items-center justify-center shadow-lg shadow-slate-900/10 transition-all duration-300 hover:-translate-y-0.5 active:translate-y-0"
                                :title="$t('Contact Seller')"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                            </button>
                        </div>

                        <div class="bg-white rounded-[32px] p-6 md:p-8 border border-slate-100 shadow-sm space-y-6">
                            
                            <div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-800 uppercase tracking-wider mb-3">
                                    {{ product.brand || $t('Unknown Brand') }}
                                </span>
                                <h1 class="text-2xl md:text-3xl font-black text-slate-900 tracking-tight leading-tight">
                                    {{ product.name }}
                                </h1>
                            </div>

                            <div class="pt-2">
                                <div class="flex items-baseline gap-3 flex-wrap">
                                    <span class="text-4xl font-extrabold text-slate-900 tracking-tight">
                                        {{ masterStore.showCurrency(productPrice) }}
                                    </span>
                                    <span 
                                        v-if="product.discount_price > 0"
                                        class="text-lg text-slate-400 line-through decoration-slate-300 font-medium"
                                    >
                                        {{ masterStore.showCurrency(mainPrice) }}
                                    </span>
                                </div>
                            </div>

                            <p class="text-sm text-slate-600 leading-relaxed font-normal">
                                {{ product.short_description }}
                            </p>

                            <div class="space-y-3 pt-2">
                                <div class="flex items-center gap-3 bg-emerald-50/60 border border-emerald-100 text-emerald-800 rounded-2xl p-3.5 text-sm font-medium">
                                    <span class="text-lg">🚚</span>
                                    <span>{{ $t("Complimentary Express Delivery Included") }}</span>
                                </div>

                                <div class="bg-slate-50 border border-slate-100 rounded-2xl p-4 space-y-1">
                                    <div class="font-bold text-xs uppercase tracking-wider text-slate-700 flex items-center gap-1.5">                <span class="text-lg">🔒</span>
                                        {{ $t("Secured Buyer Protection") }}
                                    </div>
                                    <p class="text-xs text-slate-500 leading-normal pl-5">
                                        {{ $t("Full risk coverage & refund ecosystem if your item diverges from expectations.") }}
                                    </p>
                                </div>
                            </div>

                            <div class="space-y-5 border-t border-b border-slate-100 py-6">
                                <div v-if="product.colors?.length">
                                    <label class="text-xs uppercase tracking-wider font-bold text-slate-700 block mb-3">
                                        {{ $t("Select Color") }}
                                    </label>
                                    <div class="flex flex-wrap gap-2.5">
                                        <button 
                                            v-for="color in product.colors" 
                                            :key="color.id"
                                            @click="formData.color = color.id"
                                            :class="[formData.color === color.id ? 'ring-2 ring-orange-500 ring-offset-2 scale-110' : 'hover:scale-105 border-slate-200']"
                                            class="w-7 h-7 rounded-full border transition-all duration-200"
                                            :style="{ backgroundColor: color.color_code || color.name }"
                                            :title="color.name"
                                        ></button>
                                    </div>
                                </div>

                                <div v-if="product.sizes?.length">
                                    <label class="text-xs uppercase tracking-wider font-bold text-slate-700 block mb-3">
                                        {{ $t("Select Size") }}
                                    </label>
                                    <div class="flex flex-wrap gap-2">
                                        <button 
                                            v-for="size in product.sizes" 
                                            :key="size.id"
                                            @click="formData.size = size.id"
                                            :class="[formData.size === size.id ? 'bg-orange-500 text-white border-slate-900' : 'bg-white text-slate-800 border-slate-200 hover:bg-slate-50']"
                                            class="px-4 py-2.5 text-xs font-bold border rounded-xl transition-all duration-150 tracking-wide min-w-[50px] text-center"
                                        >
                                            {{ size.name }}
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-2">
                                <div v-if="product?.quantity > 0" class="space-y-3">
                                    <button
                                        @click="buyNow"
                                        class="w-full border border-slate-200 bg-orange-500 hover:bg-orange-600 text-white font-bold py-4 rounded-2xl text-base transition-all duration-150"
                                    >
                                        {{ $t("Buy Now") }}
                                    </button>

                                    <button
                                        @click="addToCart"
                                        class="w-full border border-slate-200 bg-white hover:bg-slate-50 text-slate-800 font-bold py-4 rounded-2xl text-base transition-all duration-150"
                                    >
                                        {{ $t("Add To Cart") }}
                                    </button>
                                </div>

                                <div v-else>
                                    <button
                                        disabled
                                        class="w-full bg-slate-100 text-slate-400 font-bold py-4 rounded-2xl text-base cursor-not-allowed text-center"
                                    >
                                        ⚠️ {{ $t("Out of Stock") }}
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div v-if="relatedProducts.length > 0 && !isLoading" class="mt-20 border-t border-slate-200/60 pt-12">
                    <h2 class="text-xl md:text-2xl font-black text-slate-900 tracking-tight mb-8">
                        {{ $t("You may also like") }}
                    </h2>

                    <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-4 gap-4 lg:gap-6 items-start">
                        <div v-for="item in relatedProducts" :key="item.id" class="transition-transform duration-200 hover:-translate-y-1">
                            <ProductCard :product="item" />
                        </div>
                    </div>
                </div>

            </div>

            <!-- Loader -->
            <div v-else
            class="flex flex-col justify-center items-center min-h-[400px]">

              <!-- Modern Loader -->
              <div class="relative">

                  <!-- Outer Ring -->
                  <div
                      class="w-20 h-20 rounded-full border-4 border-orange-100"
                  ></div>

                  <!-- Animated Ring -->
                  <div
                      class="absolute top-0 left-0 w-20 h-20 rounded-full border-4 border-transparent border-t-orange-500 border-r-orange-400 animate-spin"
                  ></div>

                  <!-- Center Logo -->
                  <div
                      class="absolute inset-0 flex items-center justify-center"
                  >
                      <svg
                          class="w-8 h-8 text-orange-500 animate-pulse"
                          fill="none"
                          stroke="currentColor"
                          stroke-width="2"
                          viewBox="0 0 24 24"
                      >
                          <path
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M13 10V3L4 14h7v7l9-11h-7z"
                          />
                      </svg>
                  </div>

              </div>

              <!-- Text -->
              <div class="mt-6 text-gray-600 text-lg font-medium animate-pulse">
                  {{ $t("Loading Product...") }}
              </div>

              <!-- Small Dots -->
              <div class="flex gap-2 mt-4">
                  <span class="w-2 h-2 rounded-full bg-orange-400 animate-bounce"></span>
                  <span class="w-2 h-2 rounded-full bg-orange-500 animate-bounce [animation-delay:0.2s]"></span>
                  <span class="w-2 h-2 rounded-full bg-orange-600 animate-bounce [animation-delay:0.4s]"></span>
              </div>

          </div>
        </div>

        <Teleport to="body">
            <Transition name="fade">
                <div
                    v-if="showGallery"
                    class="fixed inset-0 bg-slate-950/80 backdrop-blur-md z-50 flex items-center justify-center p-4 md:p-6"
                >
                    <div class="bg-white rounded-3xl w-full max-w-5xl relative p-4 md:p-6 shadow-2xl overflow-hidden">
                        <button
                            @click="showGallery = false"
                            class="absolute top-4 right-4 w-10 h-10 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center font-bold text-base transition-colors z-10"
                        >
                            ✕
                        </button>

                        <swiper
                            :spaceBetween="16"
                            :navigation="true"
                            :modules="[Navigation]"
                            class="h-[65vh] md:h-[75vh]"
                        >
                            <swiper-slide v-for="thumbnail in product.thumbnails" :key="thumbnail.id">
                                <div class="h-full w-full flex items-center justify-center p-2 bg-white rounded-2xl">
                                    <img
                                        :src="thumbnail.thumbnail"
                                        class="max-h-full max-w-full object-contain"
                                        alt="High-resolution presentation"
                                    />
                                </div>
                            </swiper-slide>
                        </swiper>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </PublicLayout>
</template>


<script setup>
import { nextTick, onMounted, onUnmounted, ref, watch } from "vue";
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
import { useRoute, useRouter } from "vue-router";
import { useMaster } from "../stores/MasterStore";

import { HeartIcon, HomeIcon, MinusIcon, PlusIcon, ShareIcon } from "@heroicons/vue/24/outline";
import { HeartIcon as HeartIconFill, StarIcon } from "@heroicons/vue/24/solid";
import { FreeMode, Navigation, Thumbs } from "swiper/modules";
import { Swiper, SwiperSlide } from "swiper/vue";

import { useToast } from "vue-toastification";
import { useAuth } from "../stores/AuthStore";
import { useBasketStore } from "../stores/BasketStore";
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faFacebookF, faLinkedin, faTwitter, faPinterest, faRedditAlien, faWhatsapp, faTelegram } from '@fortawesome/free-brands-svg-icons';
import { faEnvelope, faArrowUp } from "@fortawesome/free-solid-svg-icons";
import { useShareLink } from "vue3-social-sharing";
const { shareLink } = useShareLink();

import ProductDetailsRightSide from "../components/ProductDetailsRightSide.vue";
import ToastSuccessMessage from "../components/ToastSuccessMessage.vue";
import BagIcon from "../icons/Bag.vue";
import SkeletonLoader from "../components/SkeletonLoader.vue";
import ReviewRatings from "../components/ReviewRatings.vue";
import ProductCard from "../components/ProductCard.vue";
import Review from "../components/Review.vue";
import PublicLayout from '../layouts/ProductPageLayout.vue';

// Import Swiper styles
import "swiper/css";
import "swiper/css/free-mode";
import "swiper/css/navigation";
import "swiper/css/thumbs";

const showGallery = ref(false)
const toast = useToast();
const route = useRoute();
const router = useRouter();
const masterStore = useMaster();
const basketStore = useBasketStore();
const authStore = useAuth();

const publicLayoutRef = ref(null);

const thumbsSwiper = ref(null);
const modules = [FreeMode, Navigation, Thumbs];


const showMessages = () => {
    if (authStore.token === null) {
        return authStore.loginModal = true;
    }
    router.push('/massages')
}

const setThumbsSwiper = (swiper) => {
    thumbsSwiper.value = swiper;
};

const formData = ref({
    product_id: route.params.id,
    size: null,
    color: null,
    unit: null,
});

const product = ref({});
const productPrice = ref(0);
const mainPrice = ref(0);
const discountPercentage = ref(0);

const relatedProducts = ref([]);
const popularProducts = ref([]);

const aboutProduct = ref(true);
const review = ref(false);

const cartProduct = ref(null);
const isLoading = ref(true);

onMounted(() => {
    fetchProductDetails();
    window.scrollTo(0, 0);
    findProductInCart(route.params.id);
});

watch(formData, () => {
    calculateProductPrice();
}, { deep: true });

const shareOptions = [
    { name: "facebook", icon: faFacebookF, color: "#0d68f1" },
    { name: "linkedin", icon: faLinkedin, color: "#1275b1" },
    { name: "twitter", icon: faTwitter, color: "#47acdf" },
    { name: "pinterest", icon: faPinterest, color: "#bb0f23" },
    { name: "reddit", icon: faRedditAlien, color: "#fc471e" },
    { name: "whatsapp", icon: faWhatsapp, color: "#25d366" },
    { name: "email", icon: faEnvelope, color: "#bb0f23" },
    { name: "telegram", icon: faTelegram, color: "#47acdf" },
];
// 1275b1
const share = (network) => {
    let description = product.value.short_description.replace(/<[^>]*>/g, "");
    let currentURL = window.location.href;
    let thumbnail = product.value.thumbnails[0];

    shareLink({
        network: network,
        url: currentURL,
        title: product.value.name,
        description: description,
        media: thumbnail ? thumbnail.url : null,
        quote: product.value.name,
        hashtags: product.value.meta_keywords,
        twitterUser: product.value.shop?.name
    })
}

const calculateProductPrice = () => {
    var colorPrice = 0;
    var sizePrice = 0;

    const color = product.value.colors?.find((color) => color.id == formData.value.color);
    const size = product.value.sizes?.find((size) => size.id == formData.value.size);

    if (color) {
        colorPrice = color.price ?? 0;
    }
    if (size) {
        sizePrice = size.price ?? 0;
    }

    if (product.value.discount_price > 0) {
        productPrice.value = product.value.discount_price + colorPrice + sizePrice;
        mainPrice.value = product.value.price + colorPrice + sizePrice;
    } else {
        productPrice.value = product.value.price + colorPrice + sizePrice;
        mainPrice.value = productPrice.value;
    }

    discountPercentage.value = (((mainPrice.value - productPrice.value) / mainPrice.value) * 100).toFixed(2);
}

const buyNow = () => {
    //publicLayoutRef.value?.openDownloadModal();
     if (authStore.token === null) {
         return (authStore.loginModal = true);
     }
     basketStore.addToCart({
         product_id: formData.value.product_id,
         is_buy_now: true,
         quantity: 1,
         size: formData.value.size,
         color: formData.value.color,
         unit: null
     }, product.value);

     basketStore.buyNowShopId = product.value?.shop.id;
     router.push({ name: "buynow" });
};

watch(route, async () => {
    await nextTick();
    window.scrollTo(0, 0);
    fetchProductDetails();
    aboutProduct.value = true;
    review.value = false;
    formData.value.product_id = route.params.id;
    findProductInCart(route.params.id);
});

watch(() => basketStore.products, () => {
    findProductInCart(route.params.id);
}, { deep: true });

const findProductInCart = (productId) => {
    let foundProduct = null;
    basketStore.products.forEach((item) => {
        item.products.find((product) => {
            if (product.id == productId) {
                return (foundProduct = product);
            }
        });
    });
    cartProduct.value = foundProduct;
    if (foundProduct) {
        formData.value.size = foundProduct.size?.id;
        formData.value.color = foundProduct.color?.id;
        formData.value.unit = foundProduct.unit;
    }
};

const addToCart = () => {
    //publicLayoutRef.value?.openDownloadModal();
     basketStore.addToCart(formData.value, product.value);
     setTimeout(() => {
         findProductInCart(route.params.id);
     }, 200);
};

const decrementQty = () => {
    basketStore.decrementQuantity(product.value);
    setTimeout(() => {
        findProductInCart(route.params.id);
    }, 200);
};

const incrementQty = () => {
    basketStore.incrementQuantity(product.value);
    setTimeout(() => {
        findProductInCart(route.params.id);
    }, 200);
};

const favoriteAddOrRemove = () => {
    if (authStore.token === null) {
        return (authStore.loginModal = true);
    }
    axios.post('/favorite-add-or-remove', {
        product_id: product.value.id
    }, {
        headers: {
            Authorization: authStore.token
        }
    }).then(() => {
        product.value.is_favorite = !product.value.is_favorite
        if (product.value.is_favorite === false) {
            const content = {
                component: ToastSuccessMessage,
                props: {
                    title: 'Product removed from favorite',
                    message: 'Product removed from favorite successfully',
                },
            };
            toast(content, {
                type: "default",
                hideProgressBar: true,
                icon: false,
                position: "top-right",
                toastClassName: "vue-toastification-alert",
                timeout: 3000
            });
        } else {
            const content = {
                component: ToastSuccessMessage,
                props: {
                    title: 'Product added to favorite',
                    message: 'Product added to favorite successfully',
                },
            };
            toast(content, {
                type: "default",
                hideProgressBar: true,
                icon: false,
                position: "top-right",
                toastClassName: "vue-toastification-alert",
                timeout: 3000
            });
        }
        authStore.fetchFavoriteProducts();
    }).catch((error) => {
        console.log(error);
    });
};

const showReview = () => {
    aboutProduct.value = false;
    review.value = true;
    fetchReviews();
};

const flashSale = ref({});
const fetchProductDetails = async () => {
    isLoading.value = true;
    axios.get("/product-details", {
        params: { product_id: route.params.id },
        headers: {
            Authorization: authStore.token,
        },
    }).then((response) => {
        product.value = response.data.data.product;
        selectedThumbnail.value = product.value?.thumbnails?.[0]?.thumbnail || null;
        relatedProducts.value = response.data.data.related_products;
        popularProducts.value = response.data.data.popular_products;
        flashSale.value = response.data.data.product.flash_sale;

        if (flashSale.value) {
            startCountdown();
        }

        if (product.value.colors.length > 0) {
            formData.value.color = product.value.colors[0].id;
        } else {
            formData.value.color = null;
        }
        if (product.value.sizes.length > 0) {
            formData.value.size = product.value.sizes[0].id;
        } else {
            formData.value.size = null;
        }
        calculateProductPrice();
        findProductInCart(route.params.id);

        setTimeout(() => {
            isLoading.value = false;
        }, 100);
    });
};

const averageRatings = ref({});

const totalReviews = ref(0);
const reviews = ref([]);

const currentPage = ref(1);
const perPage = ref(6);

const onClickHandler = (page) => {
    currentPage.value = page;
    fetchReviews();
};

const fetchReviews = async () => {
    axios.get("/reviews", {
        params: {
            product_id: route.params.id,
            page: currentPage.value,
            per_page: perPage.value,
        },
    }).then((response) => {
        totalReviews.value = response.data.data.total;
        reviews.value = response.data.data.reviews;
        averageRatings.value = response.data.data.average_rating_percentage;
    });
};


const endDay = ref("");
const endHour = ref("");
const endMinute = ref("");
const endSecond = ref("");
let countdownInterval = null;

const startCountdown = () => {
    const endDate = new Date(flashSale.value?.end_date).getTime();

    if (flashSale.value?.end_date) {
        countdownInterval = setInterval(() => {
            const now = new Date().getTime();
            const timeLeft = endDate - now;

            if (timeLeft <= 0) {
                clearInterval(countdownInterval);
                endDay.value = "00";
                endHour.value = "00";
                endMinute.value = "00";
                endSecond.value = "00";
            } else {
                endDay.value = String(Math.floor(timeLeft / (1000 * 60 * 60 * 24))).padStart(2, "0");
                endHour.value = String(Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))).padStart(2, "0");
                endMinute.value = String(Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60))).padStart(2, "0");
                endSecond.value = String(Math.floor((timeLeft % (1000 * 60)) / 1000)).padStart(2, "0");
            }
        }, 1000);
    }
};

onUnmounted(() => {
    clearInterval(countdownInterval);
});

// Position variables to control zoom position
const mouseX = ref(0);
const mouseY = ref(0);

const handleMouseMove = (event) => {
    const rect = event.currentTarget.getBoundingClientRect();

    mouseX.value = ((event.clientX - rect.left) / rect.width) * 100;
    mouseY.value = ((event.clientY - rect.top) / rect.height) * 100;

    document.documentElement.style.setProperty('--mouse-x', `${mouseX.value}%`);
    document.documentElement.style.setProperty('--mouse-y', `${mouseY.value}%`);
};
const selectedThumbnail = ref(
    product?.thumbnails?.[0]?.thumbnail || null
);

</script>

<style scoped>
.zoom-container {
    overflow: hidden;
    /*width: 100%;
    height: 100%;*/    
    cursor: zoom-in;
}

.zoom-image {
    /*width: 100%;
    height: 100%;*/
    object-fit: contain;
    transition: transform .2s ease;
}

.zoom-container:hover .zoom-image {
    transform: scale(1.8);
    transform-origin: var(--mouse-x, 50%) var(--mouse-y, 50%);
}
</style>

<style>
.description img {
    max-width: 95% !important;
}

iframe {
    width: 100%;
    height: 300px !important;
}

@media(max-width:500px) {
    iframe {
        height: 200px !important;
    }
}

@media(max-width:375px) {
    iframe {
        height: 180px !important;
    }
}

@media(max-width:320px) {
    iframe {
        height: 160px !important;
    }
}

.product-details-slider .swiper-slide {
    height: auto !important;
}

.product-details-thumbnail .swiper-slide {
    @apply h-20 md:h-[120px] lg:h-[100px];
}

.product-details-thumbnail .swiper-button-prev,
.product-details-thumbnail .swiper-button-next {
    @apply bg-white w-6 h-6 rounded-full shadow border border-slate-200 text-slate-600 -translate-y-1/2 mt-0;
}

.product-details-thumbnail .swiper-button-prev::after,
.product-details-thumbnail .swiper-button-next::after {
    @apply text-base;
}

.product-details-thumbnail .swiper-button-next {
    right: 0px;
}

.product-details-thumbnail .swiper-button-prev {
    left: 0px;
}

.product-details-thumbnail .swiper-slide {
    @apply border border-slate-100 rounded-lg transition overflow-hidden;
}

.product-details-thumbnail .swiper-slide-thumb-active {
    @apply border border-primary;
}

.product-details-slider .swiper-slide {
    height: auto !important;
}

.product-details-thumbnail .swiper-slide {
    border-radius: 20px;
    overflow: hidden;
}



/* Desktop zoom only */
@media (min-width: 768px) {
    .zoom-container:hover .zoom-image {
        transform: scale(1.8);
        transform-origin: calc(var(--mouse-x, 50%)) calc(var(--mouse-y, 50%));
    }
}

/* Disable zoom on mobile */
@media (max-width: 767px) {
    .zoom-container {
        cursor: default;
    }

    .zoom-image {
        transform: none !important;
    }
}

.zoom-container:hover .zoom-image {
    transform: scale(1.6);
}
.product-details-thumbnail .swiper-slide-thumb-active {
    border: 2px solid #f97316;
    border-radius: 16px;
}
.bg-primary {
    background-color: #e27429;
}
.text-primary {
    color: #e27429;
}
.product-details-thumbnail .swiper-slide {
    height: auto !important;
    display: flex !important;
    justify-content: center;
    align-items: center;
    border-radius: 16px;
    overflow: hidden;
}

.product-details-thumbnail .swiper-slide-thumb-active {
    border: 2px solid #f97316;
}

.thumbnail-box {
    width: 90px;
    height: 90px;
    display: flex;
    justify-content: center;
    align-items: center;
}

.thumbnail-image {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.product-details-slider .swiper-slide {
    display: flex;
    justify-content: center;
    align-items: center;
}

.product-details-slider .swiper-wrapper {
    align-items: center;
}

.product-details-slider .swiper-slide {
    display: flex !important;
    align-items: center;
    justify-content: center;
}
</style>