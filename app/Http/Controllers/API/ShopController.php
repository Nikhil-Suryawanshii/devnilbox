<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ShopDetailsResource;
use App\Http\Resources\ShopResource;
use App\Models\Shop;
use App\Repositories\ProductRepository;
use App\Repositories\ShopRepository;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    /**
     * Get all shops with pagination and filtering options.
     *
     * @param  Request  $request  The request object
     * @return Some_Return_Value The JSON response
     */
    public function index(Request $request)
    {
        $page = $request->page;
        $perPage = $request->per_page;
        $skip = ($page * $perPage) - $perPage;

        $shops = ShopRepository::query()->isActive();
        $shops = $shops->whereHas('products', function ($query) {
            return $query->isActive();
        });

        $total = $shops->count();

        $shops = $shops->when($perPage && $page, function ($query) use ($perPage, $skip) {
            return $query->skip($skip)->take($perPage);
        });

         if (auth('sanctum')->check()) {
            $userId = auth('sanctum')->id();
            $shops->withExists(['followers as is_followed' => function ($query) use ($userId) {
                // $query->where('user_id', $userId)->where('shop_followers.is_liked', true);
                $query->where('user_id', $userId);
            }]);
        }

        $shops = $shops->get();

        return $this->json('shops', [
            'total' => $total,
            'shops' => ShopResource::collection($shops),
        ]);
    }

    /**
     * Display the shop details.
     *
     * @param  Shop  $shop  The shop instance
     * @return mixed
     */
    public function show(Shop $shop)
    {
        return $this->json('shop details', [
            'shop' => ShopDetailsResource::make($shop),
        ]);
    }

    /**
     * Get all categories of a shop with pagination .
     */
    public function shopCategory(Request $request)
    {
        $request->validate([
            'shop_id' => 'required|exists:shops,id',
        ]);

        $page = $request->page;
        $perPage = $request->per_page;
        $skip = ($page * $perPage) - $perPage;

        $shop = ShopRepository::find($request->shop_id);

        $categories = $shop->categories()->active()->where(function ($query) use ($perPage, $page, $skip) {
            $query->when($perPage && $page, function ($query) use ($perPage, $skip) {
                return $query->skip($skip)->take($perPage);
            });
        })->get();

        $total = $shop->categories->count();

        return $this->json('Shop categories', [
            'total' => $total,
            'categories' => CategoryResource::collection($categories),
        ]);
    }

    /**
     * Get top 10 shops.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function topShops()
    {
        $shops = ShopRepository::query()->isActive()
            ->withCount('orders')
            ->withAvg('reviews as average_rating', 'rating')
            ->orderByDesc('average_rating')
            ->orderByDesc('orders_count')
            ->take(10)->get();

        return $this->json('top shops', [
            'shops' => ShopResource::collection($shops),
        ]);
    }

    /**
     * Get popular products of a shop.
     *
     * @param  Request  $request  The request object
     */
    public function popularProducts(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $product = ProductRepository::find($request->product_id);
        $shop = $product->shop;

        $products = $shop->products()->where('id', '!=', $product->id)->isActive()
            ->withCount('orders')
            ->withAvg('reviews as average_rating', 'rating')
            ->orderByDesc('orders_count')->orderByDesc('average_rating')->take(6)->get();

        return $this->json('popular products', [
            'products' => ProductResource::collection($products),
        ]);
    }

    /**
     * Update shop details.
     *
     * @param  Request  $request  The request object
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $request->validate([
            'shop_id' => 'required|exists:shops,id',
            'name' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpg,png,jpeg,gif|max:2048',
        ]);

        $shop = ShopRepository::find($request->shop_id);

        if (!$shop) {
            return $this->json('Shop not found', [], 404);
        }

        // Update shop logo if provided
        $thumbnail = $shop->mediaLogo;
        if ($request->hasFile('logo')) {
            if ($thumbnail) {
                $thumbnail = \App\Repositories\MediaRepository::updateByRequest(
                    $request->logo,
                    'shops/logo',
                    'image',
                    $thumbnail
                );
            } else {
                $thumbnail = \App\Repositories\MediaRepository::storeByRequest(
                    $request->logo,
                    'shops/logo',
                    'image'
                );
            }
        }

        // Update shop details
        $shop->update([
            'name' => $request->name ?? $shop->name,
            'address' => $request->address ?? $shop->address,
            'logo_id' => $thumbnail ? $thumbnail->id : $shop->logo_id,
        ]);

        return $this->json('Shop updated successfully', [
            'shop' => ShopDetailsResource::make($shop->fresh()),
        ]);
    }
}

