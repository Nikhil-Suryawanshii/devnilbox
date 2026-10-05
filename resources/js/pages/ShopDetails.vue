<template>
    <div class="shop-page">
        <!-- ===== SHOP HERO BANNER (preserved) ===== -->
        <ShopHeroBanner
            :shop="shop"
            :isLoading="isLoading"
            @chat="openChat"
            @follow="toggleFollow"
            @share="shareShop"
        />

        <!-- chat sidebar -->
        <RightChatSidebar v-if="activeChatShop" :show="showSidebar" @close="showSidebar = false"
            :shop="activeChatShop" />

        <!-- ===== SHOP NAVIGATION TABS ===== -->
        <div class="shop-tabs-wrapper main-container">
            <div class="shop-tabs-container">
                <div class="shop-tabs-scroll">
                    <button
                        v-for="tab in tabs"
                        :key="tab.key"
                        class="shop-tab"
                        :class="{ 'shop-tab-active': activeTab === tab.key }"
                        @click="switchTab(tab.key)"
                    >
                        <span class="tab-label-full">{{ $t(tab.label) }}</span>
                        <span class="tab-label-short">{{ $t(tab.shortLabel || tab.label) }}</span>
                        <span v-if="tab.count !== null" class="shop-tab-count">({{ tab.count }})</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- ===== MAIN CONTENT AREA ===== -->
        <div class="shop-content-area main-container">

            <!-- ===== PRODUCTS TAB ===== -->
            <template v-if="activeTab === 'products'">

                <!-- Search + Sort + View Controls -->
                <div class="shop-controls">
                    <div class="shop-search-box">
                        <MagnifyingGlassIcon class="shop-search-icon" />
                        <input
                            type="text"
                            :placeholder="$t('Search in this shop...')"
                            class="shop-search-input"
                            v-model="search"
                            @input="searchProducts()"
                        />
                    </div>
                    <div class="shop-controls-right">
                        <div class="shop-sort-custom">
                            <button
                                class="shop-sort-btn"
                                @click="isSortOpen = !isSortOpen"
                            >
                                <span><span class="sort-prefix-full">{{ $t('Sort by:') }}</span><span class="sort-prefix-short">{{ $t('Sort:') }}</span> <strong>{{ $t(sortOptions.find(o => o.value === sortBy)?.label || 'Popular') }}</strong></span>
                                <svg class="sort-icon" :class="{'rotate-180': isSortOpen}" width="12" height="7" viewBox="0 0 12 7" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M1 1L6 6L11 1"/></svg>
                            </button>
                            
                            <div v-if="isSortOpen" class="fixed inset-0 z-[40]" @click="isSortOpen = false"></div>
                            
                            <div v-if="isSortOpen" class="shop-sort-dropdown-menu z-[50]">
                                <button
                                    v-for="option in sortOptions"
                                    :key="option.value"
                                    class="shop-sort-option"
                                    :class="{'active': sortBy === option.value}"
                                    @click="selectSortOption(option.value)"
                                >
                                    <span>{{ $t(option.label) }}</span>
                                    <svg v-if="sortBy === option.value" class="sort-check-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                </button>
                            </div>
                        </div>
                        <div class="shop-view-toggle">
                            <button
                                class="shop-view-btn"
                                :class="{ active: viewMode === 'grid' }"
                                @click="viewMode = 'grid'"
                                :title="$t('Grid view')"
                            >
                                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <rect x="1" y="1" width="6.5" height="6.5" rx="1.5"/><rect x="10.5" y="1" width="6.5" height="6.5" rx="1.5"/>
                                    <rect x="1" y="10.5" width="6.5" height="6.5" rx="1.5"/><rect x="10.5" y="10.5" width="6.5" height="6.5" rx="1.5"/>
                                </svg>
                            </button>
                            <button
                                class="shop-view-btn"
                                :class="{ active: viewMode === 'list' }"
                                @click="viewMode = 'list'"
                                :title="$t('List view')"
                            >
                                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <line x1="1" y1="4" x2="17" y2="4"/><line x1="1" y1="9" x2="17" y2="9"/><line x1="1" y1="14" x2="17" y2="14"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Category Filters -->
                <div v-if="isLoadingCategories || categories.length > 1" class="shop-categories-scroll">
                    <div class="shop-categories" :ref="setCatTrack">
                        <template v-if="!isLoadingCategories">
                            <button
                                v-for="cat in categories"
                                :key="cat.key"
                                class="shop-cat-pill"
                                :class="{ 'shop-cat-active': activeCategory === cat.key }"
                                @click="selectCategory(cat.key)"
                            >
                                <span v-if="cat.key === 'all'" class="shop-cat-icon">🏷️</span>
                                <img
                                    v-else-if="cat.thumbnail"
                                    :src="cat.thumbnail"
                                    :alt="cat.label"
                                    class="shop-cat-img"
                                    loading="lazy"
                                />
                                <span v-else class="shop-cat-img shop-cat-initial">{{ (cat.label || '?').charAt(0).toUpperCase() }}</span>
                                <span class="shop-cat-label">{{ cat.key === 'all' ? $t(cat.label) : cat.label }}</span>
                            </button>
                        </template>
                        <template v-else>
                            <SkeletonLoader v-for="i in 5" :key="i" class="shop-cat-skeleton" />
                        </template>
                    </div>
                </div>

                <!-- Product Header -->
                <div class="shop-product-header">
                    <div class="shop-product-header-left">
                        <h2 class="shop-product-heading">{{ activeCategory === 'all' ? $t('All Products') : activeCategoryLabel }}</h2>
                        <span class="shop-product-count">{{ totalProducts }} {{ $t('products found') }}</span>
                    </div>
                    <div class="shop-product-header-right" v-if="totalProducts > 0 && !isLoadingProducts">
                        <span class="shop-page-info">
                            {{ $t('Page') }} {{ currentPage }} {{ $t('of') }} {{ Math.ceil(totalProducts / perPage) || 1 }}
                        </span>
                    </div>
                </div>

                <!-- Product Grid -->
                <div class="shop-product-grid" :class="viewMode === 'list' ? 'list-view' : ''">
                    <template v-if="!isLoadingProducts">
                        <div v-for="product in products" :key="product.id" class="shop-product-item">
                            <ProductCard :product="product" />
                        </div>
                    </template>
                    <template v-else>
                        <div v-for="i in 8" :key="i" class="shop-product-item">
                            <SkeletonLoader class="w-full h-[280px] sm:h-[380px] rounded-2xl" />
                        </div>
                    </template>
                </div>

                <!-- Empty State -->
                <div v-if="products.length === 0 && !isLoadingProducts" class="shop-empty-state">
                    <div class="shop-empty-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#D1D5DB" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                    </div>
                    <p class="shop-empty-text">{{ $t('No Products Found') }}</p>
                    <p class="shop-empty-sub">{{ $t('Try adjusting your search or filters') }}</p>
                </div>

                <!-- Pagination -->
                <div v-if="products.length > 0 && !isLoadingProducts" class="shop-pagination">
                    <div class="shop-pagination-info">
                        {{ $t('Showing') }} {{ (perPage * (currentPage - 1) + 1) }} {{ $t('to') }}
                        {{ (perPage * (currentPage - 1) + products.length) }} {{ $t('of') }}
                        {{ totalProducts }} {{ $t('results') }}
                    </div>
                    <div>
                        <vue-awesome-paginate
                            :total-items="totalProducts"
                            :items-per-page="perPage"
                            type="button"
                            :max-pages-shown="5"
                            v-model="currentPage"
                            :hide-prev-next-when-ends="true"
                            @click="onClickHandler"
                        />
                    </div>
                </div>
            </template>

            <!-- ===== REVIEWS TAB ===== -->
            <template v-if="activeTab === 'reviews'">
                <div class="shop-reviews-section">
                    <div class="grid grid-cols-1 xl:grid-cols-2 gap-8 items-start">
                        <!-- Rating Summary -->
                        <div>
                            <h2 class="shop-section-title">{{ $t('Rating and Review') }}</h2>
                            <ReviewRatings
                                :reviewRatings="averageRatings?.percentages"
                                :averageRating="averageRatings?.rating"
                                :totalReview="totalReviews"
                            />
                        </div>

                        <!-- Reviews List -->
                        <div>
                            <h2 class="shop-section-title">{{ $t('Reviews') }}</h2>
                            <div class="mt-4">
                                <div v-if="!isLoadingReviews" v-for="review in reviews" :key="review.id" class="mb-4">
                                    <Review :review="review" />
                                </div>

                                <!-- loading skeleton -->
                                <div v-else v-for="i in 6" :key="i" class="mb-4">
                                    <div class="w-full flex gap-3 items-center">
                                        <SkeletonLoader class="w-16 h-16 rounded-full shrink-0" />
                                        <div class="w-full space-y-2">
                                            <SkeletonLoader class="w-full h-2.5 rounded-lg" />
                                            <SkeletonLoader class="w-11/12 h-2.5 rounded-lg" />
                                            <SkeletonLoader class="w-10/12 h-2.5 rounded-lg" />
                                        </div>
                                    </div>
                                </div>

                                <!-- pagination -->
                                <div v-if="!isLoadingReviews && reviews.length > 0" class="shop-pagination">
                                    <div class="shop-pagination-info">
                                        {{ $t('Showing') }} {{ (reviewPerPage * (reviewPage - 1) + 1) }} {{ $t('to') }}
                                        {{ (reviewPerPage * (reviewPage - 1) + reviews.length) }} {{ $t('of') }}
                                        {{ totalReviews }} {{ $t('results') }}
                                    </div>
                                    <div>
                                        <vue-awesome-paginate
                                            :total-items="totalReviews"
                                            :items-per-page="reviewPerPage"
                                            type="button"
                                            :max-pages-shown="3"
                                            v-model="reviewPage"
                                            :hide-prev-next-when-ends="true"
                                            @click="reviewPagination"
                                        />
                                    </div>
                                </div>

                                <div v-if="reviews.length === 0 && !isLoadingReviews" class="shop-empty-state">
                                    <p class="shop-empty-text italic">{{ $t('No Reviews Found') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <!-- ===== ABOUT SHOP TAB ===== -->
            <template v-if="activeTab === 'about'">
                <div class="shop-about-section">
                    <h2 class="shop-section-title">{{ $t('About Shop') }}</h2>
                    <div class="shop-about-content" v-if="shop?.description">
                        <div v-html="shop?.description"></div>
                    </div>
                    <p v-else class="shop-empty-text">{{ $t('No description available') }}</p>
                </div>
            </template>

            <!-- ===== SHOP POLICIES TAB ===== -->
            <template v-if="activeTab === 'policies'">
                <div class="shop-about-section">
                    <h2 class="shop-section-title">{{ $t('Shop Policies') }}</h2>
                    <p class="shop-empty-text">{{ $t('No policies available yet') }}</p>
                </div>
            </template>

            <!-- ===== CONTACT TAB ===== -->
            <template v-if="activeTab === 'contact'">
                <div class="shop-about-section">
                    <h2 class="shop-section-title">{{ $t('Contact') }}</h2>
                    <div v-if="shop?.address || shop?.phone || shop?.email" class="shop-contact-info">
                        <div v-if="shop?.address" class="shop-contact-row">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#6B7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            <span>{{ shop?.address }}</span>
                        </div>
                        <div v-if="shop?.phone" class="shop-contact-row">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#6B7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            <span>{{ shop?.phone }}</span>
                        </div>
                        <div v-if="shop?.email" class="shop-contact-row">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#6B7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            <span>{{ shop?.email }}</span>
                        </div>
                    </div>
                    <p v-else class="shop-empty-text">{{ $t('No contact information available') }}</p>
                </div>
            </template>

        </div>

        <!-- ===== PROMOTIONAL BANNERS ===== -->
        <div v-if="shop?.banners?.length > 0" class="main-container shop-banners-section">
            <swiper :breakpoints="breakpoints" :spaceBetween="30" :freeMode="true" :modules="modules">
                <swiper-slide v-if="!isLoading" v-for="banner in shop?.banners" :key="banner.id">
                    <img :src="banner.thumbnail" alt="banner" loading="lazy"
                        class="aspect-[6/2] rounded-xl object-cover">
                </swiper-slide>
                <swiper-slide v-else v-for="i in 3" :key="i">
                    <SkeletonLoader class="w-full h-32 rounded-xl" />
                </swiper-slide>
            </swiper>
        </div>

    </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, watch, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { MagnifyingGlassIcon } from '@heroicons/vue/24/solid';
import { Swiper, SwiperSlide } from 'swiper/vue';
import { FreeMode } from 'swiper/modules';
import ProductCard from '../components/ProductCard.vue';
import ReviewRatings from '../components/ReviewRatings.vue';
import Review from '../components/Review.vue';
import SkeletonLoader from '../components/SkeletonLoader.vue';
import RightChatSidebar from '../components/RightChatSidebar.vue';
import ShopHeroBanner from '../components/ShopHeroBanner.vue';

import { useToast } from 'vue-toastification';
import ToastSuccessMessage from '../components/ToastSuccessMessage.vue';
import { useMaster } from '../stores/MasterStore';
import { useAuth } from '../stores/AuthStore';

const toast = useToast();
const authStore = useAuth();
const masterStore = useMaster();

const router = new useRouter();
const route = useRoute();

import 'swiper/css';
import 'swiper/css/free-mode';

const modules = [FreeMode];

const isLoading = ref(true);
const isLoadingProducts = ref(true);
const isLoadingReviews = ref(false);

// Tab system
const activeTab = ref('products');

const tabs = computed(() => [
    { key: 'products', label: 'All Products', count: totalProducts.value },
    { key: 'reviews', label: 'Reviews', count: totalReviews.value },
    { key: 'about', label: 'About Shop', shortLabel: 'About', count: null },
    { key: 'policies', label: 'Shop Policies', shortLabel: 'Policies', count: null },
    { key: 'contact', label: 'Contact', count: null },
]);

const switchTab = (key) => {
    activeTab.value = key;
    if (key === 'reviews') {
        fetchReviews();
    }
};

// View mode + Sort
const viewMode = ref('grid');
const sortBy = ref('popular');
const isSortOpen = ref(false);

const sortOptions = [
    { value: 'popular', label: 'Popular' },
    { value: 'newest', label: 'Newest' },
    { value: 'price_low', label: 'Price: Low to High' },
    { value: 'price_high', label: 'Price: High to Low' },
];

// UI option -> API `sort_type` value
const sortTypeMap = {
    popular: 'popular_product',
    newest: 'newest',
    price_low: 'low_to_high',
    price_high: 'high_to_low',
};

const selectSortOption = (val) => {
    sortBy.value = val;
    isSortOpen.value = false;
    currentPage.value = 1;
    fetchProducts();
};

const sortProducts = () => {
    currentPage.value = 1;
    fetchProducts();
};

// Category filters (loaded from the API: only categories that have this shop's products)
const ALL_CATEGORY = { key: 'all', label: 'All', thumbnail: null };
const activeCategory = ref('all');
const categories = ref([ALL_CATEGORY]);
const isLoadingCategories = ref(true);

const activeCategoryLabel = computed(
    () => categories.value.find((c) => c.key === activeCategory.value)?.label || ''
);

const fetchCategories = async () => {
    isLoadingCategories.value = true;
    axios.get('/shop-categories', {
        params: { shop_id: route.params.id, with_products: 1 },
        headers: { 'Accept-Language': masterStore.locale || 'en' }
    }).then((response) => {
        const list = response.data?.data?.categories || [];
        categories.value = [
            ALL_CATEGORY,
            ...list.map((c) => ({ key: c.id, label: c.name, thumbnail: c.thumbnail })),
        ];
        isLoadingCategories.value = false;
    }).catch(() => {
        categories.value = [ALL_CATEGORY];
        isLoadingCategories.value = false;
    });
};

// Global "snake" line across all category pills.
// One animated CSS variable (--cat-snake-x) lives on the track and is inherited by every pill;
// each pill only needs to know its own x-offset (--pill-x) so the line flows seamlessly
// across them. Pills added later are picked up automatically via the observers below.
let catTrackEl = null;
let catMutationObserver = null;
let catResizeObserver = null;

const syncCatSnake = () => {
    if (!catTrackEl) return;
    const pills = catTrackEl.querySelectorAll('.shop-cat-pill');
    pills.forEach((pill) => pill.style.setProperty('--pill-x', `${pill.offsetLeft}px`));
    // Width actually covered by pills (not the full container), so the line never idles on empty space
    const last = pills[pills.length - 1];
    const trackWidth = last ? last.offsetLeft + last.offsetWidth : 0;
    catTrackEl.style.setProperty('--cat-track-w', `${trackWidth}px`);
    // Roughly constant visual speed regardless of how many categories exist (incl. 140px lead-in/out)
    catTrackEl.style.setProperty('--cat-snake-dur', `${Math.min(9, Math.max(2.5, (trackWidth + 280) / 200)).toFixed(2)}s`);
};

const observeCatPills = () => {
    if (!catResizeObserver || !catTrackEl) return;
    catResizeObserver.disconnect();
    catResizeObserver.observe(catTrackEl);
    catTrackEl.querySelectorAll('.shop-cat-pill').forEach((pill) => catResizeObserver.observe(pill));
};

const teardownCatSnake = () => {
    catMutationObserver?.disconnect();
    catResizeObserver?.disconnect();
    catMutationObserver = null;
    catResizeObserver = null;
};

const setCatTrack = (el) => {
    if (el === catTrackEl) return;
    teardownCatSnake();
    catTrackEl = el;
    if (!el || typeof ResizeObserver === 'undefined') return;
    catResizeObserver = new ResizeObserver(syncCatSnake);
    catMutationObserver = new MutationObserver(() => {
        observeCatPills();
        syncCatSnake();
    });
    catMutationObserver.observe(el, { childList: true });
    observeCatPills();
    syncCatSnake();
};

onBeforeUnmount(teardownCatSnake);

const selectCategory = (key) => {
    if (activeCategory.value === key) return;
    activeCategory.value = key;
    currentPage.value = 1;
    fetchProducts();
};

// Pagination
const currentPage = ref(1);
const perPage = ref(12);

const onClickHandler = (page) => {
    currentPage.value = page;
    fetchProducts();
};

const reviewPerPage = ref(5);
const reviewPage = ref(1);

const reviewPagination = (page) => {
    reviewPage.value = page;
    fetchReviews();
};

const shop = ref({});

const products = ref([]);
const totalProducts = ref(0);

const averageRatings = ref({});
const totalReviews = ref(0);
const reviews = ref([]);

const search = ref('');
const showSidebar = ref(false);
const activeChatShop = ref(null);

let searchTimer = null;
const searchProducts = () => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
        currentPage.value = 1;
        activeTab.value = 'products';
        fetchProducts();
    }, 600);
};

