<?php

namespace Igniter\Coupons\ApiResources;

use Igniter\Api\Classes\ApiController;
use Igniter\Coupons\Http\Requests\CouponRequest;

/**
 * Coupons API Controller
 */
class Coupons extends ApiController
{
    public array $implement = [\Igniter\Api\Http\Actions\RestController::class];

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

    protected string|array $requiredAbilities = ['coupons:*'];
}
