<?php

namespace Igniter\Coupons;

use Igniter\Admin\Models\Order;
use Igniter\Cart\Classes\CartManager;
use Igniter\Cart\Facades\Cart;
use Igniter\Coupons\Models\Coupon;
use Igniter\Coupons\Models\CouponHistory;
use Igniter\Local\Facades\Location;
use Igniter\Main\Models\Customer;
use Igniter\System\Classes\BaseExtension;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Event;

class Extension extends BaseExtension
{
    public function boot()
    {
        Order::extend(function ($model) {
            $model->relation['hasMany']['coupon_history'] = [\Igniter\Coupons\Models\CouponHistory::class, 'delete' => true];
            $model->implement[] = 'Igniter.Coupons.Actions.RedeemsCoupon';
        });

        Event::listen('cart.added', function ($order) {
            Coupon::isEnabled()->isAutoApplicable()
                ->each(function ($coupon) {
                    $orderDateTime = Location::orderDateTime();
                    if ($coupon->isExpired($orderDateTime)) {
                        return;
                    }

                    $cartManager = resolve(CartManager::class);
                    $cartManager->applyCouponCondition($coupon->code);
                });
        });

        Event::listen('admin.order.paymentProcessed', function ($order) {
            if ($couponCondition = Cart::conditions()->get('coupon')) {
                $order->redeemCoupon($couponCondition);
            }
        });

        Customer::created(function ($customer) {
            Order::where('email', $customer->email)
                ->get()
                ->each(function ($order) use ($customer) {
                    CouponHistory::where('order_id', $order->order_id)
                        ->update(['customer_id' => $customer->customer_id]);
                });
        });

        Relation::morphMap([
            'coupon_history' => \Igniter\Coupons\Models\CouponHistory::class,
            'coupons' => \Igniter\Coupons\Models\Coupon::class,
        ]);
    }

    public function registerApiResources()
    {
        return [
            'coupons' => [
                'controller' => \Igniter\Coupons\ApiResources\Coupons::class,
                'name' => 'Coupons',
                'description' => 'An API resource for coupons',
                'actions' => [
                    'index:all', 'show:all', 'store:admin', 'update:admin', 'destroy:admin',
                ],
            ],
        ];
    }

    public function registerCartConditions()
    {
        return [
            \Igniter\Coupons\CartConditions\Coupon::class => [
                'name' => 'coupon',
                'label' => 'lang:igniter.coupons::default.text_coupon',
                'description' => 'lang:igniter.coupons::default.help_coupon_condition',
            ],
        ];
    }

    public function registerPermissions()
    {
        return [
            'Admin.Coupons' => [
                'label' => 'igniter.coupons::default.permissions',
                'group' => 'admin::lang.permissions.name',
            ],
        ];
    }

    public function registerNavigation()
    {
        return [
            'marketing' => [
                'child' => [
                    'coupons' => [
                        'priority' => 10,
                        'class' => 'coupons',
                        'href' => admin_url('igniter/coupons/coupons'),
                        'title' => lang('igniter.coupons::default.side_menu'),
                        'permission' => 'Admin.Coupons',
                    ],
                ],
            ],
        ];
    }
}
