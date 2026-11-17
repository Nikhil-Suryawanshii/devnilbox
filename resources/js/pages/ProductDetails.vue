<template>
    <PublicLayout ref="publicLayoutRef">
        <div class="bg-slate-50 min-h-screen pt-28 pb-28 md:pb-20 selection:bg-orange-500 selection:text-white">
            <div v-if="!isLoading" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <nav class="text-xs md:text-sm font-medium text-slate-500 mb-8 flex items-center gap-2">
                    <router-link to="/" class="hover:text-orange-500 transition-colors">{{ $t("Home") }}</router-link>
                    <span class="text-slate-300">/</span>
                    <span class="text-slate-800 line-clamp-1">{{ product.name }}</span>
                </nav>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">

                    <!-- Step 02: Gallery -->
                    <div class="lg:col-span-7 xl:col-span-8">
                        <div ref="galleryBoxRef" class="group relative product-main-image-box w-full h-[420px] sm:h-[520px] md:h-[650px] lg:h-[660px] max-h-[680px] rounded-3xl overflow-hidden bg-slate-50 p-4 sm:p-5 md:p-6 flex items-center justify-center">

                            <!-- ── Custom 3D-transition image viewer ── -->
                            <div
                                v-if="galleryThumbs.length"
                                class="pv-stage"
                                aria-live="polite"
                            >
                                <!-- Outgoing layer (animates down + fade out) -->
                                <div
                                    v-if="transitionActive && outgoingThumb"
                                    class="pv-layer pv-layer--out"
                                    :key="'out-' + (outgoingThumb?.id ?? outgoingKey) + '-' + outgoingKey"
                                >
                                    <!-- Outgoing: regular image -->
                                    <img
                                        v-if="outgoingThumb.thumbnail"
                                        :src="outgoingThumb.thumbnail"
                                        class="product-main-image"
                                        :alt="product.name"
                                        draggable="false"
                                    />
                                    <!-- Outgoing: video thumb placeholder -->
                                    <div
                                        v-else-if="isVideoThumb(outgoingThumb)"
                                        class="relative h-full w-full overflow-hidden bg-slate-950"
                                    >
                                        <img
                                            v-if="videoPoster"
                                            :src="videoPoster"
                                            class="absolute inset-0 w-full h-full object-cover opacity-50"
                                            alt=""
                                        />
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-black/25" />
                                        <div class="relative z-10 h-full flex items-center justify-center">
                                            <span class="w-16 h-16 rounded-full bg-orange-500 text-white flex items-center justify-center">
                                                <svg class="w-7 h-7 ml-1" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5.14v13.72L19 12 8 5.14z" /></svg>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Incoming / static layer -->
                                <div
                                    class="pv-layer"
                                    :class="transitionActive ? 'pv-layer--in' : 'pv-layer--static'"
                                    :key="'in-' + (currentThumb?.id ?? incomingKey) + '-' + incomingKey"
                                >
                                    <!-- Current: regular image -->
                                    <img
                                        v-if="currentThumb && currentThumb.thumbnail"
                                        :src="currentThumb.thumbnail"
                                        class="product-main-image cursor-pointer"
                                        :alt="product.name"
                                        draggable="false"
                                        @click="stopAutoplay(); showGallery = true"
                                    />
                                    <!-- Current: video thumb -->
                                    <div
                                        v-else-if="currentThumb && isVideoThumb(currentThumb)"
                                        class="relative h-full w-full overflow-hidden bg-slate-950"
                                        @click.stop
                                    >
                                        <template v-if="!mainVideoPlaying">
                                            <img
                                                v-if="videoPoster"
                                                :src="videoPoster"
                                                class="absolute inset-0 w-full h-full object-cover opacity-50"
                                                alt=""
                                            />
                                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-black/25" />
                                            <button
                                                type="button"
                                                class="absolute inset-0 z-10 flex flex-col items-center justify-center gap-3 group"
                                                @click="playMainVideo"
                                            >
                                                <span class="w-16 h-16 md:w-20 md:h-20 rounded-full bg-orange-500 text-white shadow-xl shadow-orange-500/40 ring-4 ring-white/25 flex items-center justify-center transition-transform group-hover:scale-105">
                                                    <svg class="w-7 h-7 md:w-8 md:h-8 ml-1" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                                        <path d="M8 5.14v13.72L19 12 8 5.14z" />
                                                    </svg>
                                                </span>
                                                <span class="text-sm font-bold uppercase tracking-[0.2em] text-white">
                                                    {{ $t('Video') }}
                                                </span>
                                            </button>
                                        </template>
                                        <video
                                            v-else
                                            ref="mainVideoEl"
                                            class="relative z-10 w-full h-full object-contain bg-black"
                                            controls
                                            playsinline
                                            autoplay
                                            :src="currentThumb.url"
                                            :poster="videoPoster || undefined"
                                        />
                                    </div>
                                    <!-- Current: embedded html -->
                                    <div
                                        v-else-if="currentThumb && currentThumb.url"
                                        class="w-full h-full overflow-hidden"
                                        v-html="currentThumb.url"
                                    ></div>
                                </div>

                                <!-- Prev arrow -->
                                <button
                                    v-if="galleryThumbs.length > 1"
                                    type="button"
                                    class="pv-nav pv-nav--prev"
                                    :disabled="transitionActive"
                                    @click="navigatePrev"
                                    :aria-label="$t('Previous')"
                                >
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                                    </svg>
                                </button>

                                <!-- Next arrow -->
                                <button
                                    v-if="galleryThumbs.length > 1"
                                    type="button"
                                    class="pv-nav pv-nav--next"
                                    :disabled="transitionActive"
                                    @click="navigateNext"
                                    :aria-label="$t('Next')"
                                >
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            </div>
                            <!-- ── / custom viewer ── -->

                            <span
                                v-if="displayDiscount > 0"
                                class="discount-badge"
                            >
                                {{ displayDiscount }}% {{ $t('OFF') }}
                            </span>

                            <div class="absolute top-4 right-4 z-10 flex flex-col gap-2">
                                <button
                                    type="button"
                                    @click="favoriteAddOrRemove"
                                    class="w-10 h-10 rounded-full bg-white shadow flex items-center justify-center hover:bg-orange-50"
                                    :title="$t('Wishlist')"
                                >
                                    <HeartIconFill v-if="product.is_favorite" class="w-5 h-5 text-red-500" />
                                    <HeartIcon v-else class="w-5 h-5 text-slate-600" />
                                </button>
                                <Menu as="div" class="relative">
                                    <MenuButton
                                        class="w-10 h-10 rounded-full bg-white shadow flex items-center justify-center hover:bg-orange-50"
                                        :title="$t('Share')"
                                    >
                                        <ShareIcon class="w-5 h-5 text-slate-600" />
                                    </MenuButton>
                                    <MenuItems class="absolute right-0 mt-2 w-44 bg-white rounded-xl shadow-lg border border-slate-100 py-1 z-20">
                                        <MenuItem
                                            v-for="social in shareOptions"
                                            :key="social.name"
                                            v-slot="{ active }"
                                        >
                                            <button
                                                type="button"
                                                class="w-full flex items-center gap-2 px-3 py-2 text-sm text-slate-700"
                                                :class="active ? 'bg-slate-50' : ''"
                                                @click="share(social.name)"
                                            >
                                                <FontAwesomeIcon :icon="social.icon" :style="{ color: social.color }" />
                                                <span class="capitalize">{{ social.name }}</span>
                                            </button>
                                        </MenuItem>
                                    </MenuItems>
                                </Menu>
                            </div>

                            <span
                                v-if="galleryThumbs.length"
                                class="absolute bottom-4 left-4 z-10 bg-slate-900/70 text-white text-xs font-semibold px-2.5 py-1 rounded-full"
                            >
                                {{ selectedThumbIndex + 1 }}/{{ galleryThumbs.length }}
                            </span>

                            <button
                                v-if="hasVideoThumb"
                                type="button"
                                @click="openVideoSlide"
                                class="absolute bottom-14 right-4 bg-orange-500 hover:bg-orange-600 text-white px-4 py-2.5 rounded-full text-xs font-semibold shadow-sm flex items-center gap-1.5 z-10"
                            >
                                ▶ {{ $t('Watch Video') }}
                            </button>

                            <button
                                v-if="galleryThumbs.length > 1"
                                type="button"
                                @click="stopAutoplay(); showGallery = true"
                                class="absolute bottom-4 right-4 bg-orange-500 hover:bg-orange-600 text-white px-4 py-2.5 rounded-full text-xs font-semibold shadow-sm flex items-center gap-1.5 z-10"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/>
                                </svg>
                                {{ $t('View Gallery') }} ({{ galleryThumbs.length }})
                            </button>
                        </div>

                        <div
                            v-if="galleryThumbs.length > 1"
                            ref="thumbStripRef"
                            class="flex items-center gap-3 overflow-x-auto pb-2 mt-3 scroll-smooth"
                        >
                            <button
                                v-for="(thumb, index) in galleryThumbs"
                                :key="`strip-${thumb.id || index}`"
                                :ref="(el) => { if (el) thumbButtonRefs[index] = el }"
                                type="button"
                                @click="stopAutoplay(); selectThumb(index, { syncColor: true })"
                                class="w-20 h-20 flex-shrink-0 border-2 rounded-2xl overflow-hidden"
                                :class="[
                                    selectedThumbIndex === index
                                        ? 'border-orange-500 ring-2 ring-orange-500/15'
                                        : 'border-slate-200 hover:border-slate-400',
                                    isVideoThumb(thumb) ? 'bg-slate-900 p-0' : 'bg-white p-1',
                                ]"
                            >
                                <img
                                    v-if="thumb.thumbnail"
                                    :src="thumb.thumbnail"
                                    class="w-full h-full object-contain rounded-xl"
                                    :alt="product.name"
                                />
                                <div
                                    v-else
                                    class="relative w-full h-full overflow-hidden bg-slate-900"
                                >
                                    <img
                                        v-if="videoPoster"
                                        :src="videoPoster"
                                        class="absolute inset-0 w-full h-full object-cover opacity-50"
                                        alt=""
                                    />
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-black/20" />
                                    <div class="relative z-10 h-full flex flex-col items-center justify-center gap-1">
                                        <span
                                            class="w-8 h-8 rounded-full bg-orange-500 text-white shadow-lg shadow-orange-500/40 ring-2 ring-white/30 flex items-center justify-center"
                                        >
                                            <svg class="w-3.5 h-3.5 ml-0.5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                                <path d="M8 5.14v13.72L19 12 8 5.14z" />
                                            </svg>
                                        </span>
                                        <span class="text-[9px] font-bold uppercase tracking-wider text-white">
                                            {{ $t('Video') }}
                                        </span>
                                    </div>
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- Step 03: Buy box -->
                    <div class="lg:col-span-5 xl:col-span-4 space-y-6 lg:sticky lg:top-28">
                        <div class="bg-white rounded-3xl p-5 flex items-center justify-between border border-slate-100 shadow-sm">
                            <div class="flex items-center gap-4 min-w-0">
                                <img
                                    :src="product.shop?.logo"
                                    class="w-12 h-12 rounded-full object-cover ring-2 ring-slate-100 shrink-0"
                                    :alt="product.shop?.name"
                                />
                                <div class="min-w-0">
                                    <router-link :to="`/shops/${product.shop?.id}`" class="font-bold text-slate-900 hover:text-orange-500 truncate block">
                                        {{ product.shop?.name }}
                                    </router-link>
                                    <div class="text-xs text-slate-500 flex items-center gap-1 mt-0.5">
                                        <span class="text-amber-500">★</span>
                                        {{ Number(product.shop?.rating || 0).toFixed(1) }}
                                        <span class="text-slate-300">|</span>
                                        {{ $t('Verified Seller') }}
                                    </div>
                                </div>
                            </div>
                            <button
                                type="button"
                                @click="showMessages"
                                class="w-12 h-12 shrink-0 rounded-2xl bg-slate-900 hover:bg-orange-500 text-white flex items-center justify-center"
                                :title="$t('Contact Seller')"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                            </button>
                        </div>

                        <div class="bg-white rounded-[32px] p-6 md:p-8 border border-slate-100 shadow-sm space-y-5">
                            <div v-if="flashSale" class="rounded-2xl bg-orange-50 border border-orange-100 px-4 py-3 flex flex-wrap items-center gap-2 text-sm">
                                <span class="font-bold text-orange-700">{{ $t('Flash Sale') }}</span>
                                <span class="text-orange-600 tabular-nums">{{ endDay }}d {{ endHour }}h {{ endMinute }}m {{ endSecond }}s</span>
                            </div>

                            <div>
                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-800 uppercase mb-3">
                                    {{ product.brand || $t('Unknown Brand') }}
                                </span>
                                <div class="flex items-start justify-between gap-3">
                                    <h1 class="text-2xl md:text-3xl font-black text-slate-900 tracking-tight leading-tight">
                                        {{ product.name }}
                                    </h1>
                                    <span
                                        class="shrink-0 px-2.5 py-1 rounded-full text-xs font-bold"
                                        :class="product.quantity > 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500'"
                                    >
                                        {{ product.quantity > 0 ? $t('In Stock') : $t('Out of Stock') }}
                                    </span>
                                </div>
                                <p v-if="tagline" class="text-sm text-slate-500 mt-2">{{ tagline }}</p>
                            </div>

                            <div class="flex flex-wrap items-center gap-3 text-sm">
                                <div class="flex items-center gap-1">
                                    <StarIcon
                                        v-for="i in 5"
                                        :key="i"
                                        class="w-4 h-4"
                                        :class="i <= Math.round(product.rating || 0) ? 'text-amber-500' : 'text-slate-300'"
                                    />
                                    <span class="font-semibold ml-1">{{ Number(product.rating || 0).toFixed(1) }}</span>
                                    <span class="text-slate-400">({{ product.total_reviews || 0 }} {{ $t('reviews') }})</span>
                                </div>
                                <button type="button" class="text-orange-600 font-semibold hover:underline" @click="setTab('reviews')">
                                    {{ $t('View Reviews') }} →
                                </button>
                            </div>

                            <div class="flex items-baseline gap-3 flex-wrap">
                                <span class="text-4xl font-extrabold text-orange-600">
                                    {{ masterStore.showCurrency(productPrice) }}
                                </span>
                                <span
                                    v-if="product.discount_price > 0"
                                    class="text-lg text-slate-400 line-through"
                                >
                                    {{ masterStore.showCurrency(mainPrice) }}
                                </span>
                                <span v-if="displayDiscount > 0" class="text-sm font-bold text-red-500">
                                    {{ displayDiscount }}% {{ $t('OFF') }}
                                </span>
                            </div>

                            <div
                                v-if="product.quantity > 0 && product.quantity <= 20"
                                class="flex items-center gap-2 rounded-2xl bg-orange-50 border border-orange-100 text-orange-800 px-4 py-3 text-sm font-medium"
                            >
                                🔥 {{ $t('Almost sold out!') }} ({{ product.quantity }} {{ $t('left in stock') }})
                            </div>

                            <div
                                v-if="product.colors?.length || product.sizes?.length"
                                class="space-y-5 border-t border-b border-slate-100 py-6"
                            >
                                <div v-if="product.colors?.length">
                                    <label class="text-xs uppercase tracking-wider font-bold text-slate-800 block mb-3">
                                        {{ $t('Color') }}<span v-if="selectedColorName">: {{ selectedColorName }}</span>
                                    </label>
                                    <div class="flex flex-wrap gap-3">
                                        <button
                                            v-for="(color, index) in product.colors"
                                            :key="color.id"
                                            type="button"
                                            @click="selectColor(color, index)"
                                            class="w-8 h-8 rounded-full border border-slate-200"
                                            :class="Number(formData.color) === Number(color.id)
                                                ? 'ring-2 ring-orange-500 ring-offset-2'
                                                : ''"
                                            :style="{ backgroundColor: color.color_code || color.name }"
                                            :title="color.name"
                                            :aria-label="color.name"
                                            :aria-pressed="Number(formData.color) === Number(color.id)"
                                        />
                                    </div>
                                </div>
                                <div v-if="product.sizes?.length">
                                    <label class="text-xs uppercase tracking-wider font-bold text-slate-800 block mb-3">
                                        {{ $t('Select Size') }}
                                    </label>
                                    <div class="flex flex-wrap gap-2">
                                        <button
                                            v-for="size in product.sizes"
                                            :key="size.id"
                                            type="button"
                                            @click="formData.size = size.id"
                                            :class="Number(formData.size) === Number(size.id)
                                                ? 'bg-orange-500 text-white border-orange-500'
                                                : 'bg-white text-slate-800 border-slate-200'"
                                            class="px-4 py-2.5 text-xs font-bold border rounded-xl min-w-[50px]"
                                        >
                                            {{ size.name }}
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Trust row: API when set, else app static fallbacks -->
                            <div class="grid grid-cols-2 gap-3 text-center text-xs">
                                <div class="rounded-xl bg-slate-50 p-3">
                                    <div class="font-bold text-slate-800">{{ $t('Free Delivery') }}</div>
                                    <div class="text-slate-500 mt-0.5">
                                        {{ product.shop?.estimated_delivery_time || '3-5 days' }}
                                    </div>
                                </div>
                                <div class="rounded-xl bg-slate-50 p-3">
                                    <div class="font-bold text-slate-800">
                                        {{ product.warranty_label || $t('1 Year Warranty') }}
                                    </div>
                                    <div class="text-slate-500 mt-0.5">
                                        {{ product.warranty_note || $t('Official Brand') }}
                                    </div>
                                </div>
                                <div class="rounded-xl bg-slate-50 p-3">
                                    <div class="font-bold text-slate-800">
                                        <template v-if="product.return_days">
                                            {{ product.return_days }} {{ $t('Days Return') }}
                                        </template>
                                        <template v-else>{{ $t('7 Days Return') }}</template>
                                    </div>
                                    <div class="text-slate-500 mt-0.5">
                                        {{ product.return_note || $t('Easy Returns') }}
                                    </div>
                                </div>
                                <div class="rounded-xl bg-slate-50 p-3">
                                    <div class="font-bold text-slate-800">{{ $t('24/7 Support') }}</div>
                                    <div class="text-slate-500 mt-0.5">
                                        {{ product.shop?.support_note || $t("We're here") }}
                                    </div>
                                </div>
                            </div>

                            <div class="hidden md:block pt-1">
                                <button
                                    v-if="product?.quantity > 0"
                                    type="button"
                                    @click="buyNow"
                                    class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-4 rounded-2xl text-base"
                                >
                                    {{ $t('Buy Now') }}
                                </button>
                                <button
                                    v-else
                                    type="button"
                                    disabled
                                    class="w-full bg-slate-100 text-slate-400 font-bold py-4 rounded-2xl cursor-not-allowed"
                                >
                                    {{ $t('Out of Stock') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 04: Tabs -->
                <div class="mt-12 bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="flex overflow-x-auto border-b border-slate-100">
                        <button
                            v-for="tab in tabs"
                            :key="tab.id"
                            type="button"
                            class="px-5 py-3.5 text-sm font-semibold whitespace-nowrap border-b-2 transition"
                            :class="activeTab === tab.id ? 'border-orange-500 text-orange-600' : 'border-transparent text-slate-500'"
                            @click="setTab(tab.id)"
                        >
                            {{ $t(tab.label) }}
                        </button>
                    </div>

                    <div class="p-6 md:p-8 space-y-6">
                        <div v-if="activeTab === 'overview'" class="space-y-8">
                            <p v-if="product.short_description" class="text-slate-700 leading-relaxed">
                                {{ product.short_description }}
                            </p>

                            <div v-if="keyFeatureBullets.length">
                                <h3 class="text-base font-bold text-slate-900 mb-3">{{ $t('Key Features') }}</h3>
                                <div class="flex flex-wrap gap-2">
                                    <span
                                        v-for="(feat, i) in keyFeatureBullets"
                                        :key="i"
                                        class="inline-flex items-center rounded-xl bg-slate-50 border border-slate-100 px-3 py-2 text-sm text-slate-700"
                                    >
                                        {{ feat }}
                                    </span>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                                <div class="rounded-2xl bg-slate-50 p-4">
                                    <div class="font-bold text-slate-900">{{ product.warranty_label || $t('1 Year Warranty') }}</div>
                                    <div class="text-slate-500 mt-1">{{ product.warranty_note || $t('Official Brand') }}</div>
                                </div>
                                <div class="rounded-2xl bg-slate-50 p-4">
                                    <div class="font-bold text-slate-900">
                                        <template v-if="product.return_days">{{ product.return_days }} {{ $t('Days Return') }}</template>
                                        <template v-else>{{ $t('7 Days Return') }}</template>
                                    </div>
                                    <div class="text-slate-500 mt-1">{{ product.return_note || $t('Easy Returns') }}</div>
                                </div>
                                <div class="rounded-2xl bg-slate-50 p-4">
                                    <div class="font-bold text-slate-900">{{ $t('Free Delivery') }}</div>
                                    <div class="text-slate-500 mt-1">{{ product.shop?.estimated_delivery_time || '3-5 days' }}</div>
                                </div>
                                <div class="rounded-2xl bg-slate-50 p-4">
                                    <div class="font-bold text-slate-900">{{ $t('24/7 Support') }}</div>
                                    <div class="text-slate-500 mt-1">{{ product.shop?.support_note || $t("We're here") }}</div>
                                </div>
                            </div>

                            <div v-if="product.box_items?.length">
                                <h3 class="text-base font-bold text-slate-900 mb-3">{{ $t("What's in the Box") }}</h3>
                                <ul class="list-disc pl-5 space-y-1 text-sm text-slate-700">
                                    <li v-for="item in product.box_items" :key="item.id">{{ item.item_name }}</li>
                                </ul>
                            </div>

                            <div v-if="hasSpecs">
                                <h3 class="text-base font-bold text-slate-900 mb-3">{{ $t('Specifications') }}</h3>
                                <dl class="space-y-2 text-sm">
                                    <div v-if="product.brand" class="flex justify-between gap-4 border-b border-slate-100 py-2">
                                        <dt class="text-slate-500">{{ $t('Brand') }}</dt>
                                        <dd class="font-medium">{{ product.brand }}</dd>
                                    </div>
                                    <div v-if="product.colors?.length" class="flex justify-between gap-4 border-b border-slate-100 py-2">
                                        <dt class="text-slate-500">{{ $t('Colors') }}</dt>
                                        <dd class="font-medium text-right">{{ product.colors.map((c) => c.name).join(', ') }}</dd>
                                    </div>
                                    <div v-if="product.sizes?.length" class="flex justify-between gap-4 border-b border-slate-100 py-2">
                                        <dt class="text-slate-500">{{ $t('Sizes') }}</dt>
                                        <dd class="font-medium text-right">{{ product.sizes.map((s) => s.name).join(', ') }}</dd>
                                    </div>
                                    <div
                                        v-for="spec in product.specifications || []"
                                        :key="'ov-' + spec.id"
                                        class="flex justify-between gap-4 border-b border-slate-100 py-2"
                                    >
                                        <dt class="text-slate-500">{{ spec.label }}</dt>
                                        <dd class="font-medium text-right">{{ spec.value }}</dd>
                                    </div>
                                </dl>
                            </div>

                            <div v-if="product.faqs?.length">
                                <h3 class="text-base font-bold text-slate-900 mb-3">{{ $t('Q&A') }}</h3>
                                <div class="space-y-3">
                                    <div
                                        v-for="faq in product.faqs"
                                        :key="'ov-faq-' + faq.id"
                                        class="rounded-xl border border-slate-100 p-3"
                                    >
                                        <div class="font-semibold text-sm text-slate-900">{{ faq.question }}</div>
                                        <div class="text-sm text-slate-600 mt-1">{{ faq.answer }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-else-if="activeTab === 'details'" class="space-y-4">
                            <details open class="group border-b border-slate-100 pb-4">
                                <summary class="font-bold text-slate-900 cursor-pointer list-none flex justify-between">
                                    {{ $t('Product Description') }}
                                    <span class="text-slate-400">▾</span>
                                </summary>
                                <div v-if="product.description" class="prose max-w-none mt-3" v-html="product.description"></div>
                                <p v-else class="text-slate-500 text-sm mt-3">{{ $t('No description available') }}</p>
                            </details>
                            <details open class="group border-b border-slate-100 pb-4">
                                <summary class="font-bold text-slate-900 cursor-pointer list-none flex justify-between">
                                    {{ $t("What's in the Box") }}
                                    <span class="text-slate-400">▾</span>
                                </summary>
                                <ul v-if="product.box_items?.length" class="mt-3 space-y-2 text-sm text-slate-700 list-disc pl-5">
                                    <li v-for="item in product.box_items" :key="item.id">{{ item.item_name }}</li>
                                </ul>
                                <p v-else class="text-slate-500 text-sm mt-3">{{ $t('Details not available') }}</p>
                            </details>
                            <details open class="group pb-2">
                                <summary class="font-bold text-slate-900 cursor-pointer list-none flex justify-between">
                                    {{ $t('Specifications') }}
                                    <span class="text-slate-400">▾</span>
                                </summary>
                                <dl v-if="hasSpecs" class="mt-3 space-y-2 text-sm">
                                    <div v-if="product.brand" class="flex justify-between gap-4 border-b border-slate-50 py-2">
                                        <dt class="text-slate-500">{{ $t('Brand') }}:</dt>
                                        <dd class="font-medium text-slate-900">{{ product.brand }}</dd>
                                    </div>
                                    <div v-if="product.colors?.length" class="flex justify-between gap-4 border-b border-slate-50 py-2">
                                        <dt class="text-slate-500">{{ $t('Colors') }}:</dt>
                                        <dd class="font-medium text-slate-900 text-right">
                                            {{ product.colors.map((c) => c.name).join(', ') }}
                                        </dd>
                                    </div>
                                    <div v-if="product.sizes?.length" class="flex justify-between gap-4 border-b border-slate-50 py-2">
                                        <dt class="text-slate-500">{{ $t('Sizes') }}:</dt>
                                        <dd class="font-medium text-slate-900 text-right">
                                            {{ product.sizes.map((s) => s.name).join(', ') }}
                                        </dd>
                                    </div>
                                    <div
                                        v-for="spec in product.specifications || []"
                                        :key="spec.id"
                                        class="flex justify-between gap-4 border-b border-slate-50 py-2"
                                    >
                                        <dt class="text-slate-500">{{ spec.label }}:</dt>
                                        <dd class="font-medium text-slate-900 text-right">{{ spec.value }}</dd>
                                    </div>
                                </dl>
                                <p v-else class="text-slate-500 text-sm mt-3">{{ $t('Details not available') }}</p>
                            </details>
                        </div>

                        <div v-else-if="activeTab === 'reviews'" class="space-y-4">
                            <div v-if="reviews.length">
                                <Review v-for="item in reviews" :key="item.id" :review="item" />
                            </div>
                            <p v-else class="text-slate-500 text-sm">{{ $t('No reviews yet') }}</p>
                            <div v-if="totalReviews > perPage" class="flex justify-center gap-2 pt-2">
                                <button
                                    type="button"
                                    class="px-3 py-1.5 text-sm rounded-lg border disabled:opacity-40"
                                    :disabled="currentPage <= 1"
                                    @click="onClickHandler(currentPage - 1)"
                                >
                                    {{ $t('Prev') }}
                                </button>
                                <span class="text-sm text-slate-500 self-center">{{ currentPage }}</span>
                                <button
                                    type="button"
                                    class="px-3 py-1.5 text-sm rounded-lg border disabled:opacity-40"
                                    :disabled="currentPage * perPage >= totalReviews"
                                    @click="onClickHandler(currentPage + 1)"
                                >
                                    {{ $t('Next') }}
                                </button>
                            </div>
                        </div>

                        <div v-else-if="activeTab === 'qa'" class="space-y-4">
                            <div v-if="product.faqs?.length" class="space-y-4">
                                <div
                                    v-for="faq in product.faqs"
                                    :key="faq.id"
                                    class="rounded-2xl border border-slate-100 p-4"
                                >
                                    <div class="font-bold text-slate-900 text-sm">{{ faq.question }}</div>
                                    <div class="text-slate-600 text-sm mt-2">{{ faq.answer }}</div>
                                </div>
                            </div>
                            <p v-else class="text-slate-500 text-sm">{{ $t('No questions yet') }}</p>
                            <button
                                type="button"
                                @click="showMessages"
                                class="inline-flex px-5 py-3 rounded-2xl bg-orange-500 hover:bg-orange-600 text-white font-bold text-sm"
                            >
                                {{ $t('Contact Seller') }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Step 05: Related -->
                <div v-if="relatedProducts.length" class="mt-16 border-t border-slate-200/60 pt-12">
                    <div class="flex items-center justify-between gap-4 mb-8">
                        <h2 class="text-xl md:text-2xl font-black text-slate-900">{{ $t('Related Products') }}</h2>
                        <router-link
                            :to="product.shop?.id ? `/shops/${product.shop.id}` : '/products'"
                            class="text-orange-600 text-sm font-bold hover:underline"
                        >
                            {{ $t('View All') }} →
                        </router-link>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 xl:grid-cols-5 gap-4 lg:gap-6">
                        <ProductCard v-for="item in relatedProducts" :key="item.id" :product="item" />
                    </div>
                </div>
            </div>

            <div v-else class="flex flex-col justify-center items-center min-h-[400px]">
                <div class="w-20 h-20 rounded-full border-4 border-transparent border-t-orange-500 animate-spin"></div>
                <div class="mt-6 text-gray-600 animate-pulse">{{ $t('Loading Product...') }}</div>
            </div>
        </div>

        <!-- Sticky Buy Now only -->
        <div
            v-if="!isLoading && product?.quantity > 0"
            class="md:hidden fixed bottom-16 left-0 right-0 z-40 bg-white border-t border-slate-200 px-4 py-3 flex items-center gap-3 shadow-[0_-4px_12px_rgba(0,0,0,0.06)]"
        >
            <div class="text-lg font-extrabold text-orange-600 tabular-nums shrink-0">
                {{ masterStore.showCurrency(productPrice) }}
            </div>
            <button
                type="button"
                @click="buyNow"
                class="flex-1 bg-orange-500 text-white font-bold py-2.5 rounded-xl text-sm"
            >
                {{ $t('Buy Now') }}
            </button>
        </div>

        <Teleport to="body">
            <Transition name="fade">
                <div
                    v-if="showGallery"
                    class="fixed inset-0 bg-slate-950/80 backdrop-blur-md z-50 flex items-center justify-center p-4"
                >
                    <div class="bg-white rounded-3xl w-full max-w-5xl relative p-4 md:p-6 shadow-2xl">
                        <button
                            type="button"
                            @click="showGallery = false"
                            class="absolute top-4 right-4 w-10 h-10 rounded-full bg-slate-100 z-10"
                        >
                            ✕
                        </button>
                        <swiper
                            :spaceBetween="16"
                            :navigation="true"
                            :modules="[Navigation]"
                            :initial-slide="selectedThumbIndex"
                            class="h-[65vh] md:h-[75vh]"
                        >
                            <swiper-slide v-for="(thumbnail, index) in galleryThumbs" :key="thumbnail.id || index">
                                <div class="h-full w-full flex items-center justify-center p-2">
                                    <img
                                        v-if="thumbnail.thumbnail"
                                        :src="thumbnail.thumbnail"
                                        class="max-h-full max-w-full object-contain"
                                        :alt="product.name"
                                    />
                                    <div
                                        v-else-if="isVideoThumb(thumbnail)"
                                        class="relative h-full w-full flex items-center justify-center rounded-2xl overflow-hidden bg-slate-950"
                                    >
                                        <img
                                            v-if="videoPoster"
                                            :src="videoPoster"
                                            class="absolute inset-0 w-full h-full object-cover opacity-40"
                                            alt=""
                                        />
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent" />
                                        <video
                                            controls
                                            playsinline
                                            class="relative z-10 max-h-full max-w-full rounded-xl"
                                            :src="thumbnail.url"
                                            :poster="videoPoster || undefined"
                                        />
                                    </div>
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
import { computed, nextTick, onMounted, onUnmounted, ref, watch } from "vue";
import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";
import { useRoute, useRouter } from "vue-router";
import { useMaster } from "../stores/MasterStore";
import { HeartIcon, ShareIcon } from "@heroicons/vue/24/outline";
import { HeartIcon as HeartIconFill, StarIcon } from "@heroicons/vue/24/solid";
import { Navigation } from "swiper/modules";
import { Swiper, SwiperSlide } from "swiper/vue";
import { useToast } from "vue-toastification";
import { useAuth } from "../stores/AuthStore";
import { useBasketStore } from "../stores/BasketStore";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import {
    faFacebookF,
    faLinkedin,
    faTwitter,
    faPinterest,
    faRedditAlien,
    faWhatsapp,
    faTelegram,
} from "@fortawesome/free-brands-svg-icons";
import { faEnvelope } from "@fortawesome/free-solid-svg-icons";
import { useShareLink } from "vue3-social-sharing";
import ToastSuccessMessage from "../components/ToastSuccessMessage.vue";
import ProductCard from "../components/ProductCard.vue";
import Review from "../components/Review.vue";
import PublicLayout from "../layouts/ProductPageLayout.vue";
import "swiper/css";
import "swiper/css/navigation";

// ─── Transition: accessibility check ───────────────────────────────────────
const prefersReducedMotion =
    typeof window !== 'undefined' &&
    window.matchMedia('(prefers-reduced-motion: reduce)').matches;

const { shareLink } = useShareLink();
const showGallery = ref(false);
const toast = useToast();
const route = useRoute();
const router = useRouter();
const masterStore = useMaster();
const basketStore = useBasketStore();
const authStore = useAuth();
const publicLayoutRef = ref(null);

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
const isLoading = ref(true);
const selectedThumbnail = ref(null);
const selectedThumbIndex = ref(0);
const mainSwiper = ref(null);
const syncingFromColor = ref(false);

// ─── DOM refs for scroll-into-view behaviour ───────────────────────────────
const galleryBoxRef = ref(null);    // the main image preview box
const thumbStripRef = ref(null);    // the horizontal thumbnail strip
const thumbButtonRefs = ref([]);    // individual thumbnail buttons (array)

/**
 * After selecting a new thumbnail:
 *  1. Scroll the active thumbnail into view within the horizontal strip.
 *  2. If the gallery preview box is above the current viewport (user scrolled
 *     down below the image), scroll the page back up so it's visible.
 */
const scrollActiveThumb = (index) => {
    nextTick(() => {
        // 1. Scroll the thumbnail strip so the active button is visible
        const btn = thumbButtonRefs.value[index];
        if (btn) {
            btn.scrollIntoView({
                behavior: 'smooth',
                block: 'nearest',
                inline: 'center',
            });
        }

        // 2. If the user manually clicked a thumbnail and the gallery box is out of view,
        //    bring it back into view so the transition is visible (skip during autoplay).
        if (!autoplayActive.value) {
            const box = galleryBoxRef.value;
            if (box) {
                const rect = box.getBoundingClientRect();
                const isAboveViewport = rect.bottom < 80; // 80px = rough navbar height
                if (isAboveViewport) {
                    box.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
        }
    });
};

// ─── 3-D image transition state ────────────────────────────────────────────
const transitionActive = ref(false);
const outgoingThumb = ref(null);
const outgoingKey = ref(0);   // bumped each transition so Vue re-keyes the layer
const incomingKey = ref(0);   // bumped each transition so Vue re-keyes the incoming layer
let transitionTimer = null;

const TRANSITION_DURATION = 650; // ms — matches CSS animation duration

const currentThumb = computed(() => galleryThumbs.value[selectedThumbIndex.value] ?? null);
const activeTab = ref("overview");
const flashSale = ref(null);
const averageRatings = ref({});
const totalReviews = ref(0);
const reviews = ref([]);
const currentPage = ref(1);
const perPage = ref(6);

const tabs = [
    { id: "overview", label: "Overview" },
    { id: "details", label: "Details" },
    { id: "reviews", label: "Reviews" },
    { id: "qa", label: "Q&A" },
];

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

const galleryThumbs = computed(() => product.value.thumbnails || []);
const imageThumbs = computed(() => galleryThumbs.value.filter((t) => !!t?.thumbnail));
const isVideoThumb = (thumb) => !!thumb && !thumb.thumbnail && (thumb.type === "file" || !!thumb.url);
const hasVideoThumb = computed(() => galleryThumbs.value.some((t) => isVideoThumb(t)));
const videoPoster = computed(() => imageThumbs.value[0]?.thumbnail || null);
const mainVideoPlaying = ref(false);
const mainVideoEl = ref(null);

const playMainVideo = async () => {
    stopAutoplay();
    mainVideoPlaying.value = true;
    await nextTick();
    try {
        await mainVideoEl.value?.play?.();
    } catch {
        // Autoplay may be blocked; controls remain available.
    }
};

const stopMainVideo = () => {
    if (mainVideoEl.value) {
        try {
            mainVideoEl.value.pause();
            mainVideoEl.value.currentTime = 0;
        } catch {
            // ignore
        }
    }
    mainVideoPlaying.value = false;
};

const displayDiscount = computed(() => {
    const fromApi = Number(product.value.discount_percentage || 0);
    const fromCalc = Number(discountPercentage.value || 0);
    return Math.max(fromApi, fromCalc);
});

const selectedColorName = computed(() => {
    const color = product.value.colors?.find((c) => Number(c.id) === Number(formData.value.color));
    return color?.name || "";
});

const tagline = computed(() => product.value.short_description || "");

const keyFeatureBullets = computed(() => {
    // Prefer dedicated API features when present
    const fromApi = (product.value.features || []).map((f) => f.title).filter(Boolean);
    if (fromApi.length) return fromApi;

    const fromShort = parseBullets(product.value.short_description);
    if (fromShort.length) return fromShort;
    const stripped = String(product.value.description || "").replace(/<[^>]*>/g, "\n");
    return parseBullets(stripped);
});

const hasSpecs = computed(
    () =>
        !!(
            product.value.brand ||
            product.value.colors?.length ||
            product.value.sizes?.length ||
            product.value.specifications?.length
        )
);

function parseBullets(text) {
    if (!text) return [];
    return String(text)
        .split(/[\n•|;]+/)
        .map((s) => s.replace(/^[-*]\s*/, "").trim())
        .filter((s) => s.length >= 3 && s.length <= 80)
        .slice(0, 8);
}

const showMessages = () => {
    if (authStore.token === null) return (authStore.loginModal = true);
    router.push("/massages");
};

const slideIndexForColor = (colorIndex) => {
    const images = imageThumbs.value;
    const all = galleryThumbs.value;
    const colors = product.value.colors || [];
    if (!all.length) return 0;

    // Prefer thumbnail with matching color_id
    const color = colors[colorIndex];
    if (color) {
        const byColor = all.findIndex((t) => Number(t.color_id) === Number(color.id));
        if (byColor >= 0) return byColor;
    }

    if (images.length) {
        const image = images[colorIndex % images.length];
        const idx = all.findIndex((t) => t.id === image.id && t.thumbnail === image.thumbnail);
        return idx >= 0 ? idx : colorIndex % all.length;
    }
    return colorIndex % all.length;
};

const colorIndexForSlide = (slideIndex) => {
    const colors = product.value.colors || [];
    const images = imageThumbs.value;
    const all = galleryThumbs.value;
    if (!colors.length || !all.length) return -1;
    const thumb = all[slideIndex];
    if (thumb?.thumbnail && images.length) {
        const imagePos = images.findIndex((t) => t.id === thumb.id && t.thumbnail === thumb.thumbnail);
        if (imagePos >= 0) return imagePos % colors.length;
    }
    return slideIndex % colors.length;
};

const selectThumb = (index, { syncColor = false } = {}) => {
    const thumbs = galleryThumbs.value;
    if (!thumbs.length) return;
    const safeIndex = Math.max(0, Math.min(index, thumbs.length - 1));
    if (safeIndex === selectedThumbIndex.value) return; // same slide — no-op

    stopMainVideo();

    // ── 3D transition logic ─────────────────────────────────────────────
    if (!prefersReducedMotion) {
        // Cancel any in-flight transition so rapid clicks don't break state
        if (transitionTimer !== null) {
            clearTimeout(transitionTimer);
            transitionTimer = null;
            transitionActive.value = false;
            outgoingThumb.value = null;
        }

        // Snapshot the currently visible thumb as the outgoing layer
        outgoingThumb.value = thumbs[selectedThumbIndex.value] ?? null;
        outgoingKey.value++;
        incomingKey.value++;
        transitionActive.value = true;

        // Advance the index — Vue will render the incoming slide immediately
        selectedThumbIndex.value = safeIndex;
        selectedThumbnail.value = thumbs[safeIndex]?.thumbnail || null;

        // After the CSS animation finishes, clean up the outgoing layer
        transitionTimer = setTimeout(() => {
            transitionActive.value = false;
            outgoingThumb.value = null;
            transitionTimer = null;
        }, TRANSITION_DURATION + 50); // slight buffer so the animation fully completes
    } else {
        // Reduced motion: instant swap
        selectedThumbIndex.value = safeIndex;
        selectedThumbnail.value = thumbs[safeIndex]?.thumbnail || null;
    }
    // ── /transition logic ───────────────────────────────────────────────

    if (syncColor && product.value.colors?.length) {
        const colorIdx = colorIndexForSlide(safeIndex);
        if (colorIdx >= 0 && product.value.colors[colorIdx]) {
            formData.value.color = product.value.colors[colorIdx].id;
        }
    }

    // Scroll the active thumbnail into view + bring gallery box back if needed
    scrollActiveThumb(safeIndex);
};

/** Navigate to previous gallery item — also stops autoplay */
const navigatePrev = () => {
    stopAutoplay();
    const len = galleryThumbs.value.length;
    if (!len) return;
    const prev = (selectedThumbIndex.value - 1 + len) % len;
    selectThumb(prev, { syncColor: true });
};

/** Navigate to next gallery item — also stops autoplay */
const navigateNext = () => {
    stopAutoplay();
    const len = galleryThumbs.value.length;
    if (!len) return;
    const next = (selectedThumbIndex.value + 1) % len;
    selectThumb(next, { syncColor: true });
};

// ─── Auto-slideshow ─────────────────────────────────────────────────────────
let autoplayInterval = null;
const autoplayActive = ref(false);
const AUTOPLAY_DELAY = 3500; // ms between slides

/** Advance one slide (internal — does NOT stop autoplay) */
const autoplayStep = () => {
    if (transitionActive.value) return;
    if (typeof document !== 'undefined' && document.hidden) return;
    const len = galleryThumbs.value.length;
    if (!len || len <= 1) { stopAutoplay(); return; }
    // Skip if currently on a video slide — pause there instead
    if (isVideoThumb(currentThumb.value)) { return; }
    const next = (selectedThumbIndex.value + 1) % len;
    selectThumb(next, { syncColor: false });
};

/** Start the auto-slideshow. Only runs if there are ≥2 image slides. */
const startAutoplay = () => {
    clearInterval(autoplayInterval);
    const len = galleryThumbs.value.length;
    if (!len || len <= 1) return;
    autoplayActive.value = true;
    autoplayInterval = setInterval(autoplayStep, AUTOPLAY_DELAY);
};

/** Stop the auto-slideshow permanently (user has taken control). */
const stopAutoplay = () => {
    if (autoplayInterval !== null) {
        clearInterval(autoplayInterval);
        autoplayInterval = null;
    }
    autoplayActive.value = false;
};

// These Swiper callbacks are kept for the lightbox gallery Swiper (not used by main viewer).
const onMainSwiper = (swiper) => {
    mainSwiper.value = swiper;
    if (selectedThumbIndex.value > 0) swiper.slideTo(selectedThumbIndex.value, 0);
};

const onMainSlideChange = (swiper) => {
    if (syncingFromColor.value) return;
    if (swiper.activeIndex !== selectedThumbIndex.value) stopMainVideo();
    selectedThumbIndex.value = swiper.activeIndex;
    selectedThumbnail.value = galleryThumbs.value[swiper.activeIndex]?.thumbnail || null;
    if (product.value.colors?.length) {
        const colorIdx = colorIndexForSlide(swiper.activeIndex);
        if (colorIdx >= 0 && product.value.colors[colorIdx]) {
            formData.value.color = product.value.colors[colorIdx].id;
        }
    }
};

const selectColor = (color, index) => {
    stopAutoplay();
    formData.value.color = color.id;
    if (!galleryThumbs.value.length) return;
    syncingFromColor.value = true;
    selectThumb(slideIndexForColor(index));
    nextTick(() => {
        syncingFromColor.value = false;
    });
};

const openVideoSlide = () => {
    stopAutoplay();
    const idx = galleryThumbs.value.findIndex((t) => isVideoThumb(t));
    if (idx >= 0) selectThumb(idx);
};

const setTab = (id) => {
    activeTab.value = id;
    if (id === "reviews") fetchReviews();
};

const share = (network) => {
    const description = (product.value.short_description || "").replace(/<[^>]*>/g, "");
    const thumbnail = product.value.thumbnails?.[0];
    shareLink({
        network,
        url: window.location.href,
        title: product.value.name,
        description,
        media: thumbnail?.url || thumbnail?.thumbnail || null,
        quote: product.value.name,
        hashtags: product.value.meta_keywords,
        twitterUser: product.value.shop?.name,
    });
};

const calculateProductPrice = () => {
    let colorPrice = 0;
    let sizePrice = 0;
    const color = product.value.colors?.find((c) => c.id == formData.value.color);
    const size = product.value.sizes?.find((s) => s.id == formData.value.size);
    if (color) colorPrice = color.price ?? 0;
    if (size) sizePrice = size.price ?? 0;

    if (product.value.discount_price > 0) {
        productPrice.value = product.value.discount_price + colorPrice + sizePrice;
        mainPrice.value = product.value.price + colorPrice + sizePrice;
    } else {
        productPrice.value = product.value.price + colorPrice + sizePrice;
        mainPrice.value = productPrice.value;
    }
    discountPercentage.value = mainPrice.value
        ? (((mainPrice.value - productPrice.value) / mainPrice.value) * 100).toFixed(2)
        : 0;
};

/** App parity: Buy Now only, always quantity 1 */
const buyNow = () => {
    if (authStore.token === null) return (authStore.loginModal = true);
    basketStore.addToCart(
        {
            product_id: formData.value.product_id,
            is_buy_now: true,
            quantity: 1,
            size: formData.value.size,
            color: formData.value.color,
            unit: null,
        },
        product.value
    );
    basketStore.buyNowShopId = product.value?.shop?.id;
    router.push({ name: "buynow" });
};

const favoriteAddOrRemove = () => {
    if (authStore.token === null) return (authStore.loginModal = true);
    axios
        .post(
            "/favorite-add-or-remove",
            { product_id: product.value.id },
            { headers: { Authorization: authStore.token } }
        )
        .then(() => {
            product.value.is_favorite = !product.value.is_favorite;
            const added = product.value.is_favorite;
            toast(
                {
                    component: ToastSuccessMessage,
                    props: {
                        title: added ? "Product added to favorite" : "Product removed from favorite",
                        message: added
                            ? "Product added to favorite successfully"
                            : "Product removed from favorite successfully",
                    },
                },
                {
                    type: "default",
                    hideProgressBar: true,
                    icon: false,
                    position: "top-right",
                    toastClassName: "vue-toastification-alert",
                    timeout: 3000,
                }
            );
            authStore.fetchFavoriteProducts();
        })
        .catch(() => {});
};

const onClickHandler = (page) => {
    currentPage.value = page;
    fetchReviews();
};

const fetchReviews = () => {
    axios
        .get("/reviews", {
            params: {
                product_id: route.params.id,
                page: currentPage.value,
                per_page: perPage.value,
            },
        })
        .then((response) => {
            totalReviews.value = response.data.data.total;
            reviews.value = response.data.data.reviews;
            averageRatings.value = response.data.data.average_rating_percentage || {};
        });
};

const endDay = ref("00");
const endHour = ref("00");
const endMinute = ref("00");
const endSecond = ref("00");
let countdownInterval = null;

const startCountdown = () => {
    clearInterval(countdownInterval);
    const endDate = new Date(flashSale.value?.end_date).getTime();
    if (!flashSale.value?.end_date) return;
    countdownInterval = setInterval(() => {
        const timeLeft = endDate - Date.now();
        if (timeLeft <= 0) {
            clearInterval(countdownInterval);
            endDay.value = endHour.value = endMinute.value = endSecond.value = "00";
            return;
        }
        endDay.value = String(Math.floor(timeLeft / (1000 * 60 * 60 * 24))).padStart(2, "0");
        endHour.value = String(Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))).padStart(2, "0");
        endMinute.value = String(Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60))).padStart(2, "0");
        endSecond.value = String(Math.floor((timeLeft % (1000 * 60)) / 1000)).padStart(2, "0");
    }, 1000);
};

const fetchProductDetails = () => {
    isLoading.value = true;
    axios
        .get("/product-details", {
            params: { product_id: route.params.id },
            headers: { Authorization: authStore.token },
        })
        .then((response) => {
            product.value = response.data.data.product;
            relatedProducts.value = response.data.data.related_products || [];
            flashSale.value = response.data.data.product.flash_sale || null;
            selectedThumbIndex.value = 0;
            mainVideoPlaying.value = false;
            selectedThumbnail.value = product.value?.thumbnails?.[0]?.thumbnail || null;
            mainSwiper.value = null;
            thumbButtonRefs.value = [];
            if (flashSale.value) startCountdown();
            formData.value.color = product.value.colors?.length ? product.value.colors[0].id : null;
            formData.value.size = product.value.sizes?.length ? product.value.sizes[0].id : null;
            calculateProductPrice();
            isLoading.value = false;
            nextTick(() => {
                startAutoplay();
            });
        })
        .catch(() => {
            isLoading.value = false;
        });
};

onMounted(() => {
    fetchProductDetails();
    window.scrollTo(0, 0);
});

watch(formData, () => calculateProductPrice(), { deep: true });

watch(route, async () => {
    stopAutoplay();
    await nextTick();
    window.scrollTo(0, 0);
    activeTab.value = "overview";
    formData.value.product_id = route.params.id;
    fetchProductDetails();
});

onUnmounted(() => {
    clearInterval(countdownInterval);
    if (transitionTimer !== null) clearTimeout(transitionTimer);
    stopAutoplay();
});
</script>

<style scoped>
.discount-badge {
    position: absolute;
    top: 10px;
    left: 10px;
    z-index: 10;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: max-content;
    max-width: calc(100% - 20px);
    height: auto;
    padding: 4px 8px;
    border-radius: 999px;
    background: #ef4444;
    color: #fff;
    font-size: 11px;
    font-weight: 800;
    line-height: 1.15;
    letter-spacing: 0.01em;
    text-transform: uppercase;
    white-space: nowrap;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.08);
    pointer-events: none;
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

/* ══════════════════════════════════════════════
   Product Viewer — 3D Perspective Stage
   ══════════════════════════════════════════════ */

/**
 * pv-stage: fills the parent rounded box (overflow:hidden is on the parent),
 * establishes the 3D perspective for child layers.
 */
.pv-stage {
    position: absolute;
    inset: 0;
    /* Perspective anchored at the vertical centre of the box */
    perspective: 1200px;
    perspective-origin: 50% 50%;
    overflow: hidden;
}

/**
 * pv-layer: a full-size absolutely-positioned layer used for both
 * the outgoing and incoming image during a transition, and as the
 * static single layer when no transition is running.
 * Each layer centres its image exactly as the old Swiper slide did.
 */
.pv-layer {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    /* Preserve 3D so rotateX works with the parent perspective */
    transform-style: preserve-3d;
    will-change: transform, opacity;
    /* Prevent the image from bleeding outside the rounded box */
    overflow: hidden;
}

/* Static layer — no animation, just centred display */
.pv-layer--static {
    animation: none;
    transform: none;
    opacity: 1;
}

/* ── Outgoing: slides down + shrinks + tilts backward + fades ── */
.pv-layer--out {
    z-index: 1;
    animation: pv-slide-out 650ms cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
}

/* ── Incoming: slides in from above + expands + un-tilts + fades in ── */
.pv-layer--in {
    z-index: 2;
    animation: pv-slide-in 650ms cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
}

@keyframes pv-slide-out {
    0% {
        opacity: 1;
        transform: translateY(0%) scale(1) rotateX(0deg);
    }
    100% {
        opacity: 0;
        transform: translateY(100%) scale(0.96) rotateX(-8deg);
    }
}

@keyframes pv-slide-in {
    0% {
        opacity: 0;
        transform: translateY(-100%) scale(0.96) rotateX(8deg);
    }
    100% {
        opacity: 1;
        transform: translateY(0%) scale(1) rotateX(0deg);
    }
}

/* ── Prev / Next navigation arrows ── */
.pv-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    z-index: 20;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.90);
    border: none;
    cursor: pointer;
    color: #f97316; /* orange-500 — matches existing swiper arrow colour */
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
    transition: background 0.15s ease, color 0.15s ease, box-shadow 0.15s ease;
    -webkit-tap-highlight-color: transparent;
}
.pv-nav:hover:not(:disabled) {
    background: #f97316;
    color: #ffffff;
    box-shadow: 0 4px 14px rgba(249, 115, 22, 0.40);
}
.pv-nav:disabled {
    opacity: 0.4;
    cursor: default;
    pointer-events: none;
}
.pv-nav--prev {
    left: 12px;
}
.pv-nav--next {
    right: 12px;
}

