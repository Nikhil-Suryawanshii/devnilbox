<template>
    <div class="shop-hero-wrapper main-container">
        <!-- DESKTOP / TABLET LAYOUT (>= 768px) -->
        <div v-if="!isLoading" class="shop-hero-banner shop-hero-desktop">
            <!-- Full-width cover image -->
            <div class="hero-cover-image">
                <img v-if="shop?.banner" :src="shop?.banner" :alt="shop?.name + ' banner'" loading="lazy" />
            </div>
            <!-- Readability gradient behind the text -->
            <div class="hero-bg-gradient"></div>

            <!-- Content area -->
            <div class="hero-content">
                <!-- Left: Profile + Info -->
                <div class="hero-left">
                    <div class="hero-profile-container">
                        <div class="hero-profile-image">
                            <img :src="shop?.logo" :alt="shop?.name + ' logo'" loading="lazy" />
                        </div>
                    </div>

                    <div class="hero-info">
                        <div class="hero-name-row">
                            <h1 class="hero-shop-name">{{ shop?.name }}</h1>
                            <span class="hero-verified-badge" title="Verified Shop">
                                <svg width="18" height="18" viewBox="0 0 20 20" fill="none">
                                    <circle cx="10" cy="10" r="10" fill="#3B82F6"/>
                                    <path d="M6 10L9 13L14 7" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                        </div>

                        <div class="hero-status-row">
                            <div class="hero-status-badge" :class="shop?.shop_status === 'Online' ? 'online' : 'offline'">
                                <span class="status-dot"></span>
                                <span class="status-text">{{ shop?.shop_status }}</span>
                            </div>
                            <span class="hero-divider">|</span>
                            <span class="hero-items-count">{{ shop?.total_products }}+ {{ $t('Items') }}</span>
                        </div>

                        <div class="hero-meta-row">
                            <div class="hero-rating-inline">
                                <svg class="hero-star-icon" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <span class="hero-rating-value">{{ Number(shop?.rating || 0).toFixed(1) }}</span>
                                <span class="hero-rating-count">({{ shop?.total_reviews }})</span>
                            </div>
                            <span class="hero-divider">|</span>
                            <div class="hero-positive-reviews">
                                <span class="positive-icon">👍</span>
                                <span class="positive-text">{{ positiveReviewPercent }}% {{ $t('Positive Reviews') }}</span>
                            </div>
                        </div>

                        <div class="hero-description" v-if="shop?.description">
                            <div v-html="shop?.description"></div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="hero-actions">
                            <button type="button" class="hero-btn hero-btn-primary" @click="$emit('chat', shop)">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                                </svg>
                                {{ $t('Chat with Shop') }}
                            </button>
                            <button
                                type="button"
                                class="hero-btn hero-btn-secondary"
                                :class="{ 'is-following': isFollowing }"
                                @click="$emit('follow', shop)"
                            >
                                <svg width="16" height="16" viewBox="0 0 24 24" :fill="isFollowing ? 'currentColor' : 'none'" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                                </svg>
                                {{ isFollowing ? $t('Following') : $t('Follow Shop') }}
                            </button>
                            <button type="button" class="hero-btn hero-btn-secondary" @click="$emit('share', shop)">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/>
                                    <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/>
                                </svg>
                                {{ $t('Share') }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Right: Rating summary card -->
                <div class="hero-rating-badge">
                    <div class="rating-badge-stars">
                        <svg v-for="i in 5" :key="i" class="rating-badge-star" :class="i <= Math.round(shop?.rating || 0) ? 'filled' : 'empty'" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    </div>
                    <div class="rating-badge-value">{{ Number(shop?.rating || 0).toFixed(1) }}</div>
                    <div class="rating-badge-reviews">{{ shop?.total_reviews }} {{ $t('Reviews') }}</div>

                    <ul class="rating-badge-list">
                        <li>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                                <polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/>
                            </svg>
                            <span>{{ shop?.total_products }}+ {{ $t('Products') }}</span>
                        </li>
                        <li>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"/>
                            </svg>
                            <span>{{ positiveReviewPercent }}% {{ $t('Positive Reviews') }}</span>
                        </li>
                        <li>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#16A34A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/>
                            </svg>
                            <span>{{ $t('Verified Seller') }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- MOBILE LAYOUT (< 768px) -->
        <div v-if="!isLoading" class="shop-hero-banner shop-hero-mobile">
            <!-- Cover image -->
            <div class="mobile-hero-cover">
                <img v-if="shop?.banner" :src="shop?.banner" :alt="shop?.name + ' banner'" loading="lazy" />
            </div>

            <!-- White profile card overlapping the cover -->
            <div class="mobile-hero-card">
                <div class="mobile-card-top">
                    <div class="hero-profile-image mobile-profile">
                        <img :src="shop?.logo" :alt="shop?.name + ' logo'" loading="lazy" />
                    </div>

                    <div class="mobile-hero-info">
                        <div class="hero-name-row">
                            <h1 class="hero-shop-name">{{ shop?.name }}</h1>
                            <span class="hero-verified-badge" title="Verified Shop">
                                <svg width="16" height="16" viewBox="0 0 20 20" fill="none">
                                    <circle cx="10" cy="10" r="10" fill="#3B82F6"/>
                                    <path d="M6 10L9 13L14 7" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                        </div>

                        <div class="hero-status-row">
                            <div class="hero-status-badge" :class="shop?.shop_status === 'Online' ? 'online' : 'offline'">
                                <span class="status-dot"></span>
                                <span class="status-text">{{ shop?.shop_status }}</span>
                            </div>
                            <span class="hero-divider">|</span>
                            <span class="hero-items-count">{{ shop?.total_products }}+ {{ $t('Items') }}</span>
                        </div>

                        <div class="hero-meta-row mobile-meta-row">
                            <div class="hero-rating-inline">
                                <svg class="hero-star-icon" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <span class="hero-rating-value">{{ Number(shop?.rating || 0).toFixed(1) }}</span>
                                <span class="hero-rating-count">({{ shop?.total_reviews }})</span>
                            </div>
                            <div class="hero-positive-reviews mobile-positive">
                                <span class="positive-icon">👍</span>
                                <span class="positive-text">{{ positiveReviewPercent }}% {{ $t('Positive Reviews') }}</span>
                            </div>
                        </div>

                        <div class="hero-description" v-if="shop?.description">
                            <div v-html="shop?.description"></div>
                        </div>
                    </div>

                    <!-- Rating summary -->
                    <div class="hero-rating-badge mobile-rating-badge">
                        <div class="rating-badge-stars">
                            <svg v-for="i in 5" :key="i" class="rating-badge-star" :class="i <= Math.round(shop?.rating || 0) ? 'filled' : 'empty'" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </div>
                        <div class="rating-badge-value">{{ Number(shop?.rating || 0).toFixed(1) }}</div>
                        <div class="rating-badge-reviews">{{ shop?.total_reviews }} {{ $t('Reviews') }}</div>

                        <ul class="rating-badge-list">
                            <li>
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                                    <polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/>
                                </svg>
                                <span>{{ shop?.total_products }}+ {{ $t('Products') }}</span>
                            </li>
                            <li>
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"/>
                                </svg>
                                <span>{{ positiveReviewPercent }}% {{ $t('Reviews') }}</span>
                            </li>
                            <li>
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#16A34A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/>
                                </svg>
                                <span>{{ $t('Verified Seller') }}</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Actions -->
                <div class="hero-actions mobile-actions">
                    <button type="button" class="hero-btn hero-btn-primary" @click="$emit('chat', shop)">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                        </svg>
                        {{ $t('Chat with Shop') }}
                    </button>
                    <button
                        type="button"
                        class="hero-btn hero-btn-secondary"
                        :class="{ 'is-following': isFollowing }"
                        @click="$emit('follow', shop)"
                    >
                        <svg width="16" height="16" viewBox="0 0 24 24" :fill="isFollowing ? 'currentColor' : 'none'" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                        </svg>
                        {{ isFollowing ? $t('Following') : $t('Follow') }}
                    </button>
                    <button type="button" class="hero-btn hero-btn-secondary" @click="$emit('share', shop)">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/>
                            <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/>
                        </svg>
                        {{ $t('Share') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Loading skeleton -->
        <div v-if="isLoading" class="shop-hero-skeleton">
            <SkeletonLoader class="skeleton-banner" />
            <div class="skeleton-content">
                <SkeletonLoader class="skeleton-avatar" />
                <div class="skeleton-lines">
                    <SkeletonLoader class="skeleton-line w-48 h-5" />
                    <SkeletonLoader class="skeleton-line w-32 h-4" />
                    <SkeletonLoader class="skeleton-line w-64 h-3" />
                    <div class="flex gap-3 mt-3">
                        <SkeletonLoader class="w-24 h-10 rounded-xl" />
                        <SkeletonLoader class="w-20 h-10 rounded-xl" />
                        <SkeletonLoader class="w-20 h-10 rounded-xl" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import SkeletonLoader from './SkeletonLoader.vue';

const props = defineProps({
    shop: {
        type: Object,
        default: () => ({})
    },
    isLoading: {
        type: Boolean,
        default: true
    }
});

defineEmits(['follow', 'chat', 'share', 'favourite']);

const positiveReviewPercent = computed(() => {
    // Use actual data if available, otherwise show a default
    return props.shop?.positive_review_percentage ?? 98;
});

const isFollowing = computed(() => !!props.shop?.is_following);
</script>

<style scoped>
/* ==========================================
   SHOP HERO BANNER
   ========================================== */

.shop-hero-wrapper {
    padding-top: 20px;
    padding-bottom: 8px;
}

/* ---- DESKTOP LAYOUT ---- */
.shop-hero-desktop {
    display: block;
    position: relative;
    border-radius: 24px;
    overflow: hidden;
    min-height: 270px;
    background: linear-gradient(135deg, #FFF8F2 0%, #FFF0E0 40%, #FFE8D0 100%);
    box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06), 0 1px 4px rgba(0, 0, 0, 0.04);
}

/* Cover now fills the whole card (like the reference design) */
.hero-cover-image {
    position: absolute;
    inset: 0;
    z-index: 1;
}

.hero-cover-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
}

/* Soft fade only on the far left so text stays readable */
.hero-bg-gradient {
    position: absolute;
    inset: 0;
    background: linear-gradient(
        90deg,
        rgba(255, 248, 242, 0.92) 0%,
        rgba(255, 244, 234, 0.75) 28%,
        rgba(255, 240, 224, 0.2) 48%,
        transparent 62%
    );
    z-index: 2;
    pointer-events: none;
}

.hero-content {
    position: relative;
    z-index: 3;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 28px 32px;
    min-height: 270px;
    gap: 24px;
}

.hero-left {
    display: flex;
    gap: 22px;
    align-items: flex-start;
    flex: 1;
    max-width: 62%;
}

/* ---- PROFILE IMAGE ---- */
.hero-profile-container {
    flex-shrink: 0;
}

.hero-profile-image {
    width: 104px;
    height: 104px;
    border-radius: 22px;
    background: #FFFFFF;
    padding: 6px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1), 0 1px 4px rgba(0, 0, 0, 0.04);
}

.hero-profile-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 17px;
}

/* ---- SHOP INFO ---- */
.hero-info {
    display: flex;
    flex-direction: column;
    gap: 8px;
    padding-top: 2px;
    min-width: 0;
}

.hero-name-row {
    display: flex;
    align-items: center;
    gap: 8px;
    min-width: 0;
}

.hero-shop-name {
    font-size: 1.6rem;
    font-weight: 800;
    color: #1A1A1A;
    letter-spacing: -0.02em;
    line-height: 1.3;
    margin: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.hero-verified-badge {
    display: inline-flex;
    align-items: center;
    flex-shrink: 0;
}

/* ---- STATUS ---- */
.hero-status-row {
    display: flex;
    align-items: center;
    gap: 10px;
}

.hero-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 0.8rem;
    font-weight: 500;
}

.hero-status-badge.online .status-dot {
    width: 9px;
    height: 9px;
    border-radius: 50%;
    background: #22C55E;
    box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.2);
    animation: dotPulse 2s ease-in-out infinite;
}