onMounted(() => {
    if (!masterStore.multiVendor) {
        router.push('/');
        return;
    }
    fetchDetails();
    window.scrollTo(0, 0);
    fetchCategories();
    fetchProducts();
});

const fetchDetails = async () => {
    isLoading.value = true;
    axios.get('/shops/' + route.params.id, {
        headers: { Authorization: authStore.token }
    }).then((response) => {
        shop.value = response.data.data.shop;
        setTimeout(() => {
            isLoading.value = false;
        }, 200);
    }).catch((error) => {
        isLoading.value = false;
    })
};

const fetchProducts = async () => {
    isLoadingProducts.value = true;
    axios.get('/products', {
        params: {
            shop_id: route.params.id,
            page: currentPage.value,
            per_page: perPage.value,
            search: search.value,
            sort_type: sortTypeMap[sortBy.value],
            category_id: activeCategory.value === 'all' ? undefined : activeCategory.value,
        },
        headers: {
            'Accept-Language': masterStore.locale || 'en',
            Authorization: authStore.token
        }
    }).then((response) => {
        totalProducts.value = response.data.data.total;
        products.value = response.data.data.products;
        isLoadingProducts.value = false;
    }).catch((error) => {
        isLoadingProducts.value = false;
    })
};

const fetchReviews = async () => {
    isLoadingReviews.value = true;
    axios.get('/reviews', {
        params: {
            shop_id: route.params.id,
            page: reviewPage.value,
            per_page: reviewPerPage.value
        }
    }).then((response) => {
        totalReviews.value = response.data.data.total;
        reviews.value = response.data.data.reviews;
        averageRatings.value = response.data.data.average_rating_percentage;
        isLoadingReviews.value = false;
    }).catch((error) => {
        isLoadingReviews.value = false;
    })
};

