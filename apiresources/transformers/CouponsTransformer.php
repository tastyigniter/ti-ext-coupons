<?php

namespace Igniter\Coupons\ApiResources\Transformers;

use League\Fractal\TransformerAbstract;
use Igniter\Coupons\Models\Coupons_model;

class CouponsTransformer extends TransformerAbstract
{
    public function transform(Coupons_model $coupon)
    {
        return $coupon->toArray();
    }
}
