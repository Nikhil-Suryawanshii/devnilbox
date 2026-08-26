<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Repositories\CategoryRepository;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Retrieves a paginated list of categories with their associated products.
     *
     * @param  Request  $request  The HTTP request object.
     * @return JsonResponse The JSON response containing the categories and the total count.
     */
    public function index(Request $request)
    {
        $forLanding = $request->boolean('for_landing');
        $page = (int) ($request->page ?? 1);
        $perPage = (int) ($request->per_page ?? ($forLanding ? 500 : 15));
        $skip = ($page * $perPage) - $perPage;

        $categories = CategoryRepository::query()->active();

        if ($forLanding) {
            $categories = $categories->orderBy('display_order', 'asc')->orderBy('name', 'asc');
        } else {
            $shop = generaleSetting('rootShop');

            $categories = $categories
                ->whereHas('shops', function ($query) use ($shop) {
                    $query->where('id', $shop->id);
                })->whereHas('products', function ($query) {
                    $query->whereHas('shop', function ($query) {
                        return $query->isActive();
                    });
                })->orderBy('display_order', 'asc');
        }

        $total = $categories->count();

        $categories = $categories->when($perPage && $page && ! $forLanding, function ($query) use ($perPage, $skip) {
            return $query->skip($skip)->take($perPage);
        })->when($forLanding, function ($query) {
            return $query;
        })->with('subCategories')->get();

        return $this->json('categories', [
            'total' => $total,
            'categories' => CategoryResource::collection($categories),
        ]);
    }
}