/* ── Product image sizing (same as before) ── */
.product-main-image {
    max-width: min(100%, 620px);
    max-height: min(100%, 650px);
    width: auto;
    height: auto;
    object-fit: contain;
    object-position: center;
    /* Prevent the image itself from being dragged during a fast click */
    user-select: none;
}

@media (max-width: 767px) {
    .product-main-image {
        max-width: min(100%, 100%);
        max-height: min(100%, 380px);
    }
    .pv-nav {
        width: 30px;
        height: 30px;
    }
    .pv-nav--prev { left: 6px; }
    .pv-nav--next { right: 6px; }
}

@media (min-width: 768px) and (max-width: 1023px) {
    .product-main-image {
        max-width: min(100%, 560px);
        max-height: min(100%, 500px);
    }
}

/* Respect reduced-motion — animations are skipped in JS already;
   belt-and-suspenders CSS override. */
@media (prefers-reduced-motion: reduce) {
    .pv-layer--out,
    .pv-layer--in {
        animation: none !important;
        opacity: 1 !important;
        transform: none !important;
    }
}

/* Keep Swiper styles for the lightbox gallery modal */
.product-main-swiper {
    height: 100%;
    width: 100%;
}
.product-main-swiper :deep(.swiper-slide) {
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}
.product-main-swiper :deep(.swiper-button-next),
.product-main-swiper :deep(.swiper-button-prev) {
    color: #f97316;
}
</style>
