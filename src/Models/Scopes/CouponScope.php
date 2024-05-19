<?php

namespace Igniter\Coupons\Models\Scopes;

use Igniter\Flame\Database\Builder;
use Igniter\Flame\Database\Scope;

class CouponScope extends Scope
{
    public function addIsAutoApplicable()
    {
        return function(Builder $builder) {
            return $builder->where('auto_apply', '1');
        };
    }

    public function addWhereHasCategory()
    {
        return function(Builder $builder, $categoryId) {
            return $builder->whereHas('categories', function($q) use ($categoryId) {
                $q->where('categories.category_id', $categoryId);
            });
        };
    }

    public function addWhereHasMenu()
    {
        return function(Builder $builder, $menuId) {
            return $builder->whereHas('menus', function($q) use ($menuId) {
                $q->where('menus.menu_id', $menuId);
            });
        };
    }

    public function addWhereCodeAndLocation()
    {
        return function(Builder $builder, $code, $locationId) {
            return $builder->whereHasOrDoesntHaveLocation($locationId)->whereCode($code);
        };
    }
}
