<?php

namespace Igniter\Coupons\Actions;

use Carbon\Carbon;
use Igniter\Cart\CartCondition;
use Igniter\Coupons\Models\CouponHistory;
use Igniter\Flame\Traits\ExtensionTrait;
use Igniter\System\Actions\ModelAction;
use Illuminate\Support\Facades\Event;

class RedeemsCoupon extends ModelAction
{
    use ExtensionTrait;

    /**
     * Redeem coupon by order
     * @throws \Exception
     */
    public function redeemCoupon($couponCondition)
    {
        if (!$couponCondition instanceof CartCondition) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid argument, expected %s, got %s',
                CartCondition::class, get_class($couponCondition)
            ));
        }

        if (!$couponLog = $this->logCouponHistory($couponCondition)) {
            return false;
        }

        $couponLog->status = 1;
        $couponLog->created_at = Carbon::now();
        $couponLog->save();

        Event::fire('admin.order.couponRedeemed', [$couponLog]);
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
        // Make sure order model exists
        if (!$this->model->exists) {
            return false;
        }

        return CouponHistory::createHistory($couponCondition, $this->model);
    }
}
