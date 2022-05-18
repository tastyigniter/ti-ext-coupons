<?php

namespace Igniter\Coupons\ApiResources\Repositories;

use Igniter\Api\Classes\AbstractRepository;
use Igniter\Coupons\Models\Coupon;

class CouponsRepository extends AbstractRepository
{
    protected $modelClass = Coupon::class;
}
