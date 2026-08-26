<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Support\Str;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Scopes\PosOrderFalse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Order extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'order_status' => OrderStatus::class,
        'payment_method' => PaymentMethod::class,
        'payment_status' => PaymentStatus::class,
    ];

    /**
     * Get all of the products for the Order.
     */
    public function products(): BelongsToMany
    {
        $pivotColumns = ['quantity', 'color', 'unit', 'size', 'price'];

        if (Schema::hasColumn('order_products', 'sku')) {
            $pivotColumns[] = 'sku';
        }
        return $this->belongsToMany(Product::class, 'order_products')->withPivot($pivotColumns)->withoutGlobalScopes();
    }

    /**
     * Get all of the vat taxes for the Order.
     */
    public function vatTaxes(): HasMany
    {
        return $this->hasMany(OrderVatTax::class, 'order_id');
    }

    /**
     * Get the customer that owns the Order.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Get the shop for the Order.
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }

    /**
     * Get the coupon for the Order.
     */
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class, 'coupon_id')->withTrashed();
    }

    /**
     * Get the address for the Order.
     */
    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'address_id');
    }

    /**
     * Get the payments for the Order.
     */
    public function payments(): BelongsToMany
    {
        return $this->belongsToMany(Payment::class, 'order_payments');
    }

    /**
     * Get the driver order for the Order.
     */
    public function driverOrder(): BelongsTo
    {
        return $this->belongsTo(DriverOrder::class, 'id', 'order_id');
    }

    /**
     * apply global scope
     */
    protected static function booted()
    {
        static::addGlobalScope(new PosOrderFalse);
    }

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($model) {
            if (
                $model->isDirty('order_status')
                && $model->order_status === OrderStatus::CONFIRM
                && blank($model->tracking_id)
            ) {
                $model->tracking_id = self::generateUniqueTrackingId($model);
            }
        });

        static::created(function ($model) {
            self::clearOrderCache();
            $model->histories()->create([
                'status' => $model->order_status->value,
            ]);
        });

        static::updated(function ($model) {
            self::clearOrderCache();
            if ($model->isDirty('order_status')) {
                $model->histories()->create([
                    'status' => $model->order_status->value,
                ]);
            }
        });

        static::deleted(function () {
            self::clearOrderCache();
        });
    }

    /**
     * Get the histories for the Order.
     */
    public function histories(): HasMany
    {
        return $this->hasMany(OrderHistory::class);
    }

    protected static function clearOrderCache()
    {
        $cacheKeys = [
            'admin_all_orders',
            'shop_all_orders',
        ];

        foreach (OrderStatus::cases() as $status) {
            $cacheKeys[] = 'admin_status_' . Str::camel($status->value);
            $cacheKeys[] = 'shop_status_' . Str::camel($status->value);
        }

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }


    public function productStockOuts(): ?HasMany
    {
        if (function_exists('module_exists') && module_exists('Purchase')) {
            return $this->hasMany(\Modules\Purchase\App\Models\ProductStockOut::class);
        }
        return null;
    }

    private static function generateUniqueTrackingId(self $order): string
    {
        $chars = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';
        $datePart = now()->format('d');
        $orderPart = substr(str_pad((string) $order->order_code, 4, '0', STR_PAD_LEFT), -4);

        do {
            $randomPart = '';
            for ($i = 0; $i < 6; $i++) {
                $randomPart .= $chars[random_int(0, strlen($chars) - 1)];
            }
            $trackingId = $datePart . $orderPart . $randomPart;
        } while (static::withoutGlobalScopes()->where('tracking_id', $trackingId)->exists());

        return $trackingId;
    }
}
