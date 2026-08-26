<template>
    <PublicLayout>
        <!-- Hero: 3 columns lg (equal, gap-0) | md: text full width + 2 cols | max-sm: stack 1→2→3 -->
        <section
            class="relative overflow-x-hidden bg-gradient-to-br from-[#fbdcb8] via-[#fcebd6] to-[#fff7ed] pt-24 pb-10 lg:pb-14">
            <div
                class="pointer-events-none absolute inset-y-0 right-0 w-2/3 bg-gradient-to-l from-orange-300/25 to-transparent blur-3xl">
            </div>

            <div class="relative z-10 mx-auto w-full max-w-[1600px] px-4 sm:px-6 lg:px-8">
                <div class="grid w-full min-w-0 grid-cols-1 gap-0 md:grid-cols-2 lg:grid-cols-7 lg:items-center">
                    <!-- 1 — Text -->
                    <div
                        class=" order-1 flex min-w-0 flex-col justify-center border-b border-orange-200/20 py-8 text-center md:col-span-2 md:border-b md:px-2 md:py-10 lg:col-span-2 lg:border-b-0 lg:border-r lg:border-orange-200/25 lg:px-4 lg:py-6 lg:text-left">
                        <div
                            class="lg:ml-0 mx-auto mb-5 inline-flex items-center gap-2 rounded-full border border-orange-200/80 bg-white/90 px-3 py-1.5 text-xs font-bold text-orange-800 shadow-sm ">
                            <span class="h-2 w-2 shrink-0 animate-pulse rounded-full bg-emerald-500"></span>
                            Live Shopping Platform
                        </div>
                        <h1
                            class="mb-5 text-4xl font-extrabold leading-tight tracking-tight text-slate-900 sm:text-5xl lg:text-[2.25rem] lg:leading-[1.12] xl:text-5xl">
                            An Easier Way to <span class="text-orange-600">Sell</span>. A Smarter Way to
                            <span class="text-orange-600">Buy</span>.
                            <span class="mt-1 block sm:mt-0 sm:inline lg:block">Your Phone</span>
                        </h1>
                        <p class="mx-auto mb-8 max-w-xl text-base leading-relaxed text-slate-600 sm:text-lg lg:mx-0">
                            Access premium products at prices you won't find online. Real-time shopping directly from
                            the world's best stores.
                        </p>
                        <div
                            class="flex flex-col items-stretch justify-center gap-3 sm:flex-row lg:max-xl:flex-col lg:max-xl:items-start sm:items-center sm:justify-center lg:justify-start">
                            <button v-if="master.appStoreLink" type="button"
                                class="flex w-full items-center justify-center gap-3 rounded-xl border border-slate-700 bg-slate-900 px-5 py-2.5 text-white shadow-lg transition-all hover:bg-slate-800 hover:shadow-xl active:scale-[0.98] sm:w-auto sm:justify-start"
                                @click="openAppStore">
                                <svg class="h-7 w-7 shrink-0" viewBox="0 0 384 512" fill="white" aria-hidden="true">
                                    <path
                                        d="M318.7 268.1c-.2-47.6 38.9-70.5 40.7-71.6-22.3-32.6-57-37.1-69.1-37.6-29.4-3-57.4 17.3-72.3 17.3-14.9 0-37.9-16.9-62.3-16.5-32 .5-61.5 18.6-78 47.2-33.3 57.7-8.5 143 23.9 189.8 15.8 22.8 34.6 48.3 59.3 47.4 23.7-.9 32.6-15.3 61.2-15.3 28.6 0 36.6 15.3 61.7 14.8 25.5-.4 41.6-23 57.3-45.9 18.1-26.4 25.6-52 25.9-53.3-.6-.2-49.7-19.1-49.9-75.9zM269.1 111.6c13.1-15.9 22-38 19.6-60.1-18.9.8-41.8 12.6-55.4 28.5-12.2 14.2-22.8 36.9-19.9 58.6 21.1 1.6 42.6-10.7 55.7-27z" />
                                </svg>
                                <div class="text-left">
                                    <div
                                        class="mb-0.5 text-[10px] whitespace-nowrap font-medium uppercase leading-none opacity-80">
                                        Download on the
                                    </div>
                                    <div
                                        class="font-sans whitespace-nowrap text-xl xl:text-[13px] 2xl:text-xl lg:max-xl:text-[16px] font-bold leading-none tracking-wide">
                                        App Store</div>
                                </div>
                            </button>
                            <button v-if="master.playStoreLink" type="button"
                                class="flex w-full items-center justify-center gap-3 rounded-xl border border-slate-700 bg-slate-900 px-5 py-2.5 text-white shadow-lg transition-all hover:bg-slate-800 hover:shadow-xl active:scale-[0.98] sm:w-auto sm:justify-start"
                                @click="openPlayStore">
                                <svg class="h-7 w-7 shrink-0" viewBox="0 0 512 512" fill="currentColor"
                                    aria-hidden="true">
                                    <defs>
                                        <linearGradient id="a" x1="49.999%" x2="49.999%" y1="0%" y2="100%">
                                            <stop offset="0%" stop-color="#00A0FF" />
                                            <stop offset="100%" stop-color="#00E5FF" />
                                        </linearGradient>

                                        <linearGradient id="b" x1="0%" x2="100%" y1="0%" y2="0%">
                                            <stop offset="0%" stop-color="#FFE000" />
                                            <stop offset="100%" stop-color="#FFB300" />
                                        </linearGradient>

                                        <linearGradient id="c" x1="0%" x2="100%" y1="0%" y2="100%">
                                            <stop offset="0%" stop-color="#FF3A44" />
                                            <stop offset="100%" stop-color="#C31162" />
                                        </linearGradient>

                                        <linearGradient id="d" x1="0%" x2="100%" y1="100%" y2="0%">
                                            <stop offset="0%" stop-color="#32A071" />
                                            <stop offset="100%" stop-color="#6DD400" />
                                        </linearGradient>
                                    </defs>

                                    <!-- Blue -->
                                    <path fill="url(#a)"
                                        d="M28 16c-9 9-14 22-14 38v404c0 16 5 29 14 38l246-240L28 16z" />

                                    <!-- Green -->
                                    <path fill="url(#d)" d="M92 54l206 202 68-68L92 54z" />

                                    <!-- Yellow -->
                                    <path fill="url(#b)" d="M434 222l-68-34-68 68 68 68 68-34c34-17 34-51 0-68z" />

                                    <!-- Red -->
                                    <path fill="url(#c)" d="M92 458l274-134-68-68L92 458z" />
                                </svg>
                                <div class="text-left">
                                    <div class="mb-0.5 text-[10px]  font-medium uppercase leading-none opacity-80">
                                        Get it on
                                    </div>
                                    <div
                                        class="font-sans whitespace-nowrap text-xl xl:text-[13px] 2xl:text-xl lg:max-xl:text-[16px] font-bold leading-none tracking-wide">
                                        Google Play
                                    </div>
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- 2 — Phone -->
                    <div
                        class="lg:col-span-2 md:-rotate-3 order-2 flex min-w-0 items-center justify-center border-orange-200/25 py-4 md:border-r lg:border-r lg:py-6">
                        <div class="relative z-20 flex shrink-0 justify-center">
                            <div
                                class="relative h-auto w-full max-w-[300px] lg:max-w-[240px] xl:max-w-[300px] shrink-0 overflow-hidden rounded-[2.5rem] border-[6px] border-slate-900 bg-white shadow-2xl sm:rounded-[3rem] sm:border-[8px]">
                                <div class="relative h-full w-full bg-slate-50">
                                    <div
                                        class="absolute inset-x-0 top-0 z-20 flex h-6 items-end justify-center bg-white pb-1">
                                        <div class="h-4 w-20 rounded-b-xl bg-slate-900"></div>
                                    </div>
                                    <div class="absolute inset-x-0 top-8 z-30 flex justify-center">
                                        <img :src="master.logo || '/public/assets/logo.png'"
                                            class="h-7 object-contain drop-shadow-lg sm:h-8" alt="" />
                                    </div>
                                    <swiper :modules="modules" :slides-per-view="1" :space-between="0" :loop="true"
                                        :autoplay="{ delay: 3000, disableOnInteraction: false }"
                                        :pagination="{ clickable: true }" class="h-full w-full">
                                        <swiper-slide v-for="banner in banners" :key="banner.id" class="h-full w-full">
                                            <img :src="banner.thumbnail" class="h-full w-full object-cover" alt="" />
                                        </swiper-slide>
                                        <swiper-slide v-if="banners.length === 0"
                                            class="flex h-full w-full items-center justify-center bg-slate-200">
                                            <span class="animate-pulse text-sm font-medium text-slate-400">
                                                Loading Deals...
                                            </span>
                                        </swiper-slide>
                                    </swiper>
                                    <div
                                        class="absolute bottom-2 left-1/2 z-20 h-1 w-28 -translate-x-1/2 rounded-full bg-slate-900/20 sm:w-32">
                                    </div>
                                </div>
                            </div>
                            <div
                                class="pointer-events-none absolute left-1/2 top-1/2 -z-10 h-[80%] w-[120%] -translate-x-1/2 -translate-y-1/2 rounded-full bg-orange-400/20 blur-3xl">
                            </div>
                        </div>
                    </div>

                    <!-- 3 — Full-width stage: fixed-size main + corner tiles use free space -->
                    <div class="lg:col-span-3 order-3 flex min-w-0 items-center justify-center py-4 lg:py-6">
                        <div
                            class="relative flex h-full w-full min-w-0 items-end justify-center overflow-hidden lg:overflow-visible">
                            <div class="pointer-events-none absolute inset-0 ">
                                <!-- Polaroid-style tiles -->
                                <div
                                    class="absolute left-3 top-[4%] z-10 w-[102px] sm:w-[120px] md:w-[140px] lg:w-[160px] -rotate-6">
                                    <div
                                        class="aspect-[4/5] overflow-hidden  rounded-3xl border-2 border-b-[10px] border-white bg-white p-0.5 pb-2 shadow-[0_0_0_1px_rgba(15,23,42,0.07),0_16px_40px_-12px_rgba(0,0,0,0.38)] ring-1 ring-black/5">
                                        <div class="absolute top-3 right-3 z-20">
                                            <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M16.1315 3.71436C14.4172 3.71436 12.9029 4.57721 12 5.8915C11.0972 4.57721 9.58289 3.71436 7.86861 3.71436C5.10289 3.71436 2.85718 5.96007 2.85718 8.72578C2.85718 14.8344 12 20.3258 12 20.3258C12 20.3258 21.1429 14.8344 21.1429 8.72578C21.1429 5.96007 18.8972 3.71436 16.1315 3.71436Z"
                                                    fill="url(#paint0_radial)" />
                                                <path opacity="0.5"
                                                    d="M18.2056 4.16016C20.9485 8.53158 18.4228 14.2687 15.3885 15.8973C12.0399 17.6973 9.74847 16.8516 5.00562 14.1602C7.70847 17.743 11.9999 20.3202 11.9999 20.3202C11.9999 20.3202 21.1428 14.8287 21.1428 8.72016C21.1428 6.6973 19.937 4.94873 18.2056 4.16016Z"
                                                    fill="url(#paint1_radial)" />
                                                <path opacity="0.5"
                                                    d="M16.1315 3.71436C14.4172 3.71436 12.9029 4.57721 12 5.8915C11.0972 4.57721 9.58289 3.71436 7.86861 3.71436C5.10289 3.71436 2.85718 5.96007 2.85718 8.72578C2.85718 14.8344 12 20.3258 12 20.3258C12 20.3258 21.1429 14.8344 21.1429 8.72578C21.1429 5.96007 18.8972 3.71436 16.1315 3.71436Z"
                                                    fill="url(#paint2_radial)" />
                                                <path opacity="0.5"
                                                    d="M16.1315 3.71436C14.4172 3.71436 12.9029 4.57721 12 5.8915C11.0972 4.57721 9.58289 3.71436 7.86861 3.71436C5.10289 3.71436 2.85718 5.96007 2.85718 8.72578C2.85718 14.8344 12 20.3258 12 20.3258C12 20.3258 21.1429 14.8344 21.1429 8.72578C21.1429 5.96007 18.8972 3.71436 16.1315 3.71436Z"
                                                    fill="url(#paint3_radial)" />
                                                <path opacity="0.24"
                                                    d="M10.7486 5.74883C11.2514 6.93169 10.1371 8.5374 8.25714 9.33169C6.37714 10.126 4.45143 9.8174 3.94857 8.64026C3.44571 7.46312 4.56 5.85169 6.44 5.0574C8.32 4.26312 10.2457 4.56597 10.7486 5.74883Z"
                                                    fill="url(#paint4_radial)" />
                                                <path opacity="0.24"
                                                    d="M16.8742 4.78885C17.5885 5.57742 17.1485 7.13742 15.8971 8.26885C14.6456 9.40028 13.0513 9.68028 12.3371 8.8917C11.6228 8.10313 12.0628 6.54313 13.3142 5.41171C14.5656 4.28028 16.1599 4.00028 16.8742 4.78885Z"
                                                    fill="url(#paint5_radial)" />
                                                <path opacity="0.32"
                                                    d="M16.2229 5.04578C18.7372 5.90293 21.1372 9.61721 17.0801 14.2458C14.6515 17.0172 12.0001 18.4172 8.62866 17.8686C10.4515 19.3886 12.0058 20.3258 12.0058 20.3258C12.0058 20.3258 21.1487 14.8344 21.1487 8.72578C21.1429 5.96007 18.8972 3.71436 16.1315 3.71436C14.4172 3.71436 12.9029 4.57721 12.0001 5.8915C12.0001 5.8915 14.3829 4.41721 16.2229 5.04578Z"
                                                    fill="url(#paint6_linear)" />
                                                <defs>
                                                    <radialGradient id="paint0_radial" cx="0" cy="0" r="1"
                                                        gradientUnits="userSpaceOnUse"
                                                        gradientTransform="translate(9.38479 8.34769) rotate(-29.408) scale(14.3064 11.3486)">
                                                        <stop offset="0.2479" stop-color="#FF0000" />
                                                        <stop offset="0.8639" stop-color="#C20000" />
                                                    </radialGradient>
                                                    <radialGradient id="paint1_radial" cx="0" cy="0" r="1"
                                                        gradientUnits="userSpaceOnUse"
                                                        gradientTransform="translate(9.7385 7.47018) rotate(-29.408) scale(12.3173 9.77078)">
                                                        <stop offset="0.2479" stop-color="#FF0000" />
                                                        <stop offset="1" stop-color="#C20000" />
                                                    </radialGradient>
                                                    <radialGradient id="paint2_radial" cx="0" cy="0" r="1"
                                                        gradientUnits="userSpaceOnUse"
                                                        gradientTransform="translate(9.38479 8.34769) rotate(-29.408) scale(14.3064 11.3486)">
                                                        <stop stop-color="white" stop-opacity="0.25" />
                                                        <stop offset="1" stop-color="white" stop-opacity="0" />
                                                    </radialGradient>
                                                    <radialGradient id="paint3_radial" cx="0" cy="0" r="1"
                                                        gradientUnits="userSpaceOnUse"
                                                        gradientTransform="translate(14.5277 13.2044) rotate(-26.296) scale(10.4431 5.16038)">
                                                        <stop stop-color="#BD2719" stop-opacity="0.25" />
                                                        <stop offset="1" stop-color="#BD2719" stop-opacity="0" />
                                                    </radialGradient>
                                                    <radialGradient id="paint4_radial" cx="0" cy="0" r="1"
                                                        gradientUnits="userSpaceOnUse"
                                                        gradientTransform="translate(7.34746 7.19453) rotate(-21.6908) scale(3.71252 2.30616)">
                                                        <stop stop-color="white" />
                                                        <stop offset="1" stop-color="white" stop-opacity="0" />
                                                    </radialGradient>
                                                    <radialGradient id="paint5_radial" cx="0" cy="0" r="1"
                                                        gradientUnits="userSpaceOnUse"
                                                        gradientTransform="translate(14.6004 6.84619) rotate(-40.7634) scale(3.07376 1.9095)">
                                                        <stop stop-color="white" />
                                                        <stop offset="1" stop-color="white" stop-opacity="0" />
                                                    </radialGradient>
                                                    <linearGradient id="paint6_linear" x1="13.8868" y1="26.8498"
                                                        x2="15.6583" y2="2.96408" gradientUnits="userSpaceOnUse">
                                                        <stop stop-color="#860805" />
                                                        <stop offset="1" stop-color="#BD2719" stop-opacity="0" />
                                                    </linearGradient>
                                                </defs>
                                            </svg>
                                        </div>
                                        <img :src="watchHero" class="h-full w-full  rounded-3xl object-cover" alt="" />
                                    </div>
                                </div>
                                <div
                                    class="absolute right-2 top-[2%] z-10 w-[112px] sm:w-[120px] md:w-[130px] lg:w-[140px] rotate-6">
                                    <div
                                        class="aspect-[3/5] overflow-hidden  rounded-3xl border-2 border-b-[10px] border-white bg-white p-0.5 pb-2 shadow-[0_0_0_1px_rgba(15,23,42,0.07),0_16px_40px_-12px_rgba(0,0,0,0.38)] ring-1 ring-black/5">
                                        <div class="absolute top-3 right-3 z-20">
                                            <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M16.1315 3.71436C14.4172 3.71436 12.9029 4.57721 12 5.8915C11.0972 4.57721 9.58289 3.71436 7.86861 3.71436C5.10289 3.71436 2.85718 5.96007 2.85718 8.72578C2.85718 14.8344 12 20.3258 12 20.3258C12 20.3258 21.1429 14.8344 21.1429 8.72578C21.1429 5.96007 18.8972 3.71436 16.1315 3.71436Z"
                                                    fill="url(#paint0_radial)" />
                                                <path opacity="0.5"
                                                    d="M18.2056 4.16016C20.9485 8.53158 18.4228 14.2687 15.3885 15.8973C12.0399 17.6973 9.74847 16.8516 5.00562 14.1602C7.70847 17.743 11.9999 20.3202 11.9999 20.3202C11.9999 20.3202 21.1428 14.8287 21.1428 8.72016C21.1428 6.6973 19.937 4.94873 18.2056 4.16016Z"
                                                    fill="url(#paint1_radial)" />
                                                <path opacity="0.5"
                                                    d="M16.1315 3.71436C14.4172 3.71436 12.9029 4.57721 12 5.8915C11.0972 4.57721 9.58289 3.71436 7.86861 3.71436C5.10289 3.71436 2.85718 5.96007 2.85718 8.72578C2.85718 14.8344 12 20.3258 12 20.3258C12 20.3258 21.1429 14.8344 21.1429 8.72578C21.1429 5.96007 18.8972 3.71436 16.1315 3.71436Z"
                                                    fill="url(#paint2_radial)" />
                                                <path opacity="0.5"
                                                    d="M16.1315 3.71436C14.4172 3.71436 12.9029 4.57721 12 5.8915C11.0972 4.57721 9.58289 3.71436 7.86861 3.71436C5.10289 3.71436 2.85718 5.96007 2.85718 8.72578C2.85718 14.8344 12 20.3258 12 20.3258C12 20.3258 21.1429 14.8344 21.1429 8.72578C21.1429 5.96007 18.8972 3.71436 16.1315 3.71436Z"
                                                    fill="url(#paint3_radial)" />
                                                <path opacity="0.24"
                                                    d="M10.7486 5.74883C11.2514 6.93169 10.1371 8.5374 8.25714 9.33169C6.37714 10.126 4.45143 9.8174 3.94857 8.64026C3.44571 7.46312 4.56 5.85169 6.44 5.0574C8.32 4.26312 10.2457 4.56597 10.7486 5.74883Z"
                                                    fill="url(#paint4_radial)" />
                                                <path opacity="0.24"
                                                    d="M16.8742 4.78885C17.5885 5.57742 17.1485 7.13742 15.8971 8.26885C14.6456 9.40028 13.0513 9.68028 12.3371 8.8917C11.6228 8.10313 12.0628 6.54313 13.3142 5.41171C14.5656 4.28028 16.1599 4.00028 16.8742 4.78885Z"
                                                    fill="url(#paint5_radial)" />
                                                <path opacity="0.32"
                                                    d="M16.2229 5.04578C18.7372 5.90293 21.1372 9.61721 17.0801 14.2458C14.6515 17.0172 12.0001 18.4172 8.62866 17.8686C10.4515 19.3886 12.0058 20.3258 12.0058 20.3258C12.0058 20.3258 21.1487 14.8344 21.1487 8.72578C21.1429 5.96007 18.8972 3.71436 16.1315 3.71436C14.4172 3.71436 12.9029 4.57721 12.0001 5.8915C12.0001 5.8915 14.3829 4.41721 16.2229 5.04578Z"
                                                    fill="url(#paint6_linear)" />
                                                <defs>
                                                    <radialGradient id="paint0_radial" cx="0" cy="0" r="1"
                                                        gradientUnits="userSpaceOnUse"
                                                        gradientTransform="translate(9.38479 8.34769) rotate(-29.408) scale(14.3064 11.3486)">
                                                        <stop offset="0.2479" stop-color="#FF0000" />
                                                        <stop offset="0.8639" stop-color="#C20000" />
                                                    </radialGradient>
                                                    <radialGradient id="paint1_radial" cx="0" cy="0" r="1"
                                                        gradientUnits="userSpaceOnUse"
                                                        gradientTransform="translate(9.7385 7.47018) rotate(-29.408) scale(12.3173 9.77078)">
                                                        <stop offset="0.2479" stop-color="#FF0000" />
                                                        <stop offset="1" stop-color="#C20000" />
                                                    </radialGradient>
                                                    <radialGradient id="paint2_radial" cx="0" cy="0" r="1"
                                                        gradientUnits="userSpaceOnUse"
                                                        gradientTransform="translate(9.38479 8.34769) rotate(-29.408) scale(14.3064 11.3486)">
                                                        <stop stop-color="white" stop-opacity="0.25" />
                                                        <stop offset="1" stop-color="white" stop-opacity="0" />
                                                    </radialGradient>
                                                    <radialGradient id="paint3_radial" cx="0" cy="0" r="1"
                                                        gradientUnits="userSpaceOnUse"
                                                        gradientTransform="translate(14.5277 13.2044) rotate(-26.296) scale(10.4431 5.16038)">
                                                        <stop stop-color="#BD2719" stop-opacity="0.25" />
                                                        <stop offset="1" stop-color="#BD2719" stop-opacity="0" />
                                                    </radialGradient>
                                                    <radialGradient id="paint4_radial" cx="0" cy="0" r="1"
                                                        gradientUnits="userSpaceOnUse"
                                                        gradientTransform="translate(7.34746 7.19453) rotate(-21.6908) scale(3.71252 2.30616)">
                                                        <stop stop-color="white" />
                                                        <stop offset="1" stop-color="white" stop-opacity="0" />
                                                    </radialGradient>
                                                    <radialGradient id="paint5_radial" cx="0" cy="0" r="1"
                                                        gradientUnits="userSpaceOnUse"
                                                        gradientTransform="translate(14.6004 6.84619) rotate(-40.7634) scale(3.07376 1.9095)">
                                                        <stop stop-color="white" />
                                                        <stop offset="1" stop-color="white" stop-opacity="0" />
                                                    </radialGradient>
                                                    <linearGradient id="paint6_linear" x1="13.8868" y1="26.8498"
                                                        x2="15.6583" y2="2.96408" gradientUnits="userSpaceOnUse">
                                                        <stop stop-color="#860805" />
                                                        <stop offset="1" stop-color="#BD2719" stop-opacity="0" />
                                                    </linearGradient>
                                                </defs>
                                            </svg>
                                        </div>

                                        <img :src="coat" class="h-full w-full  rounded-3xl object-cover" alt="" />
                                    </div>
                                </div>
                                <div
                                    class="absolute bottom-[4%] md:bottom-[19%] lg:bottom-[4%] left-4 z-10 w-[102px] sm:w-[120px] md:w-[130px] lg:w-[150px] -rotate-3">
                                    <div
                                        class="aspect-[4/5] overflow-hidden  rounded-3xl border-2 border-b-[10px] border-white bg-white p-0.5 pb-2 shadow-[0_0_0_1px_rgba(15,23,42,0.07),0_16px_40px_-12px_rgba(0,0,0,0.38)] ring-1 ring-black/5">
                                        <div class="absolute top-3 right-3 z-20">
                                            <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M16.1315 3.71436C14.4172 3.71436 12.9029 4.57721 12 5.8915C11.0972 4.57721 9.58289 3.71436 7.86861 3.71436C5.10289 3.71436 2.85718 5.96007 2.85718 8.72578C2.85718 14.8344 12 20.3258 12 20.3258C12 20.3258 21.1429 14.8344 21.1429 8.72578C21.1429 5.96007 18.8972 3.71436 16.1315 3.71436Z"
                                                    fill="url(#paint0_radial)" />
                                                <path opacity="0.5"
                                                    d="M18.2056 4.16016C20.9485 8.53158 18.4228 14.2687 15.3885 15.8973C12.0399 17.6973 9.74847 16.8516 5.00562 14.1602C7.70847 17.743 11.9999 20.3202 11.9999 20.3202C11.9999 20.3202 21.1428 14.8287 21.1428 8.72016C21.1428 6.6973 19.937 4.94873 18.2056 4.16016Z"
                                                    fill="url(#paint1_radial)" />
                                                <path opacity="0.5"
                                                    d="M16.1315 3.71436C14.4172 3.71436 12.9029 4.57721 12 5.8915C11.0972 4.57721 9.58289 3.71436 7.86861 3.71436C5.10289 3.71436 2.85718 5.96007 2.85718 8.72578C2.85718 14.8344 12 20.3258 12 20.3258C12 20.3258 21.1429 14.8344 21.1429 8.72578C21.1429 5.96007 18.8972 3.71436 16.1315 3.71436Z"
                                                    fill="url(#paint2_radial)" />
                                                <path opacity="0.5"
                                                    d="M16.1315 3.71436C14.4172 3.71436 12.9029 4.57721 12 5.8915C11.0972 4.57721 9.58289 3.71436 7.86861 3.71436C5.10289 3.71436 2.85718 5.96007 2.85718 8.72578C2.85718 14.8344 12 20.3258 12 20.3258C12 20.3258 21.1429 14.8344 21.1429 8.72578C21.1429 5.96007 18.8972 3.71436 16.1315 3.71436Z"
                                                    fill="url(#paint3_radial)" />
                                                <path opacity="0.24"
                                                    d="M10.7486 5.74883C11.2514 6.93169 10.1371 8.5374 8.25714 9.33169C6.37714 10.126 4.45143 9.8174 3.94857 8.64026C3.44571 7.46312 4.56 5.85169 6.44 5.0574C8.32 4.26312 10.2457 4.56597 10.7486 5.74883Z"
                                                    fill="url(#paint4_radial)" />
                                                <path opacity="0.24"
                                                    d="M16.8742 4.78885C17.5885 5.57742 17.1485 7.13742 15.8971 8.26885C14.6456 9.40028 13.0513 9.68028 12.3371 8.8917C11.6228 8.10313 12.0628 6.54313 13.3142 5.41171C14.5656 4.28028 16.1599 4.00028 16.8742 4.78885Z"
                                                    fill="url(#paint5_radial)" />
                                                <path opacity="0.32"
                                                    d="M16.2229 5.04578C18.7372 5.90293 21.1372 9.61721 17.0801 14.2458C14.6515 17.0172 12.0001 18.4172 8.62866 17.8686C10.4515 19.3886 12.0058 20.3258 12.0058 20.3258C12.0058 20.3258 21.1487 14.8344 21.1487 8.72578C21.1429 5.96007 18.8972 3.71436 16.1315 3.71436C14.4172 3.71436 12.9029 4.57721 12.0001 5.8915C12.0001 5.8915 14.3829 4.41721 16.2229 5.04578Z"
                                                    fill="url(#paint6_linear)" />
                                                <defs>
                                                    <radialGradient id="paint0_radial" cx="0" cy="0" r="1"
                                                        gradientUnits="userSpaceOnUse"
                                                        gradientTransform="translate(9.38479 8.34769) rotate(-29.408) scale(14.3064 11.3486)">
                                                        <stop offset="0.2479" stop-color="#FF0000" />
                                                        <stop offset="0.8639" stop-color="#C20000" />
                                                    </radialGradient>
                                                    <radialGradient id="paint1_radial" cx="0" cy="0" r="1"
                                                        gradientUnits="userSpaceOnUse"
                                                        gradientTransform="translate(9.7385 7.47018) rotate(-29.408) scale(12.3173 9.77078)">
                                                        <stop offset="0.2479" stop-color="#FF0000" />
                                                        <stop offset="1" stop-color="#C20000" />
                                                    </radialGradient>
                                                    <radialGradient id="paint2_radial" cx="0" cy="0" r="1"
                                                        gradientUnits="userSpaceOnUse"
                                                        gradientTransform="translate(9.38479 8.34769) rotate(-29.408) scale(14.3064 11.3486)">
                                                        <stop stop-color="white" stop-opacity="0.25" />
                                                        <stop offset="1" stop-color="white" stop-opacity="0" />
                                                    </radialGradient>
                                                    <radialGradient id="paint3_radial" cx="0" cy="0" r="1"
                                                        gradientUnits="userSpaceOnUse"
                                                        gradientTransform="translate(14.5277 13.2044) rotate(-26.296) scale(10.4431 5.16038)">
                                                        <stop stop-color="#BD2719" stop-opacity="0.25" />
                                                        <stop offset="1" stop-color="#BD2719" stop-opacity="0" />
                                                    </radialGradient>
                                                    <radialGradient id="paint4_radial" cx="0" cy="0" r="1"
                                                        gradientUnits="userSpaceOnUse"
                                                        gradientTransform="translate(7.34746 7.19453) rotate(-21.6908) scale(3.71252 2.30616)">
                                                        <stop stop-color="white" />
                                                        <stop offset="1" stop-color="white" stop-opacity="0" />
                                                    </radialGradient>
                                                    <radialGradient id="paint5_radial" cx="0" cy="0" r="1"
                                                        gradientUnits="userSpaceOnUse"
                                                        gradientTransform="translate(14.6004 6.84619) rotate(-40.7634) scale(3.07376 1.9095)">
                                                        <stop stop-color="white" />
                                                        <stop offset="1" stop-color="white" stop-opacity="0" />
                                                    </radialGradient>
                                                    <linearGradient id="paint6_linear" x1="13.8868" y1="26.8498"
                                                        x2="15.6583" y2="2.96408" gradientUnits="userSpaceOnUse">
                                                        <stop stop-color="#860805" />
                                                        <stop offset="1" stop-color="#BD2719" stop-opacity="0" />
                                                    </linearGradient>
                                                </defs>
                                            </svg>
                                        </div>

                                        <img :src="bag" class="h-full w-full  rounded-3xl object-cover" alt="" />
                                    </div>
                                </div>
                                <div
                                    class="absolute bottom-[4%] md:bottom-[29%] lg:bottom-[4%] right-2 z-[100] w-[92px] sm:w-[120px] md:w-[130px] lg:w-[140px] rotate-6">
                                    <div
                                        class="aspect-[4/5] overflow-hidden  rounded-3xl border-2 border-b-[10px] border-white bg-white p-0.5 pb-2 shadow-[0_0_0_1px_rgba(15,23,42,0.07),0_16px_40px_-12px_rgba(0,0,0,0.38)] ring-1 ring-black/5">
                                        <div class="absolute top-3 right-3 z-20">
                                            <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M16.1315 3.71436C14.4172 3.71436 12.9029 4.57721 12 5.8915C11.0972 4.57721 9.58289 3.71436 7.86861 3.71436C5.10289 3.71436 2.85718 5.96007 2.85718 8.72578C2.85718 14.8344 12 20.3258 12 20.3258C12 20.3258 21.1429 14.8344 21.1429 8.72578C21.1429 5.96007 18.8972 3.71436 16.1315 3.71436Z"
                                                    fill="url(#paint0_radial)" />
                                                <path opacity="0.5"
                                                    d="M18.2056 4.16016C20.9485 8.53158 18.4228 14.2687 15.3885 15.8973C12.0399 17.6973 9.74847 16.8516 5.00562 14.1602C7.70847 17.743 11.9999 20.3202 11.9999 20.3202C11.9999 20.3202 21.1428 14.8287 21.1428 8.72016C21.1428 6.6973 19.937 4.94873 18.2056 4.16016Z"
                                                    fill="url(#paint1_radial)" />
                                                <path opacity="0.5"
                                                    d="M16.1315 3.71436C14.4172 3.71436 12.9029 4.57721 12 5.8915C11.0972 4.57721 9.58289 3.71436 7.86861 3.71436C5.10289 3.71436 2.85718 5.96007 2.85718 8.72578C2.85718 14.8344 12 20.3258 12 20.3258C12 20.3258 21.1429 14.8344 21.1429 8.72578C21.1429 5.96007 18.8972 3.71436 16.1315 3.71436Z"
                                                    fill="url(#paint2_radial)" />
                                                <path opacity="0.5"
                                                    d="M16.1315 3.71436C14.4172 3.71436 12.9029 4.57721 12 5.8915C11.0972 4.57721 9.58289 3.71436 7.86861 3.71436C5.10289 3.71436 2.85718 5.96007 2.85718 8.72578C2.85718 14.8344 12 20.3258 12 20.3258C12 20.3258 21.1429 14.8344 21.1429 8.72578C21.1429 5.96007 18.8972 3.71436 16.1315 3.71436Z"
                                                    fill="url(#paint3_radial)" />
                                                <path opacity="0.24"
                                                    d="M10.7486 5.74883C11.2514 6.93169 10.1371 8.5374 8.25714 9.33169C6.37714 10.126 4.45143 9.8174 3.94857 8.64026C3.44571 7.46312 4.56 5.85169 6.44 5.0574C8.32 4.26312 10.2457 4.56597 10.7486 5.74883Z"
                                                    fill="url(#paint4_radial)" />
                                                <path opacity="0.24"
                                                    d="M16.8742 4.78885C17.5885 5.57742 17.1485 7.13742 15.8971 8.26885C14.6456 9.40028 13.0513 9.68028 12.3371 8.8917C11.6228 8.10313 12.0628 6.54313 13.3142 5.41171C14.5656 4.28028 16.1599 4.00028 16.8742 4.78885Z"
                                                    fill="url(#paint5_radial)" />
                                                <path opacity="0.32"
                                                    d="M16.2229 5.04578C18.7372 5.90293 21.1372 9.61721 17.0801 14.2458C14.6515 17.0172 12.0001 18.4172 8.62866 17.8686C10.4515 19.3886 12.0058 20.3258 12.0058 20.3258C12.0058 20.3258 21.1487 14.8344 21.1487 8.72578C21.1429 5.96007 18.8972 3.71436 16.1315 3.71436C14.4172 3.71436 12.9029 4.57721 12.0001 5.8915C12.0001 5.8915 14.3829 4.41721 16.2229 5.04578Z"
                                                    fill="url(#paint6_linear)" />
                                                <defs>
                                                    <radialGradient id="paint0_radial" cx="0" cy="0" r="1"
                                                        gradientUnits="userSpaceOnUse"
                                                        gradientTransform="translate(9.38479 8.34769) rotate(-29.408) scale(14.3064 11.3486)">
                                                        <stop offset="0.2479" stop-color="#FF0000" />
                                                        <stop offset="0.8639" stop-color="#C20000" />
                                                    </radialGradient>
                                                    <radialGradient id="paint1_radial" cx="0" cy="0" r="1"
                                                        gradientUnits="userSpaceOnUse"
                                                        gradientTransform="translate(9.7385 7.47018) rotate(-29.408) scale(12.3173 9.77078)">
                                                        <stop offset="0.2479" stop-color="#FF0000" />
                                                        <stop offset="1" stop-color="#C20000" />
                                                    </radialGradient>
                                                    <radialGradient id="paint2_radial" cx="0" cy="0" r="1"
                                                        gradientUnits="userSpaceOnUse"
                                                        gradientTransform="translate(9.38479 8.34769) rotate(-29.408) scale(14.3064 11.3486)">
                                                        <stop stop-color="white" stop-opacity="0.25" />
                                                        <stop offset="1" stop-color="white" stop-opacity="0" />
                                                    </radialGradient>
                                                    <radialGradient id="paint3_radial" cx="0" cy="0" r="1"
                                                        gradientUnits="userSpaceOnUse"
                                                        gradientTransform="translate(14.5277 13.2044) rotate(-26.296) scale(10.4431 5.16038)">
                                                        <stop stop-color="#BD2719" stop-opacity="0.25" />
                                                        <stop offset="1" stop-color="#BD2719" stop-opacity="0" />
                                                    </radialGradient>
                                                    <radialGradient id="paint4_radial" cx="0" cy="0" r="1"
                                                        gradientUnits="userSpaceOnUse"
                                                        gradientTransform="translate(7.34746 7.19453) rotate(-21.6908) scale(3.71252 2.30616)">
                                                        <stop stop-color="white" />
                                                        <stop offset="1" stop-color="white" stop-opacity="0" />
                                                    </radialGradient>
                                                    <radialGradient id="paint5_radial" cx="0" cy="0" r="1"
                                                        gradientUnits="userSpaceOnUse"
                                                        gradientTransform="translate(14.6004 6.84619) rotate(-40.7634) scale(3.07376 1.9095)">
                                                        <stop stop-color="white" />
                                                        <stop offset="1" stop-color="white" stop-opacity="0" />
                                                    </radialGradient>
                                                    <linearGradient id="paint6_linear" x1="13.8868" y1="26.8498"
                                                        x2="15.6583" y2="2.96408" gradientUnits="userSpaceOnUse">
                                                        <stop stop-color="#860805" />
                                                        <stop offset="1" stop-color="#BD2719" stop-opacity="0" />
                                                    </linearGradient>
                                                </defs>
                                            </svg>
                                        </div>
                               <img :src="shoes" class="h-full w-full  rounded-3xl object-cover" alt="" />
                                    </div>
                                </div>
                            </div>
                            <img :src="hero_girl" alt="" loading="eager"
                                class="pointer-events-auto relative z-40 mx-auto block h-auto max-sm:w-[300px] sm:w-[433px] lg:w-[450px] xl:w-[533px] max-w-none shrink-0 object-contain object-bottom drop-shadow-2xl" />
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Worldwide Shipping Section -->
        <section class="py-16 bg-[#fffaf5]">
            <div class="container mx-auto px-4 text-center">
                <div class="inline-flex items-center gap-2 bg-gray-200/50 px-4 py-2 rounded-full mb-6">
                    <span class="text-2xl">🌍</span>
                    <h2 class="text-2xl md:text-3xl font-bold text-slate-800">Worldwide Shipping Available!</h2>
                </div>
                <p class="text-slate-600 mb-10 max-w-2xl mx-auto text-lg">Fast and Reliable Delivery to Any Corner of
                    the World</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 max-w-4xl mx-auto">
                    <!-- Turkey -->
                    <div
                        class="bg-white p-4 rounded-xl shadow-sm border border-slate-100 flex items-center gap-4 hover:shadow-md transition-all hover:scale-105 cursor-default">
                        <img src="https://flagcdn.com/w80/tr.png" alt="Turkey"
                            class="w-10 h-7 object-cover rounded shadow-sm" />
                        <div class="text-left">
                            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">TR</div>
                            <div class="font-bold text-slate-800">Turkey</div>
                        </div>
                    </div>

                    <!-- North Macedonia -->
                    <div
                        class="bg-white p-4 rounded-xl shadow-sm border border-slate-100 flex items-center gap-4 hover:shadow-md transition-all hover:scale-105 cursor-default">
                        <img src="https://flagcdn.com/w80/mk.png" alt="North Macedonia"
                            class="w-10 h-7 object-cover rounded shadow-sm" />
                        <div class="text-left">
                            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">MK</div>
                            <div class="font-bold text-slate-800">North Macedonia</div>
                        </div>
                    </div>

                    <!-- Ukraine -->
                    <div
                        class="bg-white p-4 rounded-xl shadow-sm border border-slate-100 flex items-center gap-4 hover:shadow-md transition-all hover:scale-105 cursor-default">
                        <img src="https://flagcdn.com/w80/ua.png" alt="Ukraine"
                            class="w-10 h-7 object-cover rounded shadow-sm" />
                        <div class="text-left">
                            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">UA</div>
                            <div class="font-bold text-slate-800">Ukraine</div>
                        </div>
                    </div>

                    <!-- Kosovo -->
                    <div
                        class="bg-white p-4 rounded-xl shadow-sm border border-slate-100 flex items-center gap-4 hover:shadow-md transition-all hover:scale-105 cursor-default">
                        <img src="https://flagcdn.com/w80/xk.png" alt="Kosovo"
                            class="w-10 h-7 object-cover rounded shadow-sm" />
                        <div class="text-left">
                            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">XK</div>
                            <div class="font-bold text-slate-800">Kosovo</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Trending Products Section -->
        <section class="bg-white py-16 sm:py-20">
            <div class="container mx-auto max-w-6xl px-4">
                <h2 class="mb-8 text-center text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">
                    Trending <span class="text-orange-600">Products</span>
                </h2>

                <div class="relative mx-auto mb-8 max-w-2xl">
                    <input v-model="searchInput" type="search" placeholder="Search for products, brands and more..."
                        class="w-full rounded-full border border-slate-200 bg-slate-50 py-3.5 pl-5 pr-12 text-sm text-slate-800 shadow-inner outline-none ring-orange-500/30 transition focus:border-orange-300 focus:bg-white focus:ring-2"
                        @input="debouncedSearch" @keydown.enter.prevent="runSearchNow" />
                    <span class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-slate-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                </div>

                <div class="mb-10 -mx-1 flex gap-2 overflow-x-auto px-1 pb-2">
                    <button type="button"
                        class="shrink-0 rounded-full px-4 py-2 text-sm font-semibold transition sm:px-5"
                        :class="categoryId === null ? 'bg-orange-500 text-white shadow-md' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                        @click="setCategory(null)">All</button>
                    <button v-for="c in categories" :key="c.id" type="button"
                        class="shrink-0 rounded-full px-4 py-2 text-sm font-semibold transition sm:px-5"
                        :class="categoryId === c.id ? 'bg-orange-500 text-white shadow-md' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                        @click="setCategory(c.id)">{{ c.name }}</button>
                </div>

                <div class="grid grid-cols-2 items-stretch gap-3 sm:grid-cols-3 sm:gap-4 md:grid-cols-4 lg:grid-cols-5">
                    <ProductCard
                        v-for="product in products"
                        :key="product.id"
                        :product="product"
                    />

                    <div
                        v-for="i in (loading && !products.length ? initialSkeletonCount : 0)"
                        :key="'sk-' + i"
                        class="h-40 animate-pulse rounded-2xl bg-slate-100 sm:h-44 md:h-48"
                    ></div>
                </div>                <p v-if="loadError" class="mt-8 text-center text-sm text-red-600">{{ loadError }}</p>
                <p v-else-if="!loading && !products.length" class="mt-8 text-center text-slate-500">No products to show.
                </p>
                <p v-if="loading && products.length" class="mt-6 text-center text-sm text-slate-500">Loading more…</p>
                <div ref="loadTrigger" class="h-2 w-full" aria-hidden="true"></div>
            </div>
        </section>

        <!-- How It Works -->
        <section class="py-24 px-4 bg-slate-50 border-y border-slate-200">
            <div class="container mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-5xl font-bold mb-4 text-slate-900">How It Works</h2>
                    <p class="text-slate-500 max-w-xl mx-auto">From the boutique shelf to your doorstep in a few simple
                        steps.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <!-- Step 1 -->
                    <div
                        class="bg-white p-8 rounded-2xl border border-slate-100 shadow-sm hover:shadow-lg hover:border-orange-100 transition-all">
                        <div
                            class="w-14 h-14 bg-orange-100 text-orange-600 rounded-xl flex items-center justify-center text-2xl mb-6 shadow-sm">
                            📸</div>
                        <h3 class="text-xl font-bold mb-3 text-slate-900">Live Looks & Real Pricing</h3>
                        <p class="text-sm text-slate-500 leading-relaxed">See up-to-date photos and genuine prices
                            shared straight from boutiques by our shoppers.</p>
                    </div>

                    <!-- Step 2 -->
                    <div
                        class="bg-white p-8 rounded-2xl border border-slate-100 shadow-sm hover:shadow-lg hover:border-orange-100 transition-all">
                        <div
                            class="w-14 h-14 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-2xl mb-6 shadow-sm">
                            🔍</div>
                        <h3 class="text-xl font-bold mb-3 text-slate-900">Discover & Select</h3>
                        <p class="text-sm text-slate-500 leading-relaxed">Scroll through a curated collection and find
                            standout pieces from top global brands.</p>
                    </div>

                    <!-- Step 3 -->
                    <div
                        class="bg-white p-8 rounded-2xl border border-slate-100 shadow-sm hover:shadow-lg hover:border-orange-100 transition-all">
                        <div
                            class="w-14 h-14 bg-green-50 text-green-600 rounded-xl flex items-center justify-center text-2xl mb-6 shadow-sm">
                            💬</div>
                        <h3 class="text-xl font-bold mb-3 text-slate-900">Chat & Ask</h3>
                        <p class="text-sm text-slate-500 leading-relaxed">Message shoppers, request videos, and get all
                            the details you need before you buy.</p>
                    </div>

                    <!-- Step 4 -->
                    <div
                        class="bg-white p-8 rounded-2xl border border-slate-100 shadow-sm hover:shadow-lg hover:border-orange-100 transition-all">
                        <div
                            class="w-14 h-14 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center text-2xl mb-6 shadow-sm">
                            📦</div>
                        <h3 class="text-xl font-bold mb-3 text-slate-900">Checkout & Ship</h3>
                        <p class="text-sm text-slate-500 leading-relaxed">Confirm your order, and our shoppers will take
                            care of purchasing and safe delivery.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="py-20 bg-white text-slate-900">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-12 text-center">
                    <div>
                        <div class="text-4xl md:text-5xl font-black mb-2 text-orange-600">5+</div>
                        <div class="text-sm font-bold uppercase tracking-wider opacity-80 text-slate-600">Years of
                            Excellence</div>
                    </div>
                    <div>
                        <div class="text-4xl md:text-5xl font-black mb-2 text-orange-600">50k+</div>
                        <div class="text-sm font-bold uppercase tracking-wider opacity-80 text-slate-600">Packages
                            Delivered</div>
                    </div>
                    <div>
                        <div class="text-4xl md:text-5xl font-black mb-2 text-orange-600">100k+</div>
                        <div class="text-sm font-bold uppercase tracking-wider opacity-80 text-slate-600">Products Sold
                        </div>
                    </div>
                    <div>
                        <div class="text-4xl md:text-5xl font-black mb-2 text-orange-600">100%</div>
                        <div class="text-sm font-bold uppercase tracking-wider opacity-80 text-slate-600">Secure Payment
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>

