<?php

namespace Igniter\Coupons\Actions;

use Igniter\Coupons\Models\Coupons_history_model;
use Igniter\Flame\Traits\ExtensionTrait;

class RedeemsCoupon
{
    use ExtensionTrait;

    /**
     * Add cart coupon to order by order_id
     *
     * @param \Admin\Models\Orders_model $order
     * @param \Igniter\Flame\Cart\CartCondition $couponCondition
     * @param \Admin\Models\Customers_model $customer
     *
     * @return int|bool
     */
    public function logCouponHistory($couponCondition)
    {
        if (!$couponCondition instanceof CartCondition) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid argument, expected %s, got %s',
                CartCondition::class, get_class($couponCondition)
            ));
        }

        if (!$this->exists)
            return FALSE;

        return Coupons_history_model::createHistory($couponCondition, $this);
    }

    /**
     * Redeem coupon by order_id
     */
    public function redeemCoupon()
    {
        $this
            ->coupon_history()
            ->where('status', '!=', '1')
            ->get()
            ->each(function (Coupons_history_model $model) {
                $model->status = 1;
                $model->date_used = Carbon::now();
                $model->save();

                Event::fire('admin.order.couponRedeemed', [$model]);
            });
    }
}
