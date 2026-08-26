<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddFavoriteRequest;
use App\Http\Requests\ReviewRequest;
use App\Http\Resources\BrandResource;
use App\Http\Resources\ColorResource;
use App\Http\Resources\ProductDetailsResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ReviewResource;
use App\Http\Resources\SizeResource;
use App\Models\FlashSale;
use App\Repositories\ProductRepository;
use App\Repositories\ReviewRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Retrieve a paginated list of products based on the provided request parameters.
     *
     * @param  Request  $request  The request object containing page, per_page, and search parameters
     * @return Some_Return_Value The JSON response containing total and products data
     */
    public function indexOld(Request $request)
    { 
        $page = $request->page;
        $perPage = $request->per_page;
        $skip = ($page * $perPage) - $perPage;

        $search = $request->search;
        $shopID = $request->shop_id;
        $categoryID = $request->category_id;
        $subCategoryID = $request->sub_category_id;

        $rating = $request->rating; // 4.0
        $sortType = $request->sort_type;
        $minPrice = $request->min_price;
        $maxPrice = $request->max_price;
        $brandID = $request->brand_id;
        $colorID = $request->color_id;
        $sizeID = $request->size_id;
        $isDigital = $request->is_digital == true ? true : false;

        $generaleSetting = generaleSetting('setting');
        $shop = null;
        if ($generaleSetting?->shop_type == 'single') {
            $shop = generaleSetting('rootShop');
        }

        // get data for
        $rootShop = $shop ?? generaleSetting('rootShop');
        $productQuery = ProductRepository::query()->when($shop, function ($query) use ($shop) {
            return $query->where('shop_id', $shop->id);
        })->isActive();

        $flashSale = FlashSale::isActive()->first();
        $flashSaleMinPrice = $flashSale ? $flashSale->products->min('pivot.price') : null;

        $productMinPrice = $productQuery->min('price');
        if ($flashSaleMinPrice && $flashSaleMinPrice < $productMinPrice) {
            $productMinPrice = $flashSaleMinPrice;
        }

        $productMaxPrice = $productQuery->max('price');
        $sizes = $rootShop?->sizes()->isActive()->get();
        $colors = $rootShop?->colors()->isActive()->get();
        $brands = $rootShop?->brands()->isActive()->get();

        // filter query
        $products = ProductRepository::query()
            ->withSum('orders as orders_count', 'order_products.quantity')
            ->withAvg('reviews as average_rating', 'rating')
            ->withCount('favorites as favorites_count')
            ->with(['shop'])
            ->isActive()
            ->when($isDigital, function ($query) {
                return $query->where('is_digital', true);
            })
            ->when($shop, function ($query) use ($shop) {
                return $query->where('shop_id', $shop->id);
            })->when($shopID && ! $shop, function ($query) use ($shopID) {
                return $query->where('shop_id', $shopID);
            })
            ->when($search, function ($query) use ($search) {
                return $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%'.$search.'%')
                        ->orWhere('short_description', 'like', '%'.$search.'%')
                        ->orWhere('code', 'like', '%'.$search.'%');
                });
            })->when($brandID, function ($query) use ($brandID) {
                return $query->where('brand_id', $brandID);
            })->when($colorID, function ($query) use ($colorID) {
                return $query->whereHas('colors', function ($query) use ($colorID) {
                    return $query->where('id', $colorID);
                });
            })->when($sizeID, function ($query) use ($sizeID) {
                return $query->whereHas('sizes', function ($query) use ($sizeID) {
                    return $query->where('id', $sizeID);
                });
            })->when($categoryID, function ($query) use ($categoryID) {
                return $query->whereHas('categories', function ($query) use ($categoryID) {
                    return $query->where('id', $categoryID);
                });
            })->when($subCategoryID, function ($query) use ($subCategoryID) {
                $query->whereHas('subcategories', function ($query) use ($subCategoryID) {
                    return $query->where('id', $subCategoryID);
                });
            })->when($rating, function ($query) use ($rating) {
                $ratingValue = floatval($rating);
                $upperBound = $ratingValue + 1;

                return $query->havingRaw('average_rating >= '.$rating.' AND average_rating < '.$upperBound);
            })->when($sortType == 'top_selling', function ($query) {
                return $query->orderByDesc('orders_count');
            })->when($sortType == 'popular_product', function ($query) {
                return $query->orderByDesc('orders_count')->orderByDesc('average_rating');
            })->when($sortType == 'newest' || $sortType == 'just_for_you', function ($query) {
                return $query->orderBy('id', 'desc');
            })->when($minPrice || $maxPrice, function ($query) use ($minPrice, $maxPrice) {
                $query->whereRaw('
                    COALESCE(
                        (SELECT flash_sale_products.price
                         FROM flash_sale_products
                         INNER JOIN flash_sales ON flash_sales.id = flash_sale_products.flash_sale_id
                         WHERE flash_sale_products.product_id = products.id
                         AND flash_sale_products.quantity > 0
                         AND flash_sales.status = 1
                         AND flash_sales.start_date <= CURDATE()
                         AND flash_sales.end_date >= CURDATE()
                         AND (flash_sales.start_time <= CURTIME() OR flash_sales.end_time >= CURTIME())
                         ORDER BY flash_sale_products.price ASC LIMIT 1
                        ),
                        IF(discount_price > 0, discount_price, price)
                    ) BETWEEN ? AND ?
                ', [$minPrice ?? 0, $maxPrice ?? PHP_INT_MAX]);
            })
            ->when(in_array($sortType, ['high_to_low', 'low_to_high']), function ($query) use ($sortType) {
                $order = $sortType === 'high_to_low' ? 'DESC' : 'ASC';

                return $query->orderByRaw("
                    COALESCE(
                        (SELECT flash_sale_products.price
                         FROM flash_sale_products
                         INNER JOIN flash_sales ON flash_sales.id = flash_sale_products.flash_sale_id
                         WHERE flash_sale_products.product_id = products.id
                         AND flash_sale_products.quantity > 0
                         AND flash_sales.status = 1
                         AND flash_sales.start_date <= CURDATE()
                         AND flash_sales.end_date >= CURDATE()
                         AND (flash_sales.start_time <= CURTIME() OR flash_sales.end_time >= CURTIME())
                         ORDER BY flash_sale_products.price $order LIMIT 1
                        ),
                        IF(discount_price > 0, discount_price, price)
                    ) $order
                ")->orderByDesc('id');
            });

        $total = $products->count();
        $products = $products->when($perPage && $page, function ($query) use ($perPage, $skip) {
            return $query->skip($skip)->take($perPage);
        })->get();

        return $this->json('products', [
            'total' => $total,
            'products' => ProductResource::collection($products),
            'filters' => [
                'sizes' => $sizes ? SizeResource::collection($sizes) : [],
                'colors' => $colors ? ColorResource::collection($colors) : [],
                'brands' => $brands ? BrandResource::collection($brands) : [],
                'min_price' => (int) intval($productMinPrice),
                'max_price' => (int) intval($productMaxPrice),
            ],
        ]);
    }
    
    


       public function index(Request $request)
    {
        $page = max((int) $request->page, 1);
        $perPage = min(max((int) $request->per_page, 12), 50);

        $search = trim($request->search ?? '');

        $shopID = $request->shop_id;
        $categoryID = $request->category_id;
        $subCategoryID = $request->sub_category_id;

        $rating = $request->rating;
        $sortType = $request->sort_type;

        $minPrice = $request->min_price;
        $maxPrice = $request->max_price;

        $brandID = $request->brand_id;
        $colorID = $request->color_id;
        $sizeID = $request->size_id;

        $isDigital = filter_var(
            $request->is_digital,
            FILTER_VALIDATE_BOOLEAN
        );

        /*
        |--------------------------------------------------------------------------
        | Cache Key
        |--------------------------------------------------------------------------
        */

        $cacheKey = 'products_' . md5(json_encode($request->all()));

        $response = Cache::remember($cacheKey, now()->addMinutes(5), function () use (
            $page,
            $perPage,
            $search,
            $shopID,
            $categoryID,
            $subCategoryID,
            $rating,
            $sortType,
            $minPrice,
            $maxPrice,
            $brandID,
            $colorID,
            $sizeID,
            $isDigital
        ) {

            /*
            |--------------------------------------------------------------------------
            | Shop Settings
            |--------------------------------------------------------------------------
            */

            $generalSetting = generaleSetting('setting');

            $shop = null;

            if ($generalSetting?->shop_type === 'single') {
                $shop = generaleSetting('rootShop');
            }

            $rootShop = $shop ?? generaleSetting('rootShop');

            /*
            |--------------------------------------------------------------------------
            | Filters Data
            |--------------------------------------------------------------------------
            */

            $sizes = $rootShop?->sizes()
                ->select('id', 'name')
                ->isActive()
                ->get();

            $colors = $rootShop?->colors()
                ->select('id', 'name', 'color_code')
                ->isActive()
                ->get();

            $brands = $rootShop?->brands()
                ->select('id', 'name')
                ->isActive()
                ->get();

            /*
            |--------------------------------------------------------------------------
            | Price Range
            |--------------------------------------------------------------------------
            */

            $priceQuery = ProductRepository::query()
                ->isActive()
                ->when($shop, function ($query) use ($shop) {
                    return $query->where('shop_id', $shop->id);
                });

            $productMinPrice = (clone $priceQuery)->min(
                DB::raw('IF(discount_price > 0, discount_price, price)')
            );

            $productMaxPrice = (clone $priceQuery)->max(
                DB::raw('IF(discount_price > 0, discount_price, price)')
            );

            /*
            |--------------------------------------------------------------------------
            | Products Query
            |--------------------------------------------------------------------------
            */

            $products = ProductRepository::query()

                ->select([
                    'id',
                    'shop_id',
                    'brand_id',
                    'media_id',
                    'name',
                    'slug',
                    'code',
                    'price',
                    'discount_price',
                    'favourite_count',
                    'share_count',
                    'quantity',
                    'is_active',
                    'is_digital',
                    'created_at',
                ])

                /*
                |--------------------------------------------------------------------------
                | Eager Loading
                |--------------------------------------------------------------------------
                */

                ->with([
                    'brand:id,name',
                    'shop:id,name',
                    'media:id,src',
                ])
                ->withSum('orders as orders_count', 'order_products.quantity')
                ->withAvg('reviews as average_rating', 'rating')

                /*
                |--------------------------------------------------------------------------
                | Base Filters
                |--------------------------------------------------------------------------
                */

                ->isActive()

                ->where('quantity', '>', 0)

                ->when($isDigital, function ($query) {
                    return $query->where('is_digital', true);
                })

                ->when($shop, function ($query) use ($shop) {
                    return $query->where('shop_id', $shop->id);
                })

                ->when($shopID && !$shop, function ($query) use ($shopID) {
                    return $query->where('shop_id', $shopID);
                })

                /*
                |--------------------------------------------------------------------------
                | Search
                |--------------------------------------------------------------------------
                */

                ->when($search, function ($query) use ($search) {

                    return $query->where(function ($q) use ($search) {

                        $q->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('code', 'LIKE', "%{$search}%");
                    });
                })

                /*
                |--------------------------------------------------------------------------
                | Brand Filter
                |--------------------------------------------------------------------------
                */

                ->when($brandID, function ($query) use ($brandID) {
                    return $query->where('brand_id', $brandID);
                })

                /*
                |--------------------------------------------------------------------------
                | Color Filter
                |--------------------------------------------------------------------------
                */

                ->when($colorID, function ($query) use ($colorID) {

                    return $query->whereHas('colors', function ($q) use ($colorID) {

                        $q->where('colors.id', $colorID);
                    });
                })

                /*
                |--------------------------------------------------------------------------
                | Size Filter
                |--------------------------------------------------------------------------
                */

                ->when($sizeID, function ($query) use ($sizeID) {

                    return $query->whereHas('sizes', function ($q) use ($sizeID) {

                        $q->where('sizes.id', $sizeID);
                    });
                })

                /*
                |--------------------------------------------------------------------------
                | Category Filter
                |--------------------------------------------------------------------------
                */

                ->when($categoryID, function ($query) use ($categoryID) {

                    return $query->whereHas('categories', function ($q) use ($categoryID) {

                        $q->where('categories.id', $categoryID);
                    });
                })

                /*
                |--------------------------------------------------------------------------
                | Sub Category Filter
                |--------------------------------------------------------------------------
                */

                ->when($subCategoryID, function ($query) use ($subCategoryID) {

                    return $query->whereHas('subcategories', function ($q) use ($subCategoryID) {

                        $q->where('sub_categories.id', $subCategoryID);
                    });
                })

                /*
                |--------------------------------------------------------------------------
                | Rating Filter
                |--------------------------------------------------------------------------
                */

                ->when($rating, function ($query) use ($rating) {

                    $ratingValue = (float) $rating;
                    $upperBound = $ratingValue + 1;

                    return $query->havingRaw(
                        'average_rating >= ? AND average_rating < ?',
                        [$ratingValue, $upperBound]
                    );
                })

                /*
                |--------------------------------------------------------------------------
                | Price Filter
                |--------------------------------------------------------------------------
                */

                ->when(
                    $minPrice !== null || $maxPrice !== null,
                    function ($query) use ($minPrice, $maxPrice) {

                        return $query->whereBetween(
                            DB::raw('IF(discount_price > 0, discount_price, price)'),
                            [
                                $minPrice ?? 0,
                                $maxPrice ?? 99999999
                            ]
                        );
                    }
                );

            /*
            |--------------------------------------------------------------------------
            | Sorting
            |--------------------------------------------------------------------------
            */

            switch ($sortType) {

                case 'top_selling':

                    $products->orderByDesc('orders_count');

                    break;

                case 'popular_product':

                    $products->orderByDesc('orders_count')
                        ->orderByDesc('average_rating');

                    break;

                case 'high_to_low':

                    $products->orderByRaw(
                        'IF(discount_price > 0, discount_price, price) DESC'
                    );

                    break;

                case 'low_to_high':

                    $products->orderByRaw(
                        'IF(discount_price > 0, discount_price, price) ASC'
                    );

                    break;

                case 'newest':

                case 'just_for_you':

                default:

                    $products->latest('id');

                    break;
            }

            /*
            |--------------------------------------------------------------------------
            | Pagination
            |--------------------------------------------------------------------------
            */

            $products = $products->paginate(
                $perPage,
                ['*'],
                'page',
                $page
            );

            /*
            |--------------------------------------------------------------------------
            | Response
            |--------------------------------------------------------------------------
            */

            return [

                'total' => $products->total(),

                'current_page' => $products->currentPage(),

                'last_page' => $products->lastPage(),

                'per_page' => $products->perPage(),

                'products' => ProductResource::collection($products),

                'filters' => [

                    'sizes' => $sizes
                        ? SizeResource::collection($sizes)
                        : [],

                    'colors' => $colors
                        ? ColorResource::collection($colors)
                        : [],

                    'brands' => $brands
                        ? BrandResource::collection($brands)
                        : [],

                    'min_price' => (int) $productMinPrice,

                    'max_price' => (int) $productMaxPrice,
                ]
            ];
        });

        return $this->json('products', $response);
    }

    
    

    /**
     * Show the product details.
     *
     * @param  datatype  $id  description
     * @return response
     */
    public function show(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $product = ProductRepository::find($request->product_id);
        ProductRepository::recentView($product);

        $relatedProducts = ProductRepository::query()->whereHas('categories', function ($query) use ($product) {
            $query->whereIn('categories.id', $product->categories->pluck('id'));
        })->where('id', '!=', $product->id)
            ->isActive()
            ->where(fn ($q) => $q->where('is_digital', true)->orWhere('quantity', '>', 0))
            ->inRandomOrder()
            ->limit(6)->get();

        $shop = $product->shop;

        $popularProducts = $shop->products()->isActive()->where('id', '!=', $product->id)->withCount('orders')->withAvg('reviews as average_rating', 'rating')->orderByDesc('average_rating')->orderByDesc('orders_count')->take(6)->get();

        return $this->json('product details', [
            'product' => ProductDetailsResource::make($product),
            'related_products' => ProductResource::collection($relatedProducts),
            'popular_products' => ProductResource::collection($popularProducts),
        ]);
    }

    /**
     * Add or remove favorite product for the user.
     *
     * @param  AddFavoriteRequest  $request  The request for adding a favorite.
     * @return json Response with favorite updated successfully
     */
    public function addFavorite(AddFavoriteRequest $request)
    {
        $product = ProductRepository::find($request->product_id);
        $customer = auth()->user()?->customer;

        if (! $customer) {
            return $this->json('Unauthorized', [], 401);
        }

        $wasFavorite = $customer->favorites()->where('product_id', $product->id)->exists();
        $customer->favorites()->toggle($product->id);

        if ($wasFavorite) {
            if ($product->favourite_count > 0) {
                $product->decrement('favourite_count');
            }
        } else {
            $product->increment('favourite_count');
        }

        $product->refresh();
        $product->loadCount('favorites');
        $product->update(['favourite_count' => $product->favorites_count]);

        return $this->json('favorite updated successfully', [
            'product' => ProductResource::make($product),
        ]);
    }

    /**
     * get list of favorite products.
     *
     * @return json Response
     */
    public function favoriteProducts()
    {
 $products = auth()->user()->customer->favorites()
            ->orderByPivot('updated_at', 'desc')
            ->orderByPivot('created_at', 'desc')
            ->orderByPivot('id', 'desc')
            ->get();
        return $this->json('favorite products', [
            'products' => ProductResource::collection($products),
        ]);
    }

    /**
     * Store a new review.
     *
     * @param  ReviewRequest  $request  The review request
     * @return json Response
     */
    public function storeReview(ReviewRequest $request)
    {
        $product = ProductRepository::find($request->product_id);

        $hasReview = $product->reviews()->where('customer_id', auth()->user()->customer->id)->where('order_id', $request->order_id)->first();

        if ($hasReview) {
            return $this->json('review already exists', [
                'review' => ReviewResource::make($hasReview),
            ]);
        }

        $review = ReviewRepository::storeByRequest($request, $product);

        return $this->json('review added successfully', [
            'review' => ReviewResource::make($review),
        ]);
    }
    /**
     * Update favorite count.
     *
     * @param  Request  $request
     * @return json Response
     */
    public function updateFavoriteCount(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'action' => 'required|boolean', // 1: increase, 0: decrease
        ]);

        $product = ProductRepository::find($request->product_id);

        if ($request->action) {
            $product->increment('favourite_count');
        } else {
            if ($product->favourite_count > 0) {
                $product->decrement('favourite_count');
            }
        }

        return $this->json('Favorite count updated successfully', [
            'favourite_count' => $product->refresh()->favourite_count,
            'action' => $request->action ? 'increment' : 'decrement'
        ]);
    }

    /**
     * Increment share count.
     *
     * @param  Request  $request
     * @return json Response
     */
    public function incrementShareCount(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $product = ProductRepository::find($request->product_id);
        $product->increment('share_count');

        return $this->json('Share count updated successfully', [
            'share_count' => $product->refresh()->share_count
        ]);
    }
}
