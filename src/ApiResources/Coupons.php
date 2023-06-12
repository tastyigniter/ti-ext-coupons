<?php

namespace Igniter\Coupons\ApiResources;

use Igniter\Api\Classes\ApiController;
use Igniter\Coupons\Requests\CouponRequest;

/**
 * Coupons API Controller
 */
class Coupons extends ApiController
{
    public $implement = [\Igniter\Api\Http\Actions\RestController::class];

    public $restConfig = [
        'actions' => [
            'index' => [
                'pageLimit' => 20,
            ],
            'store' => [],
            'show' => [],
            'update' => [],
            'destroy' => [],
        ],
        'request' => CouponRequest::class,
        'repository' => Repositories\CouponsRepository::class,
        'transformer' => Transformers\CouponsTransformer::class,
    ];

    protected $requiredAbilities = ['coupons:*'];
}