.hero-status-badge.offline .status-dot {
    width: 9px;
    height: 9px;
    border-radius: 50%;
    background: #9CA3AF;
}

@keyframes dotPulse {
    0%, 100% { box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.2); }
    50% { box-shadow: 0 0 0 6px rgba(34, 197, 94, 0.1); }
}

.status-text {
    color: #374151;
}

.hero-divider {
    color: #D1D5DB;
    font-size: 0.85rem;
}

.hero-items-count {
    font-size: 0.875rem;
    color: #4B5563;
    font-weight: 500;
}

/* ---- RATING INLINE ---- */
.hero-meta-row {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.hero-rating-inline {
    display: flex;
    align-items: center;
    gap: 4px;
}

.hero-star-icon {
    width: 16px;
    height: 16px;
    color: #F59E0B;
}

.hero-rating-value {
    font-size: 0.875rem;
    font-weight: 700;
    color: #1A1A1A;
}

.hero-rating-count {
    font-size: 0.8rem;
    color: #6B7280;
}

/* ---- POSITIVE REVIEWS ---- */
.hero-positive-reviews {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 0.8rem;
    color: #4B5563;
}

.positive-icon {
    font-size: 0.8rem;
}

.positive-text {
    font-weight: 500;
}

/* ---- DESCRIPTION ---- */
.hero-description {
    font-size: 0.85rem;
    line-height: 1.5;
    color: #4B5563;
    max-width: 420px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    margin-top: 2px;
}

.hero-description :deep(p) {
    margin: 0;
}

/* ---- ACTION BUTTONS ---- */
.hero-actions {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    margin-top: 10px;
}

.hero-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 10px 20px;
    border-radius: 12px;
    font-size: 0.875rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    border: none;
    outline: none;
    white-space: nowrap;
}

