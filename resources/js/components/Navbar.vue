<template>
    <div 
      class="sticky top-0 z-20 transition-colors duration-300 ease-in-out"
        :class="scrolled ? 'bg-white shadow-sm text-slate-500' : 'bg-transparent text-white'"
    >
        <NavbarMiddle />
        <NavbarBottom />
    </div>
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
