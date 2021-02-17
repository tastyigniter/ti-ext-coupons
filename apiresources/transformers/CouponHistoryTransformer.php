<?php

namespace Igniter\Coupons\ApiResources\Transformers;

use League\Fractal\TransformerAbstract;
use Igniter\Coupons\Models\Coupons_history_model;

class CouponHistoryTransformer extends TransformerAbstract
{
    public function transform(Coupons_history_model $history)
    {
        return $history->toArray();
    }
}