.hero-btn:active {
    transform: scale(0.97);
}

.hero-btn-primary {
    background: linear-gradient(135deg, #FF6B00 0%, #FF8C3A 100%);
    color: #FFFFFF;
    box-shadow: 0 2px 8px rgba(255, 107, 0, 0.3);
}

.hero-btn-primary:hover {
    background: linear-gradient(135deg, #E56000 0%, #FF7A20 100%);
    box-shadow: 0 4px 16px rgba(255, 107, 0, 0.35);
    transform: translateY(-1px);
}

.hero-btn-secondary {
    background: #FFFFFF;
    color: #374151;
    border: 1px solid #E5E7EB;
}

.hero-btn-secondary:hover {
    border-color: #D1D5DB;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    transform: translateY(-1px);
}

.hero-btn-secondary.is-following {
    color: #FF6B00;
    background: #FFF7F0;
    border-color: #FFD4AD;
}

/* ---- RATING SUMMARY CARD ---- */
.hero-rating-badge {
    flex-shrink: 0;
    width: 210px;
    background: rgba(255, 255, 255, 0.96);
    border-radius: 18px;
    padding: 16px 18px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.7);
}

.rating-badge-stars {
    display: flex;
    gap: 2px;
}

.rating-badge-star {
    width: 16px;
    height: 16px;
}

.rating-badge-star.filled {
    color: #F59E0B;
}

.rating-badge-star.empty {
    color: #E5E7EB;
}

.rating-badge-value {
    font-size: 1.75rem;
    font-weight: 800;
    color: #1A1A1A;
    line-height: 1.1;
}

.rating-badge-reviews {
    font-size: 0.75rem;
    color: #6B7280;
    font-weight: 500;
    padding-bottom: 8px;
    border-bottom: 1px solid #F3F4F6;
    width: 100%;
    text-align: center;
}

.rating-badge-list {
    list-style: none;
    margin: 6px 0 0;
    padding: 0;
    width: 100%;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.rating-badge-list li {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.78rem;
    font-weight: 500;
    color: #374151;
}

.rating-badge-list li svg {
    flex-shrink: 0;
    color: #4B5563;
}

/* ---- MOBILE LAYOUT ---- */
.shop-hero-mobile {
    display: none;
    flex-direction: column;
}

.mobile-hero-cover {
    position: relative;
    width: 100%;
    height: 150px;
    overflow: hidden;
    border-radius: 18px;
    background: linear-gradient(135deg, #FFF0E0, #FFD9B3);
}

.mobile-hero-cover img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.mobile-hero-card {
    position: relative;
    z-index: 2;
    margin: -26px 6px 0;
    background: #FFFFFF;
    border-radius: 18px;
    padding: 14px;
    box-shadow: 0 6px 24px rgba(0, 0, 0, 0.08);
}

.mobile-card-top {
    display: grid;
    grid-template-columns: auto minmax(0, 1fr) auto;
    gap: 12px;
    align-items: start;
}

.mobile-profile {
    width: 76px;
    height: 76px;
    border-radius: 18px;
    padding: 4px;
    border: 1px solid #F3F4F6;
}

.mobile-profile img {
    border-radius: 14px;
}

.mobile-hero-info {
    display: flex;
    flex-direction: column;
    gap: 5px;
    min-width: 0;
}

.mobile-hero-info .hero-shop-name {
    font-size: 1.15rem;
}

.mobile-hero-info .hero-status-row {
    gap: 6px;
    flex-wrap: wrap;
}

.mobile-hero-info .hero-status-badge,
.mobile-hero-info .hero-items-count {
    font-size: 0.75rem;
}

.mobile-meta-row {
    gap: 8px;
}

.mobile-positive {
    font-size: 0.72rem;
}

.mobile-hero-info .hero-description {
    font-size: 0.75rem;
    line-height: 1.45;
    max-width: 100%;
    -webkit-line-clamp: 3;
}

.mobile-rating-badge {
    width: 118px;
    padding: 10px 8px;
    border-radius: 14px;
    border: 1px solid #F3F4F6;
    box-shadow: none;
    background: #FFFFFF;
}

.mobile-rating-badge .rating-badge-star {
    width: 12px;
    height: 12px;
}

.mobile-rating-badge .rating-badge-value {
    font-size: 1.4rem;
}

.mobile-rating-badge .rating-badge-reviews {
    font-size: 0.65rem;
    padding-bottom: 6px;
}

.mobile-rating-badge .rating-badge-list {
    gap: 6px;
    margin-top: 4px;
}

.mobile-rating-badge .rating-badge-list li {
    font-size: 0.66rem;
    gap: 5px;
}

.mobile-actions {
    display: grid;
    grid-template-columns: 1.5fr 1fr 1fr;
    gap: 8px;
    margin-top: 14px;
    width: 100%;
}

.mobile-actions .hero-btn {
    padding: 10px 6px;
    font-size: 0.78rem;
    gap: 5px;
    min-width: 0;
}

/* ---- SKELETON LOADING ---- */
.shop-hero-skeleton {
    border-radius: 24px;
    overflow: hidden;
    background: #F3F4F6;
    min-height: 280px;
}

.skeleton-banner {
    width: 100%;
    height: 160px;
}

.skeleton-content {
    display: flex;
    gap: 20px;
    padding: 24px;
    align-items: flex-start;
}

.skeleton-avatar {
    width: 80px;
    height: 80px;
    border-radius: 18px;
    flex-shrink: 0;
}

.skeleton-lines {
    display: flex;
    flex-direction: column;
    gap: 8px;
    flex: 1;
}

.skeleton-line {
    border-radius: 6px;
}

/* ==========================================
   RESPONSIVE BREAKPOINTS
   ========================================== */

/* Tablet: 768px–1199px */
@media (max-width: 1199px) and (min-width: 768px) {
    .hero-content {
        padding: 22px 24px;
        min-height: 240px;
        gap: 16px;
    }

    .hero-profile-image {
        width: 84px;
        height: 84px;
        border-radius: 18px;
    }

    .hero-shop-name {
        font-size: 1.3rem;
    }

    .hero-left {
        max-width: 66%;
        gap: 16px;
    }

    .hero-description {
        max-width: 320px;
        font-size: 0.8rem;
    }

    .hero-btn {
        padding: 9px 14px;
        font-size: 0.8rem;
    }

    .hero-rating-badge {
        width: 180px;
        padding: 12px 14px;
    }

    .rating-badge-value {
        font-size: 1.4rem;
    }
}

/* Mobile: < 768px */
@media (max-width: 767px) {
    .shop-hero-wrapper {
        padding-top: 12px;
    }

    .shop-hero-desktop {
        display: none !important;
    }

    .shop-hero-mobile {
        display: flex;
    }

    .shop-hero-skeleton {
        border-radius: 20px;
        min-height: 340px;
    }

    .skeleton-banner {
        height: 160px;
    }

    .skeleton-content {
        flex-direction: column;
        align-items: center;
        padding: 16px;
    }

    .skeleton-avatar {
        margin-top: -40px;
    }

    .skeleton-lines {
        align-items: center;
    }
}

/* Narrow phones: < 400px — tighten the card */
@media (max-width: 399px) {
    .mobile-hero-cover {
        height: 130px;
    }

    .mobile-hero-card {
        padding: 12px;
        margin: -22px 2px 0;
    }

    .mobile-card-top {
        gap: 10px;
    }

    .mobile-profile {
        width: 64px;
        height: 64px;
    }

    .mobile-rating-badge {
        width: 104px;
        padding: 8px 6px;
    }

    .mobile-hero-info .hero-shop-name {
        font-size: 1.05rem;
    }

    /* Positive % is already in the summary card */
    .mobile-positive {
        display: none;
    }

    .mobile-actions .hero-btn {
        font-size: 0.72rem;
        padding: 9px 4px;
    }
}

/* Very small phones: < 350px — drop the summary card */
@media (max-width: 349px) {
    .mobile-rating-badge {
        display: none;
    }

    .mobile-card-top {
        grid-template-columns: auto minmax(0, 1fr);
    }
}
</style>
