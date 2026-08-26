<?php

namespace App\Http\Controllers\API;

use App\Events\SendMessageToShop;
use App\Events\SendMessageToUser;
use App\Http\Controllers\Concerns\ManagesShopUserBlocks;
use App\Http\Controllers\Controller;
use App\Http\Resources\ChatResource;
use App\Http\Resources\ShopUserResource;
use App\Repositories\ShopUserChatsRepository;
use App\Repositories\ShopUserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    use ManagesShopUserBlocks;

    public function index()
    {
        $shop = generaleSetting('shop');

        return view('shop.chat.index', compact('shop'));
    }

    public function storeMessage(Request $request)
    {
        $storeShopUser = ShopUserRepository::query()->updateOrCreate(
            [
                'shop_id' => $request->shop_id,
                'user_id' => $request->user_id,
            ],
            [
                'product_id' => $request->product_id ?? null,
            ]
        );

        if ($storeShopUser->product_id) {
            $chat = ShopUserChatsRepository::query()->create([
                'shop_user_id' => $storeShopUser->id,
                'type' => $request->type,
                'message' => $request->message,
                'product_id' => $storeShopUser->product_id,
                'shop_id' => $storeShopUser->shop_id,
                'user_id' => $storeShopUser->user_id,
            ]);

            try {
                SendMessageToShop::dispatch($storeShopUser->shop_id, $storeShopUser->user_id, $chat);
            } catch (\Throwable $th) {
                dd($th);
            }
        }

        return $this->json('success', []);
    }

    public function getShops(Request $request)
    {
        $auth = Auth::guard('api')->user();
        $page = $request->page;
        $perPage = $request->per_page;
        $skip = ($page * $perPage) - $perPage;

        $shops = ShopUserRepository::query()
            ->where('user_id', $auth->id)
            ->whereNull('customer_deleted_at')
            ->with('latestMessage')
            ->when($request->search, function ($query, $search) {
                $query->whereHas('shop', function ($query) use ($search) {
                    $query->where('name', 'LIKE', "%$search%");
                });
            })
            ->when($skip, function ($query) use ($skip) {
                return $query->skip($skip);
            })->when($perPage, function ($query) use ($perPage) {
                return $query->take($perPage);
            })
            ->get()
            ->sortByDesc(function ($shopUser) {
                return optional($shopUser->latestMessage)->created_at;
            })
            ->values();

        return $this->json('success', [
            'total' => $shops->count(),
            'data' => ShopUserResource::collection($shops ?? []),
        ]);
    }

    public function getUsers(Request $request)
    {
        $shop = generaleSetting('shop');

        $users = ShopUserRepository::query()
            ->where('shop_id', $shop->id)
            ->whereNull('customer_deleted_at')
            ->with('latestMessage')
            ->when($request->search, function ($query, $search) {
                $query->whereHas('user', function ($query) use ($search) {
                    $query->where('name', 'LIKE', "%$search%");
                });
            })
            ->get()
            ->sortByDesc(function ($shopUser) {
                return optional($shopUser->latestMessage)->created_at;
            })
            ->values();

        return $this->json('success', [
            'total' => $users->count(),
            'data' => ShopUserResource::collection($users),
        ]);
    }

    public function getMessage(Request $request)
    {
        $auth = Auth::guard('api')->user();

        $page = $request->page;
        $perPage = $request->per_page;
        $skip = ($page * $perPage) - $perPage;

        $shopUser = ShopUserRepository::query()
            ->where('user_id', $auth->id)
            ->where('shop_id', $request->shop_id)
            ->whereNull('customer_deleted_at')
            ->latest('id')->first();

        if (! $shopUser) {
            return $this->json('chat not found', [], 404);
        }

        if ($response = $this->customerCannotAccessChat($shopUser)) {
            return $response;
        }

        $chats = ShopUserChatsRepository::query()
            ->where('user_id', $auth->id)
            ->where('shop_id', $request->shop_id)
                        ->with(['user.media', 'shop.mediaLogo', 'product.media'])
            ->orderBy('id', 'desc')
            ->when($skip, function ($query) use ($skip) {
                return $query->skip($skip);
            })->when($perPage, function ($query) use ($perPage) {
                return $query->take($perPage);
            })
            ->get();

        ShopUserChatsRepository::query()
            ->where('user_id', $auth->id)
            ->where('shop_id', $request->shop_id)
            ->where('type', 'shop')
            ->update(['is_seen' => true]);

        return $this->json('success', [
            'data' => ChatResource::collection($chats),
        ]);
    }

    public function getMessageAdmin(Request $request)
    {
        $shop = generaleSetting('shop');

        $shopUser = $this->findShopUser($shop->id, (int) $request->user_id);

        if (! $shopUser) {
            return $this->json('chat not found', [], 404);
        }

        if ($response = $this->sellerCannotAccessChat($shopUser)) {
            return $response;
        }

        $chats = ShopUserChatsRepository::query()
            ->where('user_id', $request->user_id)
            ->where('shop_id', $shop->id)
            ->get();

        ShopUserChatsRepository::query()
            ->where('user_id', $request->user_id)
            ->where('shop_id', $shop->id)
            ->where('type', 'user')
            ->update(['is_seen' => true]);

        return $this->json('success', [
            'data' => ChatResource::collection($chats),
        ]);
    }

    public function sendMessage(Request $request)
    {
        $auth = Auth::guard('api')->user();

        $shopUser = ShopUserRepository::query()
            ->where('user_id', $auth->id)
            ->where('shop_id', $request->shop_id)
            ->whereNull('customer_deleted_at')
            ->first();

        if (! $shopUser) {
            $blockedChat = ShopUserRepository::query()
                ->where('user_id', $auth->id)
                ->where('shop_id', $request->shop_id)
                ->whereNotNull('seller_blocked_at')
                ->first();

            if ($blockedChat) {
                return $this->json('seller has blocked you', [], 403);
            }

            $shopUser = ShopUserRepository::query()->create([
                'user_id' => $auth->id,
                'shop_id' => $request->shop_id,
            ]);
        }

        if ($response = $this->customerCannotAccessChat($shopUser)) {
            return $response;
        }

        $chat = ShopUserChatsRepository::query()->create([
            'shop_user_id' => $shopUser->id,
            'type' => $request->type,
            'message' => $request->message,
            'shop_id' => $request->shop_id,
            'user_id' => $auth?->id,
        ]);

        try {
            SendMessageToShop::dispatch($request->shop_id, $auth->id, $chat);
        } catch (\Throwable $th) {
            dd($th);
        }

        return $this->json('message sent successfully', ['data' => ChatResource::make($chat)]);
    }

    public function sendMessageAdmin(Request $request)
    {
        $shop = generaleSetting('shop');

        $shopUser = ShopUserRepository::query()
            ->where('user_id', $request->user_id)
            ->where('shop_id', $shop->id)
            ->first();

        if (! $shopUser) {
            return $this->json('your account is not connected with this shop', [], 404);
        }

        if ($response = $this->sellerCannotAccessChat($shopUser)) {
            return $response;
        }

        $chat = ShopUserChatsRepository::query()->create([
            'shop_user_id' => $shopUser->id,
            'type' => $request->type,
            'message' => $request->message,
            'shop_id' => $shop->id,
            'user_id' => $request->user_id,
        ]);

        try {
            SendMessageToUser::dispatch($request->user_id, $chat);
        } catch (\Throwable $th) {
            // dd($th);
        }

        return $this->json('message sent successfully', ['data' => ChatResource::make($chat)]);
    }

    public function unreadMessages(Request $request)
    {
        if ($request->user_id) {
            $chats = ShopUserChatsRepository::query()
                ->where('user_id', $request->user_id)
                ->where('is_seen', false)
                ->where('type', 'shop')
                ->whereHas('shopUser', function ($q) {
                    $q->whereNull('customer_deleted_at')
                        ->whereNull('customer_blocked_at')
                        ->whereNull('seller_blocked_at');
                })
                ->get();
        } else {
            $chats = ShopUserChatsRepository::query()
                ->where('shop_id', $request->shop_id)
                ->where('is_seen', false)
                ->where('type', 'user')
                ->whereHas('shopUser', function ($q) {
                    $q->whereNull('customer_deleted_at')
                        ->whereNull('customer_blocked_at')
                        ->whereNull('seller_blocked_at');
                })
                ->get();
        }

        return $this->json('success', [
            'unread_messages' => $chats->count(),
        ]);
    }

    public function deleteChat(Request $request)
    {
        $auth = Auth::guard('api')->user();

        $request->validate([
            'shop_id' => 'required|exists:shops,id',
        ]);

        $shopUser = $this->findShopUser((int) $request->shop_id, $auth->id);

        if (! $shopUser) {
            return $this->json('chat not found', [], 404);
        }

        $shopUser->update(['customer_deleted_at' => now()]);

        return $this->json('chat deleted successfully', []);
    }

    public function blockSeller(Request $request)
    {
        $auth = Auth::guard('api')->user();
        $request->validate(['shop_id' => 'required|exists:shops,id']);

        ShopUserRepository::query()->updateOrCreate(
            ['user_id' => $auth->id, 'shop_id' => (int) $request->shop_id],
            ['customer_blocked_at' => now()]
        );

        return $this->json('seller blocked successfully', []);
    }

    public function unblockSeller(Request $request)
    {
        $auth = Auth::guard('api')->user();
        $request->validate(['shop_id' => 'required|exists:shops,id']);

        $shopUser = $this->findShopUser((int) $request->shop_id, $auth->id);
        if (! $shopUser) {
            return $this->json('chat not found', [], 404);
        }

        $shopUser->update(['customer_blocked_at' => null]);

        return $this->json('seller unblocked successfully', []);
    }
}

