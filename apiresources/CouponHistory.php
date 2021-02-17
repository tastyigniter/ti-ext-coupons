<?php

namespace Igniter\Coupons\ApiResources;

use Igniter\Api\Classes\ApiController;
use Igniter\Coupons\Requests\Coupon;

/**
 * CouponHistory API Controller
 */
class CouponHistory extends ApiController
{
    public $implement = ['Igniter.Api.Actions.RestController'];

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
        'request' => Coupon::class, // this doesnt matter as we only allow a view of this data
        'repository' => Repositories\CouponHistoryRepository::class,
        'transformer' => Transformers\CouponHistoryTransformer::class,
    ];

    protected $requiredAbilities = ['couponhistory:*'];

    public function restExtendQuery($query)
    {
        if (($token = $this->getToken()) && $token->isForCustomer())
            $query->where('customer_id', $token->tokenable_id);

        return $query;
    }

    public function store()
    {
        if (($token = $this->getToken()) && $token->isForCustomer())
            Request::merge(['customer_id' => $token->tokenable_id]);

        $this->asExtension('RestController')->store();
    }
}
