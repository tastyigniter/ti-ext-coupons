<?php

namespace Igniter\Coupons\Actions;

use Igniter\Cart\CartCondition;
use Igniter\Cart\Models\Order;
use Igniter\Coupons\Models\CouponHistory;
use Igniter\Flame\Traits\ExtensionTrait;
use Igniter\System\Actions\ModelAction;

class RedeemsCoupon extends ModelAction
{
    use ExtensionTrait;

    /**
     * Redeem coupon by order
     */
    public function redeemCoupon()
    {
        if (!$this->model->getOrderTotals()->keyBy('code')->get('coupon')) {
            return;
        }

        CouponHistory::redeem($this->model->order_id);
    }

    /**
     * Add cart coupon to order by order_id
     *
     * @param \Igniter\Cart\Models\Order $order
     * @param \Igniter\Cart\CartCondition $couponCondition
     * @param \Igniter\User\Models\Customer $customer
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

        // Make sure order model exists
        if (!$this->model->exists) {
            return false;
        }

        /** @var Order $this */
        return CouponHistory::createHistory($couponCondition, $this->model);
    }
}
