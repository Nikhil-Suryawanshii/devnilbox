<?php

namespace App\Models;

use App\Enums\SubscriptionStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Shop extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Get the shop user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(ShopSubscription::class);
    }

    /**
     * get emploees for this shop
     */
    public function employees(): HasMany
    {
        return $this->hasMany(User::class, 'shop_id');
    }

    /**
     * get withdraw model for this user.
     */
    public function withdraws(): HasMany
    {
        return $this->hasMany(Withdraw::class, 'shop_id');
    }

    /**
     * Get the logo media for the Shop.
     */
    public function mediaLogo(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'logo_id');
    }

    /**
     * Retrieve the media banner for this instance.
     */
    public function mediaBanner(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'banner_id');
    }

    /**
     * get all gallery images for this shop
     */
    public function galleries(): HasMany
    {
        return $this->hasMany(Gallery::class, 'shop_id');
    }

    /**
     * Get the logo for the Shop as an attribute.
     */
    public function logo(): Attribute
    {
        return Attribute::make(
            get: fn(): string => \App\Support\PublicMedia::url($this->mediaLogo?->src),
        );
    }

    /**
     * Get the banner for the Shop as an attribute.
     */
    public function banner(): Attribute
    {
        return Attribute::make(
            get: fn(): string => \App\Support\PublicMedia::url($this->mediaBanner?->src),
        );
    }

    /**
     * Get all of the products for the Shop.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Retrieve the categories associated with the shop.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'shop_categories');
    }

    /**
     * Retrieve the sub categories associated with the shop.
     */
    public function subCategories(): HasMany
    {
        return $this->hasMany(SubCategory::class);
    }

    /**
     * get all of the brands for the shop.
     */
    public function brands(): HasMany
    {
        return $this->hasMany(Brand::class);
    }

    /**
     * Get all of the coupons for the Shop.
     */
    public function coupons(): HasMany
    {
        return $this->hasMany(Coupon::class);
    }

    /**
     * Get all of the colors for the Shop.
     */
    public function colors(): HasMany
    {
        return $this->hasMany(Color::class);
    }

    /**
     * Get the sizes for the shop.
     */
    public function sizes(): HasMany
    {
        return $this->hasMany(Size::class, 'shop_id');
    }

    /**
     * Get all of the units for the Shop.
     */
    public function units(): HasMany
    {
        return $this->hasMany(Unit::class);
    }

    /**
     * Get all of the orders for the Shop.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get all of the banners for the Shop.
     */
    public function banners(): HasMany
    {
        return $this->hasMany(Banner::class, 'shop_id');
    }

    /**
     * Get all of the posts for the Shop.
     */
    public function posts(): HasMany
    {
        return $this->hasMany(ShopPost::class);
    }

    /**
     * Scope a query to only include active shops.
     *
     * @param  Builder  $builder  The query builder
     * @return mixed
     */
    public function scopeIsActive(Builder $builder)
    {
        return $builder->whereHas('user', function ($query) {
            $query->where('is_active', 1);
        });
    }

    /**
     * Get all of the reviews for the Shop.
     *
     * @return HasMany.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'shop_id');
    }

    public function currentSubscription(): Attribute
    {
        $subscription = $this->subscriptions()->where('status', SubscriptionStatus::ACTIVE)
            ->where(function ($q) {
                $q->whereNull('ends_at')
                    ->orWhere('ends_at', '>', now());
            })
            ->where(function ($q) {
                $q->whereNull('remaining_sales')
                    ->orWhere('remaining_sales', '>', 0);
            })
            ->first();

        return new Attribute(
            get: fn() => $subscription,
        );
    }

    /**
     * Calculates the average rating of the reviews.
     *
     * @return Attribute The average rating attribute.
     */
    public function averageRating(): Attribute
    {
        $avgRating = $this->reviews()->avg('rating');

        return new Attribute(
            get: fn() => (float) number_format($avgRating > 0 ? $avgRating : 5, 1, '.', ''),
        );
    }

    /**
     * Get all users following this shop.
     *
     * @return BelongsToMany
     */
    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'shop_followers', 'shop_id', 'user_id')
            ->withPivot('is_liked')
            ->withTimestamps();
    }

    public function likesCount(): int
    {
        return (int) $this->followers()->wherePivot('is_liked', true)->count();
    }

    /**
     * @return array{is_following: bool, is_liked: bool}
     */
    public function followStateFor(?User $user): array
    {
        if (! $user) {
            return ['is_following' => false, 'is_liked' => false];
        }

        $pivot = $user->followingShops()->where('shop_id', $this->id)->first();

        if (! $pivot) {
            return ['is_following' => false, 'is_liked' => false];
        }

        return [
            'is_following' => true,
            'is_liked' => (bool) $pivot->pivot->is_liked,
        ];
    }

    public function returnOrders(): HasMany
    {
        return $this->hasMany(ReturnOrder::class);
    }
}
