<?php

namespace Igniter\Coupons\ApiResources\Repositories;

use Igniter\Api\Classes\AbstractRepository;
use Igniter\Coupons\Models\Coupons_history_model;

class CouponHistoryRepository extends AbstractRepository
{
    protected $modelClass = Coupons_history_model::class;
}
