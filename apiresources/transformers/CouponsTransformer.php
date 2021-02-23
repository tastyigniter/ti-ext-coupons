<?php

namespace Igniter\Coupons\ApiResources\Transformers;

use League\Fractal\TransformerAbstract;
use Igniter\Api\ApiResources\Transformers\CategoryTransformer;
use Igniter\Api\ApiResources\Transformers\MenuTransformer;
use Igniter\Coupons\Models\Coupons_model;

class CouponsTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'menus',
        'categories',
    ];
    
    public function transform(Coupons_model $coupon)
    {
        return $coupon->toArray();
    }
    
    public function includeCategories(Coupons_model $coupon)
    {
        return $this->collection(
            $coupon->categories,
            new CategoryTransformer,
            'categories'
        );
    }

    public function includeMenus(Coupons_model $coupon)
    {
        return $this->collection(
            $coupon->menus,
            new MenuTransformer,
            'menus'
        );
    }
}
