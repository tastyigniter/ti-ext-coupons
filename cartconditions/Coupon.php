<?php

namespace Igniter\Coupons\CartConditions;

use Admin\Models\Menus_model;
use Exception;
use Igniter\Coupons\Models\Coupons_model;
use Igniter\Flame\Cart\CartCondition;
use Igniter\Flame\Cart\Facades\Cart;
use Igniter\Flame\Cart\Helpers\ActsAsItemable;
use Igniter\Flame\Exception\ApplicationException;
use Igniter\Local\Facades\Location;
use Main\Facades\Auth;

class Coupon extends CartCondition
{
    use ActsAsItemable;

    public $removeable = true;

    public $priority = 200;

    /**
     * @var Coupons_model
     */
    protected static $couponModel;

    protected static $applicableItems;

    public function getLabel()
    {
        return sprintf(lang($this->label), $this->getMetaData('code'));
    }

    public function getValue()
    {
        return 0 - $this->calculatedValue;
    }

    public function getModel()
    {
        if (!strlen($couponCode = $this->getMetaData('code')))
            return null;

        if (is_null(self::$couponModel) || (self::$couponModel && strtolower(self::$couponModel->code) !== strtolower($couponCode)))
            self::$couponModel = Coupons_model::getByCodeAndLocation($couponCode, Location::getId());

        return self::$couponModel;
    }

    public function getApplicableItems($couponModel)
    {
        $applicableItems = $couponModel->menus->pluck('menu_id');
        $couponModel->categories->pluck('category_id')
            ->each(function ($category) use (&$applicableItems) {
                $applicableItems = $applicableItems
                    ->merge(Menus_model::whereHasCategory($category)->pluck('menu_id'));
            });

        self::$applicableItems = $applicableItems;

        return self::$applicableItems;
    }

    public function onLoad()
    {
        if (!strlen($couponCode = $this->getMetaData('code')))
            return;

        try {
            if (!$couponModel = $this->getModel())
                throw new ApplicationException(lang('igniter.cart::default.alert_coupon_invalid'));

            $this->validateCoupon($couponModel);

            $this->getApplicableItems($couponModel);
        }
        catch (Exception $ex) {
            if (!optional($couponModel)->auto_apply)
                flash()->alert($ex->getMessage())->now();

            $this->removeMetaData('code');
        }
    }

    public function beforeApply()
    {
        $couponModel = $this->getModel();
        if (!$couponModel || $couponModel->is_limited_to_cart_item)
            return false;
    }

    public function getActions()
    {
        $value = optional($this->getModel())->discountWithOperand();

        // if we are item limited and not a % we need to apportion
        if (stripos($value, '%') === false && optional($this->getModel())->is_limited_to_cart_item) {
            $value = $this->calculateApportionment($value);
        }

        $actions = [
            'value' => $value,
        ];

        return [$actions];
    }

    public function getRules()
    {
        $minimumOrder = optional($this->getModel())->minimumOrderTotal();

        return ["subtotal > {$minimumOrder}"];
    }

    public function whenInvalid()
    {
        if (!$this->getModel()->auto_apply) {
            $minimumOrder = $this->getModel()->minimumOrderTotal();

            flash()->warning(sprintf(
                lang('igniter.cart::default.alert_coupon_not_applied'),
                currency_format($minimumOrder)
            ))->now();
        }

        $this->removeMetaData('code');
    }

    protected function calculateApportionment($value)
    {
        $applicableItems = self::$applicableItems;
        if ($applicableItems && count($applicableItems)) {
            $applicableItemsTotal = Cart::content()->sum(function ($cartItem) use ($applicableItems) {
                if (!$applicableItems->contains($cartItem->id))
                    return 0;

                return $cartItem->subtotalWithoutConditions();
            });

            $value = ($this->target->subtotalWithoutConditions() / $applicableItemsTotal) * (float)$value;
        }

        return $value;
    }

    protected function validateCoupon($couponModel)
    {
        $user = Auth::getUser();
        $locationId = Location::getId();
        $orderType = Location::orderType();
        $orderDateTime = Location::orderDateTime();

        if ($couponModel->isExpired($orderDateTime))
            throw new ApplicationException(lang('igniter.cart::default.alert_coupon_expired'));

        if ($couponModel->hasRestriction($orderType))
            throw new ApplicationException(sprintf(
                lang('igniter.cart::default.alert_coupon_order_restriction'), $orderType
            ));

        if ($couponModel->hasLocationRestriction($locationId))
            throw new ApplicationException(lang('igniter.cart::default.alert_coupon_location_restricted'));

        if ($couponModel->hasReachedMaxRedemption())
            throw new ApplicationException(lang('igniter.cart::default.alert_coupon_maximum_reached'));

        if (($couponModel->customer_redemptions
            || optional($this->customers)->isNotEmpty()
            || optional($this->customer_groups)->isNotEmpty()) && !$user
        ) throw new ApplicationException(lang('igniter.coupons::default.alert_coupon_login_required'));

        if ($user && $couponModel->customerHasMaxRedemption($user))
            throw new ApplicationException(lang('igniter.cart::default.alert_coupon_maximum_reached'));

        throw_unless($couponModel->customerCanRedeem($user),
            new ApplicationException(lang('igniter.coupons::default.alert_customer_cannot_redeem')));

        throw_unless($couponModel->customerGroupCanRedeem($user->group),
            new ApplicationException(lang('igniter.coupons::default.alert_customer_group_cannot_redeem')));
    }

    public static function isApplicableTo($cartItem)
    {
        if (!$couponModel = self::$couponModel)
            return false;

        if (!$couponModel->is_limited_to_cart_item)
            return false;

        if (!$applicableItems = self::$applicableItems)
            return false;

        return $applicableItems->contains($cartItem->id);
    }
}
