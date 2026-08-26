<?php

namespace App\Http\Controllers\API\Seller;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrderIdRequest;
use App\Http\Requests\StatusUpdateRequest;
use App\Http\Resources\SellerOrderResource;
use App\Repositories\NotificationRepository;
use App\Repositories\OrderRepository;
use App\Services\NotificationServices;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->page ?? 1;
        $perPage = $request->per_page ?? 15;
        $skip = ($page * $perPage) - $perPage;

        $search = $request->search;

        $string = $search;

        // remove # and 2 letters from search
        if (preg_match('/\d/', $string) && ! preg_match('/\s/', $string) && strpos($string, '#') !== false) {
            $search = substr($string, 3);
        }

        $startDate = $request->start_date ? Carbon::parse($request->start_date)->format('Y-m-d') : null;
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->format('Y-m-d') : null;

        $filterType = $request->filter_type ?? null;

        $orderStatus = $request->order_status ?? null;
        $shop = generaleSetting('shop');

        $resolvedStatus = $this->resolveSellerOrderStatusFilter($orderStatus);

        $orders = $shop->orders()->when($search, function ($query) use ($search) {
            return $query->where('order_code', 'like', "%$search%")->orWhereHas('customer', function ($query) use ($search) {
                $query->whereHas('user', function ($query) use ($search) {
                    return $query->where('name', 'like', "%$search%")->orWhere('email', 'like', "%$search%")->orWhere('phone', 'like', "%$search%");
                });
            });
        })->when($startDate, function ($query) use ($startDate, $endDate) {
            return $query->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate])->orWhereBetween('updated_at', [$startDate, $endDate]);
            });
        })->when($filterType == 'today', function ($query) {
            return $query->where(function ($query) {
                $query->whereDate('created_at', Carbon::today())->orWhereDate('updated_at', Carbon::today());
            });
        })->when($filterType == 'this_week', function ($query) {
            return $query->where(function ($query) {
                return $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->orWhereBetween('updated_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
            });
        })->when($filterType == 'this_month', function ($query) {
            return $query->where(function ($query) {
                $query->whereMonth('created_at', Carbon::now()->month)->orWhereMonth('updated_at', Carbon::now()->month);
            });
        })->when($filterType == 'this_year', function ($query) {
            return $query->where(function ($query) {
                $query->whereYear('created_at', Carbon::now()->year)->orWhereYear('updated_at', Carbon::now()->year);
            });
        })->when($filterType == 'last_week', function ($query) {
            return $query->where(function ($query) {
                $query->whereBetween('created_at', [Carbon::now()->subWeek(), Carbon::now()->subWeek(1)])->orWhereBetween('updated_at', [Carbon::now()->subWeek(), Carbon::now()->subWeek(1)]);
            });
        })->when($filterType == 'last_month', function ($query) {
            return $query->where(function ($query) {
                $query->whereMonth('created_at', Carbon::now()->subMonth()->month)->orWhereMonth('updated_at', Carbon::now()->subMonth()->month);
            });
        })->when($filterType == 'last_year', function ($query) {
            return $query->where(function ($query) {
                $query->whereYear('created_at', Carbon::now()->subYear()->year)->orWhereYear('updated_at', Carbon::now()->subYear()->year);
            });
        })->when($resolvedStatus['type'] !== 'none', function ($query) use ($resolvedStatus) {
            if ($resolvedStatus['type'] === 'exact') {
                return $query->where('order_status', $resolvedStatus['value']);
            }
            if ($resolvedStatus['type'] === 'to_pickup') {
                return $query->whereHas('driverOrder')->where(function ($q) {
                    $q->where('order_status', OrderStatus::CONFIRM->value)
                        ->orWhere('order_status', OrderStatus::PROCESSING->value);
                });
            }
            if ($resolvedStatus['type'] === 'to_delivery') {
                return $query->where(function ($q) {
                    $q->where('order_status', OrderStatus::ON_THE_WAY->value)
                        ->orWhere('order_status', OrderStatus::PICKUP->value);
                });
            }

            return $query;
        });

        $total = $orders->count();

        $allOrderLists = $orders->latest('id')->skip($skip)->take($perPage)->get();

        $countsByStatus = $shop->orders()
            ->selectRaw('order_status, COUNT(*) as c')
            ->groupBy('order_status')
            ->pluck('c', 'order_status');

        $totalOrders = $shop->orders()->count();

        $statusArray = [
            (object) [
                'name' => 'All',
                'value' => $totalOrders,
                'status' => 'all',
            ],
        ];

        foreach (OrderStatus::cases() as $case) {
            $statusArray[] = (object) [
                'name' => $case->value,
                'value' => (int) ($countsByStatus[$case->value] ?? 0),
                'status' => $this->orderStatusEnumToSlug($case),
            ];
        }

        return $this->json('all order list', [
            'total_items' => $total,
            'status_orders' => $statusArray,
            'orders' => SellerOrderResource::collection($allOrderLists),
        ]);
    }

    // show order details
    public function show(OrderIdRequest $request)
    {
        $order = OrderRepository::find($request->order_id);

        return $this->json('Order details', [
            'order' => SellerOrderResource::make($order),
        ]);
    }

    // status update
    public function update(StatusUpdateRequest $request)
    {
        $order = OrderRepository::find($request->order_id);

        if (! $order) {
            return $this->json('Sorry, this order is not found', [], 422);
        }

        $status = strtolower((string) $request->order_status);
        $status = str_replace(' ', '_', $status);

        $orderStatus = match ($status) {
            'pending' => OrderStatus::PENDING->value,
            'confirm' => OrderStatus::CONFIRM->value,
            'processing' => OrderStatus::PROCESSING->value,
            'pickup' => OrderStatus::PICKUP->value,
            'on_the_way' => OrderStatus::ON_THE_WAY->value,
            'delivered' => OrderStatus::DELIVERED->value,
            'cancelled', 'cancel' => OrderStatus::CANCELLED->value, // handling both cancel and cancelled
            default => OrderStatus::CONFIRM->value,
        };

        $order->update([
            'order_status' => $orderStatus,
        ]);

        $title = 'Order status updated';
        $message = 'Your order status updated to '.$orderStatus.' order code: '.$order->prefix.$order->order_code;
        $deviceKeys = $order->customer->user->devices->pluck('key')->toArray();

        try {
            NotificationServices::sendNotification($message, $deviceKeys, $title);
        } catch (\Throwable $th) {
        }

        $notify = (object) [
            'title' => $title,
            'content' => $message,
            'user_id' => $order->customer->user_id,
            'type' => 'order',
        ];
        NotificationRepository::storeByRequest($notify);

        $order->refresh();

        // OrderMailEvent::dispatch($order);

        return $this->json('Order status updated successfully!', [
            'order' => SellerOrderResource::make($order),
        ]);
    }

    /**
     * Map query ?order_status= slug to filter type. Matches web shop list: one DB enum value per tab.
     * Keeps legacy slugs to_pickup / to_delivery for older clients.
     *
     * @return array{type: 'none'|'exact'|'to_pickup'|'to_delivery', value?: string}
     */
    private function resolveSellerOrderStatusFilter(?string $raw): array
    {
        $raw = $raw !== null ? strtolower(trim((string) $raw)) : '';

        if ($raw === '' || $raw === 'all') {
            return ['type' => 'none'];
        }

        $slugToEnum = [
            'pending' => OrderStatus::PENDING->value,
            'confirm' => OrderStatus::CONFIRM->value,
            'processing' => OrderStatus::PROCESSING->value,
            'pickup' => OrderStatus::PICKUP->value,
            'on_the_way' => OrderStatus::ON_THE_WAY->value,
            'delivered' => OrderStatus::DELIVERED->value,
            'cancelled' => OrderStatus::CANCELLED->value,
            'cancel' => OrderStatus::CANCELLED->value,
        ];

        if (isset($slugToEnum[$raw])) {
            return ['type' => 'exact', 'value' => $slugToEnum[$raw]];
        }

        if ($raw === 'to_pickup') {
            return ['type' => 'to_pickup'];
        }

        if ($raw === 'to_delivery') {
            return ['type' => 'to_delivery'];
        }

        return ['type' => 'none'];
    }

    private function orderStatusEnumToSlug(OrderStatus $status): string
    {
        return str_replace(' ', '_', strtolower($status->value));
    }
}