const breakpoints = {
    320: { slidesPerView: 1, spaceBetween: 10 },
    768: { slidesPerView: 2, spaceBetween: 10 },
    1024: { slidesPerView: 2, spaceBetween: 30 },
    1280: { slidesPerView: 3, spaceBetween: 30 }
};

const openChat = (shop) => {
    activeChatShop.value = shop;
    showChat();
};

const showChat = async () => {
    const response = await axios.post('/store-message', {
        shop_id: activeChatShop.value.id,
        user_id: authStore?.user?.id,
        type: 'user',
    }, {
        headers: {
            Authorization: authStore.token,
        }
    });

    showSidebar.value = true;
}

// Follow / unfollow the shop (requires login)
const isFollowLoading = ref(false);

const notify = (title, message) => {
    toast(
        { component: ToastSuccessMessage, props: { title, message } },
        {
            type: 'default',
            hideProgressBar: true,
            icon: false,
            position: 'top-right',
            toastClassName: 'vue-toastification-alert',
            timeout: 3000,
        }
    );
};

const toggleFollow = () => {
    if (authStore.token === null) return (authStore.loginModal = true);
    if (isFollowLoading.value || !shop.value?.id) return;

    isFollowLoading.value = true;
    axios.post('/shop/follow-toggle', { shop_id: shop.value.id }, {
        headers: { Authorization: authStore.token }
    }).then((response) => {
        const data = response.data?.data || {};
        shop.value = {
            ...shop.value,
            is_following: !!data.is_following,
            likes_count: data.likes_count ?? shop.value.likes_count,
        };
        notify(
            data.is_following ? 'Shop followed' : 'Shop unfollowed',
            response.data?.message || ''
        );
    }).catch(() => {}).finally(() => {
        isFollowLoading.value = false;
    });
};

