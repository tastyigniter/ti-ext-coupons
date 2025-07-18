<?php

declare(strict_types=1);

namespace Igniter\Coupons\Http\Controllers;

use Igniter\Admin\Classes\AdminController;
use Igniter\Admin\Http\Actions\ListController;
use Igniter\Coupons\Models\CouponHistory;

class Histories extends AdminController
{
    public array $implement = [
        ListController::class,
    ];

    public array $listConfig = [
        'list' => [
            'model' => CouponHistory::class,
            'title' => 'igniter.coupons::default.text_title_histories',
            'emptyMessage' => 'igniter.coupons::default.text_histories_empty',
            'defaultSort' => ['coupon_history_id', 'DESC'],
            'configFile' => 'coupon_history',
            'back' => 'igniter/coupons/coupons',
        ],
    ];

    protected null|string|array $requiredPermissions = 'Admin.Coupons';

    public function __construct()
    {
        parent::__construct();
    }
}
