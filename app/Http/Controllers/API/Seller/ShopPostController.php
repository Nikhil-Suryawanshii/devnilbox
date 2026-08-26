<?php

namespace App\Http\Controllers\API\Seller;

use App\Http\Controllers\Controller;
use App\Models\ShopPost;
use App\Repositories\MediaRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShopPostController extends Controller
{
    public function index(Request $request)
    {
        $shop = auth()->user()->shop;
        if (!$shop) {
             return response()->json(['message' => 'Shop not found'], 404);
        }

        $posts = ShopPost::where('shop_id', $shop->id)
            ->with('media')
            ->latest()
            ->paginate($request->per_page ?? 10);

        return response()->json([
            'posts' => $posts
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'content' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120', // Extended mimes and size
        ]);

        if (!$request->input('content') && !$request->hasFile('images')) {
            return response()->json(['message' => 'Content or images are required'], 422);
        }

        $shop = auth()->user()->shop;
         if (!$shop) {
             return response()->json(['message' => 'Shop not found'], 404);
        }

        DB::beginTransaction();
        try {
            $post = ShopPost::create([
                'shop_id' => $shop->id,
                'content' => $request->input('content'),
                'is_active' => true,
            ]);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                     $media = MediaRepository::storeByRequest(
                        $image,
                        'shops/posts',
                        'image'
                    );
                    $post->media()->attach($media->id);
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Post created successfully',
                'post' => $post->load('media')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Something went wrong', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
         $shopPost = ShopPost::find($id);
         if (!$shopPost) {
             return response()->json(['message' => 'Post not found'], 404);
         }

         $shop = auth()->user()->shop;
         if ($shopPost->shop_id !== $shop->id) {
             return response()->json(['message' => 'Unauthorized'], 403);
         }

         $shopPost->delete();
         return response()->json(['message' => 'Post deleted successfully']);
    }
}
