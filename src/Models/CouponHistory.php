<?php

namespace Igniter\Coupons\Models;

use Igniter\Flame\Database\Model;
use Igniter\User\Models\Concerns\HasCustomer;
use Illuminate\Support\Facades\Event;

/**
 * Coupons History Model Class
 */
class CouponHistory extends Model
{
    use HasCustomer;

    /**
     * @var string The database table name
     */
    protected $table = 'igniter_coupons_history';

    /**
     * @var string The database table primary key
     */
    protected $primaryKey = 'coupon_history_id';

    public $timestamps = true;

    protected $guarded = [];

    protected $appends = ['customer_name'];

    protected $casts = [
        'coupon_history_id' => 'integer',
        'coupon_id' => 'integer',
        'order_id' => 'integer',
        'customer_id' => 'integer',
        'min_total' => 'float',
        'amount' => 'float',
        'status' => 'boolean',
    ];

    public $relation = [
        'belongsTo' => [
            'customer' => \Igniter\User\Models\Customer::class,
            'order' => \Igniter\Cart\Models\Order::class,
            'coupon' => \Igniter\Coupons\Models\Coupon::class,
        ],
    ];

    protected array $queryModifierFilters = [
        'redeemed' => ['applyRedeemed', 'default' => true],
        'customer' => 'applyCustomer',
        'order_id' => 'whereOrderId',
    ];

    protected array $queryModifierSorts = [
        'created_at desc' => true, 'created_at asc',
    ];

    public static function redeem($orderId)
    {
        self::query()
            ->where('order_id', $orderId)
            ->get()
            ->each(function($couponHistory) {
                $couponHistory->update([
                    'status' => 1,
                    'created_at' => now(),
                ]);

                Event::fire('admin.order.couponRedeemed', [$couponHistory]);
            });
    }

    public function getCustomerNameAttribute($value)
    {
        return ($this->customer && $this->customer->exists) ? $this->customer->full_name : $value;
    }

    public function scopeApplyRedeemed($query)
    {
        return $query->where('status', '>=', 1);
    }

    public function touchStatus()
    {
        $this->status = ($this->status < 1) ? 1 : 0;

        return $this->save();
    }

    /**
     * @param \Igniter\Cart\CartCondition $couponCondition
     * @param \Igniter\Cart\Models\Order $order
     * @return \Igniter\Coupons\Models\CouponHistory|bool
     */
    public static function createHistory($couponCondition, $order)
    {
        if (!$coupon = $couponCondition->getModel()) {
            return false;
        }

        $model = new static;
        $model->order_id = $order->getKey();
        $model->customer_id = $order->customer ? $order->customer->getKey() : null;
        $model->coupon_id = $coupon->coupon_id;
        $model->code = $coupon->code;
        $model->amount = $couponCondition->getValue();
        $model->min_total = $coupon->min_total;

        if ($model->fireSystemEvent('couponHistory.beforeAddHistory', [$couponCondition, $order->customer, $coupon], true) === false) {
            return false;
        }

        $model->save();

        return $model;
    }
}
