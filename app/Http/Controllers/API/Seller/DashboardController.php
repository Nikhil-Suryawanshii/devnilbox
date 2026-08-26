<?php

namespace App\Http\Controllers\API\Seller;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\WithdrawResource;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $filterType = request()->filter_type ?? 'this_year';

        $shop = generaleSetting('shop');
        $orderObject = $shop->orders();

    
        $totalSales = (clone $orderObject)->where(function ($query) {
            $query->where('order_status', OrderStatus::DELIVERED->value)->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
        })->sum('total_amount');
        
       // $totalOrders = (clone $orderObject)->count();
        
        $todayOrders = (clone $orderObject)->whereDate('created_at', Carbon::today())->count();

        $pendingOrder = (clone $orderObject)->where('order_status', OrderStatus::PENDING->value)->count();
        
       // $cancelledOrder = (clone $orderObject)->where('order_status', OrderStatus::CANCELLED->value)->count();

        $toPickupOrders = (clone $orderObject)->where(function ($query) {
            $query->whereHas('driverOrder')->where('order_status', OrderStatus::CONFIRM->value)->orWhere('order_status', OrderStatus::PROCESSING->value)->orWhere('order_status', OrderStatus::PICKUP->value);
        })->count();

        $toDeliveryOrders = (clone $orderObject)->where('order_status', OrderStatus::ON_THE_WAY->value)->count();

        $pendingWithdraw = $shop->withdraws()->where(function ($query) {
            $query->where('status', 'pending');
        })->sum('amount');

        $walletBalance = auth()->user()->wallet->balance - $pendingWithdraw;
        $walletBalance = $walletBalance > 0 ? $walletBalance : 0;

        $latestPendingWithdraw = $shop->withdraws()->where(function ($query) {
            $query->where('status', 'pending');
        })->latest('id')->first();

        if ($filterType === 'last_year') {
            $startDate = now()->subYear()->startOfYear();
            $endDate = now()->subYear()->endOfYear();
        } else {
            $startDate = now()->startOfYear();
            $endDate = now()->endOfYear();
        }

        // Get monthly sale chart
        $monthList = [];
        $valueList = [];

        for ($i = 1; $i <= 12; $i++) {
            $month = Carbon::create(null, $i, 1);

            $totalAmount = (clone $orderObject)->where(function ($query) use ($month, $startDate, $endDate) {
                $query->where('order_status', OrderStatus::DELIVERED->value)->whereBetween('created_at', [$month->startOfMonth()->format('Y-m-d'), $month->endOfMonth()->format('Y-m-d')])->whereBetween('created_at', [$startDate, $endDate]);
            })->sum('total_amount');

            $monthList[] = $month->format('M');
            $valueList[] = (float) $totalAmount;
        }

        $maxAmount = max($valueList);
        $minAmount = min($valueList);
        
        $statusQuery = $shop->orders();

        $statusQuery->when($filterType == 'today', function ($query) {
            $query->whereDate('created_at', Carbon::today());
        })->when($filterType == 'this_week', function ($query) {
            $query->whereBetween('created_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ]);
        })->when($filterType == 'this_month', function ($query) {
            $query->whereMonth('created_at', Carbon::now()->month)
                  ->whereYear('created_at', Carbon::now()->year);
        })->when($filterType == 'this_year', function ($query) {
            $query->whereYear('created_at', Carbon::now()->year);
        })->when($filterType == 'last_week', function ($query) {
            $query->whereBetween('created_at', [
                Carbon::now()->subWeek()->startOfWeek(),
                Carbon::now()->subWeek()->endOfWeek()
            ]);
        })->when($filterType == 'last_month', function ($query) {
            $query->whereMonth('created_at', Carbon::now()->subMonth()->month)
                  ->whereYear('created_at', Carbon::now()->subMonth()->year);
        })->when($filterType == 'last_year', function ($query) {
            $query->whereYear('created_at', Carbon::now()->subYear()->year);
        });
        
        $countsByStatus = (clone $statusQuery)
            ->selectRaw('order_status, COUNT(*) as total')
            ->groupBy('order_status')
            ->pluck('total', 'order_status');
        
        $totalOrders = (clone $statusQuery)->count();
            
        $pendingOrder = (int) ($countsByStatus[OrderStatus::PENDING->value] ?? 0);

        $confirmOrder = (int) ($countsByStatus[OrderStatus::CONFIRM->value] ?? 0);
        
        $processingOrder = (int) ($countsByStatus[OrderStatus::PROCESSING->value] ?? 0);
        
        $pickupOrder = (int) ($countsByStatus[OrderStatus::PICKUP->value] ?? 0);
        
        $onTheWayOrder = (int) ($countsByStatus[OrderStatus::ON_THE_WAY->value] ?? 0);
        
        $deliveredOrder = (int) ($countsByStatus[OrderStatus::DELIVERED->value] ?? 0);
        
        $cancelledOrder = (int) ($countsByStatus[OrderStatus::CANCELLED->value] ?? 0);    

        return $this->json('Seller dashboard data', [
            'order_status_counts' => [
                    'all' => $totalOrders,
                    'pending' => $pendingOrder,
                    'confirm' => $confirmOrder,
                    'processing' => $processingOrder,
                    'pickup' => $pickupOrder,
                    'on_the_way' => $onTheWayOrder,
                    'delivered' => $deliveredOrder,
                    'cancelled' => $cancelledOrder,
                ],
            //'total_order' => $totalOrders,
            'pending_order' => $pendingOrder,
           // 'cancelled_Order' => $cancelledOrder,
            'to_pickup_order' => $toPickupOrders,
            'today_order' => $todayOrders,
            'to_delivery_order' => $toDeliveryOrders,
            'this_manth_sales' => number_format($totalSales, 2, '.', ','),
            'wallet_balance' => number_format($walletBalance, 2, '.', ','),
            'pending_withdraw' => $latestPendingWithdraw ? WithdrawResource::make($latestPendingWithdraw) : null,
            'max_chart_amount' => (float) number_format($maxAmount, 2, '.', ''),
            'min_chart_amount' => (float) number_format($minAmount, 2, '.', ''),
            'sales_chart_months' => $monthList,
            'sales_chart_values' => $valueList,
        ]);
    }
}