// Share the shop link (native share sheet on mobile, copy link elsewhere)
const shareShop = async () => {
    const url = window.location.href;
    const title = shop.value?.name || '';
    try {
        if (navigator.share) {
            await navigator.share({ title, text: title, url });
            return;
        }
        await navigator.clipboard.writeText(url);
        notify('Link copied', 'Shop link copied to clipboard');
    } catch (e) {
        // user cancelled the share sheet or clipboard is unavailable
    }
};

</script>

<style scoped>
/* ==========================================
   SHOP PAGE - LAYOUT
   ========================================== */

.shop-page {
    background: #FAFAFA;
    min-height: 100vh;
    padding-bottom: 80px;
}

/* ==========================================
   SHOP TABS NAVIGATION
   ========================================== */

.shop-tabs-wrapper {
    margin-top: -4px;
    position: relative;
    z-index: 8;
}

.shop-tabs-container {
    background: #FFFFFF;
    border-radius: 18px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
    padding: 0 8px;
    overflow: hidden;
}

.shop-tabs-scroll {
    display: flex;
    gap: 0;
    overflow-x: auto;
    scrollbar-width: none;
    -ms-overflow-style: none;
    -webkit-overflow-scrolling: touch;
}

.shop-tabs-scroll::-webkit-scrollbar {
    display: none;
}