<script setup>
import { ref, onMounted, onUnmounted, nextTick, watch } from 'vue';
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Autoplay, Pagination } from 'swiper/modules';
import axios from 'axios';
import { useToast } from 'vue-toastification';
import 'swiper/css';
import 'swiper/css/pagination';
import PublicLayout from '../layouts/PublicLayout.vue';
import { useMaster } from '../stores/MasterStore';
import { useAuth } from '../stores/AuthStore';
import { useRouter } from 'vue-router';
import hero_girl from '../../../public/assets/hero_section/hero_girl.webp';
import watchHero from '../../../public/assets/hero_section/watch.webp';

import bag from '../../../public/assets/hero_section/bag.webp';
import coat from '../../../public/assets/hero_section/coat.jpg';
import shoes from '../../../public/assets/hero_section/shoes.webp';
import ProductCard from '@/components/ProductCard.vue'

const master = useMaster();
const authStore = useAuth();
const router = useRouter();
const toast = useToast();

const banners = ref([
    { id: 1, thumbnail: '/assets/app_screens/mobile_screen_1.jpeg' },
    { id: 2, thumbnail: '/assets/app_screens/mobile_screen_2.jpeg' }
]);
const modules = [Autoplay, Pagination];

/** ~3 rows on lg (5 cols × 3) */
const PRODUCTS_PER_PAGE = 15;
const initialSkeletonCount = PRODUCTS_PER_PAGE;
const productPlaceholder = '/default/default.jpg';

