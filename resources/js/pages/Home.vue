<template>
    <div>
        <HeroBanner :banners="banner" :ads="ads" :isLoading="isLoading" />
        <AboutSupport :isLoading="isLoading" />
        <!-- <Categories :categories="categories" :isLoading="isLoginCategory" /> -->
        <PopularProducts :products="popularProducts" :isLoading="isLoading" />
        <!-- <JustForYou :justForYou="justForYou" :isLoading="isLoading" /> -->
        <RecentlyViews :products="recentlyViewProducts" :isLoading="isLoginRecentlyView" />
    </div>
</template>

<script setup>
import { onMounted, ref } from "vue";
import AboutSupport from "../components/AboutSupport.vue";
import Categories from "../components/Categories.vue";
import HeroBanner from "../components/HeroBanner.vue";
import JustForYou from "../components/JustForYou.vue";
import PopularProducts from "../components/PopularProducts.vue";
import RecentlyViews from "../components/RecentlyViews.vue";
import { useBasketStore } from "../stores/BasketStore";
import { useMaster } from "../stores/MasterStore";

import axios from "axios";
import { useAuth } from "../stores/AuthStore";

const master = useMaster();
const basketStore = useBasketStore();

const authStore = useAuth();
const isLoading = ref(true);
const isLoginCategory = ref(true);
const isLoginRecentlyView = ref(false);

onMounted(() => {
    getData();
    master.fetchData();
    basketStore.fetchCart();
    fetchViewProducts();
    master.basketCanvas = false;
    authStore.loginModal = false;
    authStore.registerModal = false;
    authStore.showAddressModal = false;
    authStore.showChangeAddressModal = false;
});

const banner = ref([]);
const categories = ref([]);
const popularProducts = ref([]);
const topRatedShops = ref([]);
const justForYou = ref([]);
const recentlyViewProducts = ref([]);
const ads = ref([]);

const getData = () => {
    isLoading.value = true;
    axios.get("/home?page=1&per_page=12", {
        headers: {
            Authorization: authStore.token,
        },
    }).then((response) => {
        ads.value = response.data.data.ads;
        banner.value = response.data.data.banners;
        categories.value = response.data.data.categories;
        justForYou.value = response.data.data.just_for_you;
        popularProducts.value = response.data.data.popular_products;
        topRatedShops.value = response.data.data.shops.slice(0, 4);
        isLoading.value = false;
    }).catch((error) => {
        isLoading.value = false;
    });

    // fetch categories
    isLoginCategory.value = true;
    axios.get("/categories").then((response) => {
        master.categories = response.data.data.categories;
        setTimeout(() => {
            isLoginCategory.value = false;
        }, 500);
    }).catch(() => {
        isLoginCategory.value = false;
    });
};

const fetchViewProducts = () => {
    if (authStore.token) {
        isLoginRecentlyView.value = true;
        axios.get("/recently-views", {
            headers: {
                Authorization: authStore.token,
            },
        }).then((response) => {
            recentlyViewProducts.value = response.data.data.products;
            isLoginRecentlyView.value = false;
        }).catch((error) => {
            isLoginRecentlyView.value = false;
            if (error.response.status === 401) {
                authStore.token = null;
                authStore.user = null;
                authStore.addresses = [];
            }
        });
    }
};
</script>

<style scoped></style>
