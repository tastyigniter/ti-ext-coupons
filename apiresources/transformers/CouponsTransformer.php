<?php

namespace Igniter\Coupons\ApiResources\Transformers;

use Igniter\Coupons\Models\Coupons_model;
use League\Fractal\TransformerAbstract;

class CouponsTransformer extends TransformerAbstract
{
    public function transform(Coupons_model $coupon)
    {
        return $coupon->toArray();
    }
}