const openPlayStore = () => {
    if (master.playStoreLink) {
        window.open(master.playStoreLink, '_blank');
    }
}

const openAppStore = () => {
    if (master.appStoreLink) {
        window.open(master.appStoreLink, '_blank');
    }
}

const categories = ref([]);
const categoryId = ref(null);
const searchInput = ref('');
const searchApplied = ref('');
const products = ref([]);
const loading = ref(false);
const loadError = ref('');
const done = ref(false);
const page = ref(1);
const total = ref(0);
const loadTrigger = ref(null);
let io;
let searchTimer;
let searchGeneration = 0;

const productImage = (product) => product.thumbnail || productPlaceholder;

const onProductImageError = (event, product) => {
    if (event.target.src !== productPlaceholder) {
        event.target.src = productPlaceholder;
        product.thumbnail = productPlaceholder;
    }
};

const goToLogin = () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
    authStore.showLoginModal();
};

const onHeartClick = (product) => {
    if (!authStore.token) {
        goToLogin();
        return;
    }
    axios
        .post(
            '/favorite-add-or-remove',
            { product_id: product.id },
            { headers: { Authorization: authStore.token } },
        )
        .then((response) => {
            const p = response.data?.data?.product;
            if (p) {
                product.is_favorite = Boolean(p.is_favorite);
                product.favorites_count = p.favorites_count ?? product.favorites_count ?? 0;
            } else {
                product.is_favorite = !product.is_favorite;
                product.favorites_count = Math.max(
                    0,
                    (product.favorites_count ?? 0) + (product.is_favorite ? -1 : 1),
                );
            }
            toast.success(product.is_favorite ? 'Saved to wishlist' : 'Removed from wishlist');
            authStore.fetchFavoriteProducts();
        })
        .catch((err) => {
            const status = err?.response?.status;
            if (status === 401 || status === 403) {
                authStore.token = null;
                authStore.user = null;
                goToLogin();
                return;
            }
            toast.error(err?.response?.data?.message || 'Could not update wishlist');
        });
};

