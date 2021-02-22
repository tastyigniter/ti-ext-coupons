<?php

namespace Igniter\Coupons\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ExtendCouponCodeLength extends Migration
{
    public function up()
    {
        $table->string('code')->unique('code')->change();
    }
}
