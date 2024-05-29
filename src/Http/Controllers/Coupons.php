<?php

namespace Igniter\Coupons\Http\Controllers;

use Igniter\Admin\Facades\AdminMenu;

class Coupons extends \Igniter\Admin\Classes\AdminController
{
    public array $implement = [
        \Igniter\Admin\Http\Actions\ListController::class,
        \Igniter\Admin\Http\Actions\FormController::class,
        \Igniter\Local\Http\Actions\LocationAwareController::class,
    ];

    public $locationConfig = [
        'addAbsenceConstraint' => true,
    ];

    public array $listConfig = [
        'list' => [
            'model' => \Igniter\Coupons\Models\Coupon::class,
            'title' => 'igniter.coupons::default.text_title',
            'emptyMessage' => 'igniter.coupons::default.text_empty',
            'defaultSort' => ['coupon_id', 'DESC'],
            'configFile' => 'coupon',
        ],
    ];

    public array $formConfig = [
        'name' => 'igniter.coupons::default.text_form_name',
        'model' => \Igniter\Coupons\Models\Coupon::class,
        'request' => \Igniter\Coupons\Http\Requests\CouponRequest::class,
        'create' => [
            'title' => 'lang:admin::lang.form.create_title',
            'redirect' => 'igniter/coupons/coupons/edit/{coupon_id}',
            'redirectClose' => 'igniter/coupons/coupons',
            'redirectNew' => 'igniter/coupons/coupons/create',
        ],
        'edit' => [
            'title' => 'lang:admin::lang.form.edit_title',
            'redirect' => 'igniter/coupons/coupons/edit/{coupon_id}',
            'redirectClose' => 'igniter/coupons/coupons',
            'redirectNew' => 'igniter/coupons/coupons/create',
        ],
        'preview' => [
            'title' => 'lang:admin::lang.form.preview_title',
            'back' => 'igniter/coupons/coupons',
        ],
        'delete' => [
            'redirect' => 'igniter/coupons/coupons',
        ],
        'configFile' => 'coupon',
    ];

    protected null|string|array $requiredPermissions = 'Admin.Coupons';

    public function __construct()
    {
        parent::__construct();

        AdminMenu::setContext('coupons', 'marketing');
    }

    public function listOverrideColumnValue($record, $column, $alias = null)
    {
        if ($column->columnName == 'validity') {
            return ucwords($record->validity);
        }
    }
}