.shop-tab {
    position: relative;
    padding: 16px 20px;
    font-size: 0.9rem;
    font-weight: 500;
    color: #6B7280;
    background: none;
    border: none;
    cursor: pointer;
    white-space: nowrap;
    transition: color 0.25s ease;
    flex-shrink: 0;
}

.shop-tab:hover {
    color: #374151;
}

.shop-tab-active {
    color: #FF6B00;
    font-weight: 600;
}

.shop-tab-active::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 16px;
    right: 16px;
    height: 3px;
    background: linear-gradient(90deg, #FF6B00, #FF8C3A);
    border-radius: 3px 3px 0 0;
}

.tab-label-short {
    display: none;
}

.shop-sort-btn .sort-prefix-short {
    display: none;
}

.shop-tab-count {
    font-size: 0.8rem;
    color: #9CA3AF;
    margin-left: 2px;
}

.shop-tab-active .shop-tab-count {
    color: #FF6B00;
    opacity: 0.7;
}

/* ==========================================
   CONTENT AREA
   ========================================== */

.shop-content-area {
    padding-top: 20px;
    padding-bottom: 32px;
}

/* ==========================================
   SEARCH + SORT + VIEW CONTROLS
   ========================================== */

.shop-controls {
    display: flex;
    gap: 12px;
    align-items: center;
    flex-wrap: wrap;
    margin-bottom: 16px;
}

