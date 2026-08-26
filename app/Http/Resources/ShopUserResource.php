<?php

namespace App\Http\Resources;

use App\Repositories\ShopUserChatsRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class ShopUserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $blockedByCustomer = (bool) $this->customer_blocked_at;
        $blockedByShop = (bool) $this->seller_blocked_at;

        $lastMessage = ShopUserChatsRepository::query()
            ->where('shop_id', $this->shop?->id)
            ->where('user_id', $this->user?->id)
            ->latest('id')
            ->first();

        $unreadChatUser = ShopUserChatsRepository::query()
            ->where('shop_id', $this->shop?->id)
            ->where('user_id', $this->user?->id)
            ->where('type', 'user')
            ->where('is_seen', false)
            ->count();

        $unreadChatShop = ShopUserChatsRepository::query()
            ->where('shop_id', $this->shop?->id)
            ->where('user_id', $this->user?->id)
            ->where('type', 'shop')
            ->where('is_seen', false)
            ->count();

        $lastMsg = Str::limit(($lastMessage->message ?? $lastMessage?->product?->name), 12, '...');
        $lastTime = optional($lastMessage?->created_at ? Carbon::parse($lastMessage->created_at) : null)?->diffForHumans();

        $shop = $this->shop
            ? array_merge(ShopRefineForChatResource::make($this->shop)->resolve($request), [
                'is_blocked' => $blockedByCustomer,
                'is_blocked_by_shop' => $blockedByShop,
            ])
            : null;

        $user = $this->user
            ? array_merge(UserResource::make($this->user)->resolve($request), [
                'is_blocked' => $blockedByShop,
                'is_blocked_by_customer' => $blockedByCustomer,
            ])
            : null;

        return [
            'id' => $this->id ?? null,
            'shop' => $shop,
            'user' => $user,
            'product' => ChatProductResource::make($this->product ?? null),
            'last_message' => $lastMsg ?? null,
            'last_message_time' => $lastTime,
            'is_read' => (bool) ($lastMessage?->is_seen ?? false),
            'unread_message_user' => $unreadChatUser,
            'unread_message_shop' => $unreadChatShop,
        ];
    }
}
