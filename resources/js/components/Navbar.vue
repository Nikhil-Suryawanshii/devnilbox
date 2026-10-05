<template>
    <header 
        class="sticky top-0 z-50 w-full transition-all duration-300 ease-in-out backdrop-blur-xl border-b"
        :class="scrolled 
            ? 'bg-white/90 shadow-sm border-slate-200/50' 
            : 'bg-white/40 border-white/20'"
    >
        <NavbarMiddle />
        <NavbarBottom class="transition-all duration-300" :class="scrolled ? 'hidden md:block' : ''" />
    </header>
</template>

<script setup>
import NavbarBottom from "../components/NavbarBottom.vue";
import NavbarMiddle from "../components/NavbarMiddle.vue";
import { ref, onMounted, onBeforeUnmount } from 'vue';


const scrolled = ref(false);

const onScroll = () => {
    scrolled.value = window.scrollY > 20;
};

onMounted(() => {
    window.addEventListener('scroll', onScroll);
});

onBeforeUnmount(() => {
    window.removeEventListener('scroll', onScroll);
});
</script>
<style lang="scss" scoped></style>