.shop-search-box {
    flex: 1;
    min-width: 200px;
    position: relative;
}

.shop-search-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    width: 18px;
    height: 18px;
    color: #9CA3AF;
    pointer-events: none;
}

.shop-search-input {
    width: 100%;
    padding: 12px 16px 12px 42px;
    background: #FFFFFF;
    border: 1px solid #E5E7EB;
    border-radius: 14px;
    font-size: 0.875rem;
    color: #171717;
    outline: none;
    transition: all 0.25s ease;
}

.shop-search-input:focus {
    background: #FFFFFF;
    border-color: #FF6B00;
    box-shadow: 0 0 0 3px rgba(255, 107, 0, 0.08);
}

.shop-search-input::placeholder {
    color: #9CA3AF;
}

.shop-controls-right {
    display: flex;
    gap: 8px;
    align-items: center;
    flex-shrink: 0;
}

.shop-sort-custom {
    position: relative;
}

.shop-sort-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 11px 16px;
    background: #FFFFFF;
    border: 1px solid #E5E7EB;
    border-radius: 14px;
    font-size: 0.85rem;
    color: #374151;
    cursor: pointer;
    outline: none;
    transition: all 0.2s ease;
    min-width: 170px;
    justify-content: space-between;
}

.shop-sort-btn span {
    display: flex;
    gap: 4px;
}

.shop-sort-btn strong {
    font-weight: 600;
    color: #171717;
}

.shop-sort-btn:hover {
    border-color: #D1D5DB;
    background: #F9FAFB;
}

.sort-icon {
    color: #9CA3AF;
    transition: transform 0.3s ease;
}

.sort-icon.rotate-180 {
    transform: rotate(180deg);
}

.shop-sort-dropdown-menu {
    position: absolute;
    top: calc(100% + 8px);
    right: 0;
    width: 100%;
    min-width: 220px;
    background: #FFFFFF;
    border: 1px solid #E5E7EB;
    border-radius: 16px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
    padding: 8px;
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.shop-sort-option {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    padding: 12px 16px;
    background: none;
    border: none;
    border-radius: 10px;
    font-size: 0.9rem;
    color: #374151;
    cursor: pointer;
    text-align: left;
    transition: all 0.15s ease;
}

.shop-sort-option:hover {
    background: #FFF7F0;
    color: #FF6B00;
}

.shop-sort-option.active {
    background: #FFF7F0;
    color: #FF6B00;
    font-weight: 500;
}

.sort-check-icon {
    color: #FF6B00;
}

.shop-view-toggle {
    display: flex;
    gap: 8px;
}

.shop-view-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 44px;
    height: 44px;
    background: #FFFFFF;
    border: 1px solid #E5E7EB;
    border-radius: 12px;
    color: #6B7280;
    cursor: pointer;
    transition: all 0.2s ease;
}

.shop-view-btn.active {
    color: #FFFFFF;
    background: #FF6B00;
    border-color: #FF6B00;
    box-shadow: 0 2px 8px rgba(255, 107, 0, 0.3);
}

.shop-view-btn:hover:not(.active) {
    color: #374151;
    background: #F9FAFB;
}

/* ==========================================
   CATEGORY FILTERS
   ========================================== */

.shop-categories-scroll {
    overflow-x: auto;
    scrollbar-width: none;
    -ms-overflow-style: none;
    margin-bottom: 20px;
    -webkit-overflow-scrolling: touch;
}

.shop-categories-scroll::-webkit-scrollbar {
    display: none;
}

.shop-categories {
    display: flex;
    gap: 10px;
    padding: 2px 0;
}

