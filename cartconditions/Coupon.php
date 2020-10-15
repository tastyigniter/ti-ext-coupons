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
    protected $couponModel;

    protected static $isItemable;

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
        $couponCode = $this->getMetaData('code');

        if (is_null($this->couponModel) AND strlen($couponCode))
            $this->couponModel = Coupons_model::getByCode($couponCode);

        if (!$this->couponModel OR strtolower($this->couponModel->code) !== strtolower($couponCode))
            $this->couponModel = null;

        return $this->couponModel;
    }

    public function beforeApply()
    {
        if (!strlen($couponCode = $this->getMetaData('code')))
            return FALSE;

        if (self::$isItemable)
            return FALSE;

        try {
            if (!$couponModel = $this->getModel())
                throw new ApplicationException(lang('igniter.cart::default.alert_coupon_invalid'));

            $this->validateCoupon($couponModel);
        }
        catch (Exception $ex) {
            flash()->alert($ex->getMessage())->now();
            $this->removeMetaData('code');

            return FALSE;
        }
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

    public function getApplicableItems()
    {
        if (!$couponModel = $this->getModel())
            return [];

        $items = $couponModel->menus->pluck('menu_id');
        $couponModel->categories->pluck('category_id')
            ->each(function ($category) use ($items) {
                $items = $items->merge(Menus_model::whereHasCategory($category)->pluck('menu_id'));
            });

        // using this so that the condition is not applied on the cart subtotal
        // feel free to change the approach or maybe a better name?
        self::$isItemable = $items->isNotEmpty();

        return $items->all();
    }
}
