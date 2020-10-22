<?php

namespace Igniter\Coupons\CartConditions;

use Admin\Models\Menus_model;
use ApplicationException;
use Auth;
use Exception;
use Igniter\Coupons\Models\Coupons_model;
use Igniter\Flame\Cart\CartCondition;
use Igniter\Flame\Cart\Helpers\ActsAsItemable;
use Location;

class Coupon extends CartCondition
{
    use ActsAsItemable;

    public $removeable = TRUE;

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
            return self::$couponModel;

        if (is_null(self::$couponModel))
            self::$couponModel = Coupons_model::getByCode($couponCode);

        if (self::$couponModel AND strtolower(self::$couponModel->code) !== strtolower($couponCode))
            self::$couponModel = Coupons_model::getByCode($couponCode);

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
            flash()->alert($ex->getMessage())->now();
            $this->removeMetaData('code');
        }
    }

    public function beforeApply()
    {
        $couponModel = $this->getModel();
        if (!$couponModel OR $couponModel->is_limited_to_cart_item)
            return FALSE;
    }

    public function getActions()
    {
        return [
            [
                'value' => optional($this->getModel())->discountWithOperand(),
                'calculateValue' => [$this, 'calculateValue'],
            ],
        ];
    }

    public function getRules()
    {
        $minimumOrder = optional($this->getModel())->minimumOrderTotal();

        return ["subtotal > {$minimumOrder}"];
    }

    public function whenInvalid()
    {
        $minimumOrder = $this->getModel()->minimumOrderTotal();
        flash()->warning(sprintf(
            lang('igniter.cart::default.alert_coupon_not_applied'),
            currency_format($minimumOrder)
        ))->now();

        $this->removeMetaData('code');
    }

    protected function validateCoupon($couponModel)
    {
        $user = Auth::getUser();
        $locationId = Location::getId();
        $orderType = Location::orderType();

        if ($couponModel->isExpired())
            throw new ApplicationException(lang('igniter.cart::default.alert_coupon_expired'));

        if ($couponModel->hasRestriction($orderType))
            throw new ApplicationException(sprintf(
                lang('igniter.cart::default.alert_coupon_order_restriction'), $orderType
            ));

        if ($couponModel->hasLocationRestriction($locationId))
            throw new ApplicationException(lang('igniter.cart::default.alert_coupon_location_restricted'));

        if ($couponModel->hasReachedMaxRedemption())
            throw new ApplicationException(lang('igniter.cart::default.alert_coupon_maximum_reached'));

        if ($user AND $couponModel->customerHasMaxRedemption($user))
            throw new ApplicationException(lang('igniter.cart::default.alert_coupon_maximum_reached'));
    }

    public static function isApplicableTo($cartItem)
    {
        if (!$couponModel = self::$couponModel)
            return FALSE;

        if (!$couponModel->is_limited_to_cart_item)
            return FALSE;

        if (!$applicableItems = self::$applicableItems)
            return FALSE;

        return $applicableItems->contains($cartItem->id);
    }
}