.shop-cat-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 9px 18px;
    background: #FFFFFF;
    border: 1.5px solid #E5E7EB;
    border-radius: 28px;
    font-size: 0.85rem;
    font-weight: 500;
    color: #374151;
    cursor: pointer;
    white-space: nowrap;
    flex-shrink: 0;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

.shop-cat-pill:hover {
    background: #FFF7F0;
    border-color: #FFD4AD;
}

.shop-cat-active {
    background: #FFF7F0;
    border-color: #FF6B00;
    color: #FF6B00;
}

/* One global orange "snake" line flowing left -> right across ALL category pills.
   A single animated variable (--cat-snake-x) is defined on the track and inherited by
   every pill; each pill's border ring shows the part of the line that overlaps it
   (--pill-x = the pill's x offset, set by JS). New categories join the flow automatically.
   Only the existing 1.5px border ring is overlaid, so layout/size are untouched. */
@property --cat-snake-x {
    syntax: '<length>';
    inherits: true;
    initial-value: -140px;
}

.shop-categories {
    --cat-tail: 140px;
    position: relative;
    animation: shop-cat-snake var(--cat-snake-dur, 3s) linear infinite;
}

.shop-cat-pill {
    position: relative;
}

.shop-cat-pill:not(.shop-cat-active)::after {
    --h: calc(var(--cat-snake-x) - var(--pill-x, 0px));
    content: '';
    position: absolute;
    inset: -1.5px;
    padding: 1.5px;
    border-radius: 30px;
    pointer-events: none;
    background: linear-gradient(
        90deg,
        rgba(255, 107, 0, 0) calc(var(--h) - var(--cat-tail)),
        rgba(255, 107, 0, 0.28) calc(var(--h) - 60px),
        rgba(255, 107, 0, 0.8) calc(var(--h) - 8px),
        #FF8A33 var(--h),
        rgba(255, 138, 51, 0) calc(var(--h) + 8px)
    );
    /* Keep only the border ring visible */
    -webkit-mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
    -webkit-mask-composite: xor;
    mask: linear-gradient(#000 0 0) content-box exclude, linear-gradient(#000 0 0);
}

@keyframes shop-cat-snake {
    from { --cat-snake-x: -140px; }
    to { --cat-snake-x: calc(var(--cat-track-w, 600px) + 140px); }
}

@media (prefers-reduced-motion: reduce) {
    .shop-categories {
        animation: none;
    }
}

.shop-cat-active .shop-cat-icon {
    transform: scale(1.1);
}

.shop-cat-icon {
    font-size: 0.9rem;
    transition: transform 0.2s ease;
}

.shop-cat-img {
    width: 26px;
    height: 26px;
    border-radius: 50%;
    object-fit: cover;
    flex-shrink: 0;
    background: #F3F4F6;
}

.shop-cat-initial {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 700;
    color: #FF6B00;
    background: #FFF1E6;
}

.shop-cat-skeleton {
    width: 104px;
    height: 42px;
    border-radius: 28px;
    flex-shrink: 0;
}

.shop-cat-label {
    letter-spacing: -0.01em;
}

/* ==========================================
   PRODUCT HEADER
   ========================================== */

.shop-product-header {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    margin-bottom: 16px;
    flex-wrap: wrap;
    gap: 4px;
}

.shop-product-header-left {
    display: flex;
    align-items: baseline;
    gap: 10px;
    flex-wrap: wrap;
}

.shop-product-heading {
    font-size: 1.25rem;
    font-weight: 700;
    color: #171717;
    letter-spacing: -0.02em;
    margin: 0;
}

.shop-product-count {
    font-size: 0.85rem;
    color: #9CA3AF;
    font-weight: 400;
}

.shop-product-header-right {
    flex-shrink: 0;
}

.shop-page-info {
    font-size: 0.8rem;
    color: #9CA3AF;
    font-weight: 500;
}

/* ==========================================
   PRODUCT GRID
   ========================================== */

.shop-product-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
}

.shop-product-grid.list-view {
    grid-template-columns: 1fr;
}

.shop-product-item {
    min-width: 0;
}

/* ==========================================
   EMPTY STATE
   ========================================== */

.shop-empty-state {
    text-align: center;
    padding: 48px 24px;
}

.shop-empty-icon {
    display: flex;
    justify-content: center;
    margin-bottom: 16px;
}

.shop-empty-text {
    font-size: 1.1rem;
    font-weight: 600;
    color: #6B7280;
    margin: 0;
}

.shop-empty-sub {
    font-size: 0.85rem;
    color: #9CA3AF;
    margin-top: 4px;
}

/* ==========================================
   PAGINATION
   ========================================== */

.shop-pagination {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 28px;
    gap: 16px;
    flex-wrap: wrap;
}

.shop-pagination-info {
    font-size: 0.85rem;
    color: #6B7280;
}

/* ==========================================
   SECTION TITLES
   ========================================== */

