<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ShopResource;
use App\Http\Resources\UserResource;
use App\Models\Shop;
use App\Repositories\ShopRepository;
use Illuminate\Http\Request;

class ShopFollowController extends Controller
{
    public function followToggle(Request $request)
    {
        $request->validate(['shop_id' => 'required|exists:shops,id']);

        $shop = ShopRepository::find($request->shop_id);
        $user = auth()->user();

        if ($user->followingShops()->where('shop_id', $shop->id)->exists()) {
            $user->followingShops()->detach($shop->id);
            $message = 'Shop unfollowed successfully';
        } else {
            $user->followingShops()->attach($shop->id, ['is_liked' => false]);
            $message = 'Shop followed successfully';
        }

        return $this->json($message, $this->shopFollowResponse($shop, $user));
    }

    public function likeDislike(Request $request)
    {
        $request->validate(['shop_id' => 'required|exists:shops,id']);

        $shop = ShopRepository::find($request->shop_id);
        $user = auth()->user();
        $pivot = $user->followingShops()->where('shop_id', $shop->id)->first();
        $isLiked = ! ($pivot && (bool) $pivot->pivot->is_liked);

        $user->followingShops()->syncWithoutDetaching([
            $shop->id => ['is_liked' => $isLiked],
        ]);

        return $this->json(
            $isLiked ? 'Shop liked successfully' : 'Shop unliked successfully',
            $this->shopFollowResponse($shop, $user)
        );
    }

    public function isFollowing(Request $request)
    {
        $request->validate(['shop_id' => 'required|exists:shops,id']);

        return $this->json('Follow status', $this->shopFollowResponse(
            ShopRepository::find($request->shop_id),
            auth()->user()
        ));
    }

    public function followers(Request $request)
    {
        $request->validate(['shop_id' => 'required|exists:shops,id']);

        $page = (int) ($request->page ?? 1);
        $perPage = (int) ($request->per_page ?? 15);
        $skip = max(0, ($page * $perPage) - $perPage);
        $shop = ShopRepository::find($request->shop_id);
        $query = $shop->followers()->wherePivot('is_liked', true);

        return $this->json('Shop followers', [
            'total' => $query->count(),
            'likes_count' => $shop->likesCount(),
            'followers' => UserResource::collection($query->skip($skip)->take($perPage)->get()),
        ]);
    }

    public function followingShops(Request $request)
    {
        $page = (int) ($request->page ?? 1);
        $perPage = (int) ($request->per_page ?? 15);
        $skip = max(0, ($page * $perPage) - $perPage);
        $user = auth()->user();
        $query = $user->followingShops()->isActive();
        $total = $query->count();
        $shops = $query->skip($skip)->take($perPage)->get();

        $shops->each(fn (Shop $shop) => $shop->setAttribute('follow_state', $shop->followStateFor($user)));

        return $this->json('Following shops', [
            'total' => $total,
            'shops' => ShopResource::collection($shops),
        ]);
    }

    private function shopFollowResponse(Shop $shop, $user): array
    {
        return array_merge($shop->followStateFor($user), [
            'likes_count' => $shop->likesCount(),
            'shop' => collect(ShopResource::make($shop)->resolve())
                ->except(['is_following', 'is_liked', 'likes_count'])
                ->all(),
        ]);
    }
}
