<?php

namespace App\Http\Controllers\Concerns;

use App\Models\ShopUser;
use App\Repositories\ShopUserRepository;
use Illuminate\Http\JsonResponse;

trait ManagesShopUserBlocks
{
    protected function findShopUser(int $shopId, int $userId): ?ShopUser
    {
        return ShopUserRepository::query()
            ->where('shop_id', $shopId)
            ->where('user_id', $userId)
            ->latest('id')
            ->first();
    }

    protected function customerCannotAccessChat(ShopUser $shopUser): ?JsonResponse
    {
        if ($shopUser->customer_blocked_at) {
            return $this->json('you have blocked this seller', [], 403);
        }

        if ($shopUser->seller_blocked_at) {
            return $this->json('seller has blocked you', [], 403);
        }

        return null;
    }

    protected function sellerCannotAccessChat(ShopUser $shopUser): ?JsonResponse
    {
        if ($shopUser->seller_blocked_at) {
            return $this->json('you have blocked this customer', [], 403);
        }

        if ($shopUser->customer_blocked_at) {
            return $this->json('customer has blocked you', [], 403);
        }

        return null;
    }
}
