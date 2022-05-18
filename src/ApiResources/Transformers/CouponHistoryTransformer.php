<?php

namespace Igniter\Coupons\ApiResources\Transformers;

use Igniter\Coupons\Models\CouponHistory;
use League\Fractal\TransformerAbstract;

class CouponHistoryTransformer extends TransformerAbstract
{
    public function transform(CouponHistory $history)
    {
        return $history->toArray();
    }
}