.shop-section-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #171717;
    letter-spacing: -0.02em;
    margin: 0 0 16px 0;
}

/* ==========================================
   REVIEWS SECTION
   ========================================== */

.shop-reviews-section {
    padding-top: 4px;
}

/* ==========================================
   ABOUT / POLICIES / CONTACT SECTIONS
   ========================================== */

.shop-about-section {
    padding-top: 4px;
    max-width: 720px;
}

.shop-about-content {
    font-size: 0.95rem;
    line-height: 1.7;
    color: #374151;
}

.shop-about-content :deep(p) {
    margin-bottom: 12px;
}

.shop-contact-info {
    display: flex;
    flex-direction: column;
    gap: 14px;
    margin-top: 8px;
}

.shop-contact-row {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 0.9rem;
    color: #374151;
}

.shop-contact-row svg {
    flex-shrink: 0;
}

/* ==========================================
   PROMOTIONAL BANNERS
   ========================================== */

.shop-banners-section {
    padding-top: 12px;
    padding-bottom: 24px;
}

/* ==========================================
   RESPONSIVE - TABLET (768px–1199px)
   ========================================== */

@media (min-width: 768px) {
    .shop-product-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
    }

    .shop-tab {
        padding: 16px 24px;
        font-size: 0.92rem;
    }

    .shop-controls {
        flex-wrap: nowrap;
    }
}

/* ==========================================
   RESPONSIVE - DESKTOP (≥1200px)
   ========================================== */

@media (min-width: 1200px) {
    .shop-product-grid {
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
    }

    .shop-content-area {
        padding-top: 24px;
        padding-bottom: 40px;
    }

    .shop-tabs-container {
        border-radius: 20px;
        padding: 0 16px;
    }

    .shop-tab {
        padding: 18px 28px;
        font-size: 0.95rem;
    }
}

@media (min-width: 1440px) {
    .shop-product-grid {
        grid-template-columns: repeat(5, 1fr);
    }
}

@media (min-width: 1600px) {
    .shop-product-grid {
        grid-template-columns: repeat(6, 1fr);
    }
}

/* ==========================================
   RESPONSIVE - MOBILE (<768px)
   ========================================== */

@media (max-width: 767px) {
    .shop-content-area {
        padding-top: 14px;
        padding-bottom: 24px;
    }

    .shop-tabs-container {
        border-radius: 14px;
    }

    .shop-tab {
        flex: 1 0 auto;
        padding: 13px 12px;
        font-size: 0.82rem;
    }

    .tab-label-full {
        display: none;
    }

    .tab-label-short {
        display: inline;
    }

    .shop-tab-active::after {
        left: 10px;
        right: 10px;
    }

    /* Search + sort + view toggle on one row, like the reference */
    .shop-controls {
        flex-wrap: nowrap;
        gap: 8px;
    }

    .shop-search-box {
        flex: 1 1 0;
        min-width: 0;
    }

    .shop-search-input {
        padding: 11px 10px 11px 36px;
        font-size: 0.82rem;
        border-radius: 12px;
    }

    .shop-search-icon {
        left: 11px;
        width: 16px;
        height: 16px;
    }

    .shop-controls-right {
        flex: 0 0 auto;
        width: auto;
        gap: 6px;
    }

    .shop-sort-btn {
        min-width: 0;
        padding: 11px 10px;
        gap: 6px;
        font-size: 0.78rem;
        border-radius: 12px;
    }

    .shop-sort-btn .sort-prefix-full {
        display: none;
    }

    .shop-sort-btn .sort-prefix-short {
        display: inline;
    }

    .shop-view-toggle {
        gap: 6px;
    }

    .shop-view-btn {
        width: 38px;
        height: 38px;
        border-radius: 10px;
    }

    .shop-view-btn svg {
        width: 16px;
        height: 16px;
    }

    .shop-product-grid {
        gap: 10px;
    }

    .shop-product-heading {
        font-size: 1.1rem;
    }

    .shop-pagination {
        flex-direction: column;
        align-items: center;
        gap: 12px;
    }

    .shop-categories {
        gap: 8px;
    }

    .shop-cat-pill {
        padding: 6px 14px 6px 8px;
        font-size: 0.8rem;
    }

    .shop-cat-img {
        width: 22px;
        height: 22px;
    }

    .shop-banners-section {
        padding-bottom: 16px;
    }
}

/* Small mobile */
@media (max-width: 374px) {
    .shop-tab {
        padding: 12px 12px;
        font-size: 0.78rem;
    }

    .shop-search-input {
        padding: 10px 14px 10px 38px;
        font-size: 0.82rem;
    }

    .shop-cat-pill {
        padding: 6px 12px;
        font-size: 0.75rem;
    }

    .shop-product-heading {
        font-size: 1rem;
    }

    .shop-product-grid {
        gap: 8px;
    }
}
</style>

