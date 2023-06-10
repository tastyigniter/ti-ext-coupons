<?php

namespace Igniter\Coupons\Models;

use Igniter\Flame\Database\Model;

/**
 * Coupons History Model Class
 */
class CouponHistory extends Model
{
    /**
     * @var string The database table name
     */
    protected $table = 'igniter_coupons_history';

    /**
     * @var string The database table primary key
     */
    protected $primaryKey = 'coupon_history_id';

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
            'customer' => \Igniter\Main\Models\Customer::class,
            'order' => \Igniter\Admin\Models\Order::class,
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
     * @param \Igniter\Admin\Models\Order $order
     * @return \Igniter\Admin\Models\CouponHistory|bool
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

        if ($model->fireSystemEvent('couponHistory.beforeAddHistory', [$model, $couponCondition, $order->customer, $coupon], true) === false) {
            return false;
        }

        $model->save();

        return $model;
    }
}
