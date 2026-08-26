<template>
        <div class="min-h-screen bg-gray-50 pt-24 pb-12">
            <div class="main-container pt-8 pb-12">
                <!-- Header -->
                <div class="mb-8 text-center md:text-left">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-orange-100 border border-orange-200 text-orange-700 text-xs font-bold mb-4">
                        <span class="w-2 h-2 rounded-full bg-orange-500 animate-pulse"></span>
                        Trending Now
                    </div>
                    <h1 class="text-3xl md:text-4xl font-extrabold text-slate-900 mb-2">Most Popular Products</h1>
                    <p class="text-slate-500 max-w-2xl">Discover what everyone is buying right now. Handpicked favorites from around the globe.</p>
                </div>

                <!-- Content -->
                <div v-if="!isLoading">
                    <!-- Products Grid -->
                    <div v-if="products.length > 0" class="grid grid-cols-1 xs:grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-6">
                        <div v-for="product in products" :key="product.id" class="w-full">
                            <ProductCard :product="product" />
                        </div>
                    </div>

                    <!-- No Products -->
                    <div v-else class="flex flex-col items-center justify-center py-20 bg-white rounded-3xl border border-slate-100 shadow-sm">
                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4 text-3xl">📦</div>
                        <h3 class="text-lg font-bold text-slate-800 mb-1">{{ $t('No Products Found') }}</h3>
                        <p class="text-slate-500 text-sm">Check back later for new arrivals.</p>
                    </div>
                </div>

                <!-- Loading State -->
                <div v-else class="grid grid-cols-1 xs:grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-6">
                    <div v-for="i in 12" :key="i">
                        <SkeletonLoader class="w-full aspect-[3/4] rounded-2xl" />
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="products.length > 0 && !isLoading" class="mt-12 border-t border-slate-200 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="text-slate-500 text-sm font-medium order-2 md:order-1">
                        {{ $t('Showing') }} <span class="text-slate-900 font-bold">{{ (perPage * (currentPage - 1) + 1) }}</span> - <span class="text-slate-900 font-bold">{{ Math.min(perPage * currentPage, totalProducts) }}</span> {{ $t('of') }} <span class="text-slate-900 font-bold">{{ totalProducts }}</span> {{ $t('results') }}
                    </div>
                    
                    <div class="order-1 md:order-2">
                        <vue-awesome-paginate
                            :total-items="totalProducts"
                            :items-per-page="perPage"
                            :max-pages-shown="5"
                            v-model="currentPage"
                            :hide-prev-next-when-ends="true"
                            @click="onClickHandler"
                            class="pagination-container"
                        >
                            <template #prev-button>
                                <span class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                                    </svg>
                                </span>
                            </template>
                            <template #next-button>
                                <span class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                    </svg>
                                </span>
                            </template>
                        </vue-awesome-paginate>
                    </div>
                </div>
            </div>
        </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useMaster } from '../stores/MasterStore';
import ProductCard from '../components/ProductCard.vue';
import SkeletonLoader from '../components/SkeletonLoader.vue';
import { useAuth } from '../stores/AuthStore';
//import PublicLayout from '../layouts/PublicLayout.vue';
import axios from 'axios';

const authStore = useAuth();
const master = useMaster();
const currentPage = ref(1);
const perPage = ref(12);

const products = ref([]);
const totalProducts = ref(0);
const isLoading = ref(true);

onMounted(() => {
    fetchProducts();
    window.scrollTo(0, 0);
});

const onClickHandler = (page) => {
    currentPage.value = page;
    fetchProducts();
};

const fetchProducts = () => {
    window.scrollTo({
        top: 0,
        behavior: "smooth",
    });
    isLoading.value = true;
    axios.get('/products', {
        params: {
            page: currentPage.value,
            per_page: perPage.value,
            sort_type: 'popular_product'
        },
        headers: {
            'Accept-Language': master.locale || 'en',
            Authorization: authStore.token
        }
    }).then((response) => {
        if(response.data && response.data.data) {
            totalProducts.value = response.data.data.total;
            products.value = response.data.data.products;
        } else {
            products.value = [];
            totalProducts.value = 0;
        }
    }).catch((error) => {
        console.error("Error fetching products:", error);
        products.value = [];
    }).finally(() => {
        isLoading.value = false;
    });
};
</script>

<style>
.pagination-container {
    display: flex;
    gap: 0.5rem;
}
.paginate-buttons {
    height: 40px;
    width: 40px;
    border-radius: 0.5rem;
    cursor: pointer;
    background-color: white;
    border: 1px solid #e2e8f0;
    color: #475569;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}
.paginate-buttons:hover {
    background-color: #f8fafc;
    border-color: #cbd5e1;
}
.active-page {
    background-color: #0f172a;
    border-color: #0f172a;
    color: white;
}
.active-page:hover {
    background-color: #1e293b;
} 
</style>
