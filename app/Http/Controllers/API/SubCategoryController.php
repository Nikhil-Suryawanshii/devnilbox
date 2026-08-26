<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\SubCategoryResource;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use App\Models\Size;

class SubCategoryController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
        ]);

        $categoryId = $request->category_id;

        $subCategories = SubCategory::whereHas('categories', function ($query) use ($categoryId) {
            return $query->where('category_id', $categoryId);
        })->isActive()->orderBy('display_order', 'asc')->get();

        return $this->json('Sub categories list', [
            'sub_categories' => SubCategoryResource::collection($subCategories),
        ]);
    }
    
     public function getSizesBySubCategory(Request $request)
    {
        $sizes = Size::whereHas(
            'subcategories',
            function($q) use ($request){

                $q->whereIn(
                    'subcategory_id',
                    $request->subcategory_ids
                );

            }
        )->get();

        return response()->json([
            'sizes' => $sizes
        ]);
    }
}