const runSearchNow = () => {
    clearTimeout(searchTimer);
    searchApplied.value = searchInput.value.trim();
    resetAndLoad();
};

const debouncedSearch = () => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
        searchApplied.value = searchInput.value.trim();
        resetAndLoad();
    }, 400);
};

const setCategory = (id) => {
    categoryId.value = id;
    resetAndLoad();
};

const resetAndLoad = async () => {
    searchGeneration += 1;
    products.value = [];
    page.value = 1;
    done.value = false;
    loadError.value = '';
    loading.value = false;
    await loadNext();
};

const loadNext = async () => {
    if (loading.value || done.value) return;
    const gen = searchGeneration;
    loading.value = true;
    try {
        const params = { page: page.value, per_page: PRODUCTS_PER_PAGE, sort_type: 'just_for_you' };
        if (searchApplied.value) params.search = searchApplied.value;
        if (categoryId.value) params.category_id = categoryId.value;
        const req = {
            params,
            headers: { 'Accept-Language': master.locale || 'en' },
        };
        if (authStore.token) {
            req.headers.Authorization = authStore.token;
        }
        const { data } = await axios.get('/products', req);
        if (gen !== searchGeneration) return;
        const list = (data.data?.products ?? []).map((p) => ({
            ...p,
            is_favorite: Boolean(p.is_favorite),
        }));
        total.value = data.data?.total ?? 0;
        products.value = page.value === 1 ? list : products.value.concat(list);
        page.value += 1;
        if (!list.length || products.value.length >= total.value) done.value = true;
    } catch (err) {
        if (gen === searchGeneration) {
            done.value = true;
            loadError.value =
                err?.response?.status === 500
                    ? 'Could not load products. Check server logs or enable PHP fileinfo extension.'
                    : 'Could not load products. Please try again.';
            console.error('Products API error:', err?.response?.data ?? err);
        }
    } finally {
        if (gen === searchGeneration) {
            loading.value = false;
            nextTick(() => {
                const el = loadTrigger.value;
                if (io && el) {
                    io.unobserve(el);
                    io.observe(el);
                }
            });
        }
    }
};

const onIntersect = async (entries) => {
    const e = entries[0];
    if (!e?.isIntersecting || loading.value || done.value) return;
    const t = e.target;
    io?.unobserve(t);
    await loadNext();
    await nextTick();
    if (!done.value && loadTrigger.value) io?.observe(loadTrigger.value);
};

watch(
    () => authStore.token,
    () => {
        void resetAndLoad();
    },
);

onMounted(async () => {
    io = new IntersectionObserver(onIntersect, { root: null, rootMargin: '160px', threshold: 0 });
    await nextTick();
    if (loadTrigger.value) io.observe(loadTrigger.value);
    await loadNext();
    try {
        const { data } = await axios.get('/categories', {
            params: { page: 1, per_page: 100 },
            headers: { 'Accept-Language': master.locale || 'en' },
        });
        categories.value = data.data?.categories ?? [];
    } catch {
        if (master.categories?.length) {
            categories.value = master.categories;
        }
    }
});

onUnmounted(() => {
    clearTimeout(searchTimer);
    io?.disconnect();
});

const showProductDetails = (product) => {
    router.push({ name: 'productDetails', params: { id: product.id } });
}
</script>
