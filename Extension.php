<?php

namespace Igniter\Coupons;

use Admin\Models\Orders_model;
use System\Classes\BaseExtension;

class Extension extends BaseExtension
{
    public function boot()
    {
        Orders_model::extend(function($model) {
            $model->hasMany('Igniter\Coupons\Models\Coupons_history_model');
        });
    }

    public function registerCartConditions()
    {
        return [
            \Igniter\Coupons\CartConditions\Coupon::class => [
                'name' => 'coupon',
                'label' => 'lang:igniter.cart::default.text_coupon',
                'description' => 'lang:igniter.cart::default.help_coupon_condition',
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
                    ]
                ],
            ],
        ];
    } 
}
