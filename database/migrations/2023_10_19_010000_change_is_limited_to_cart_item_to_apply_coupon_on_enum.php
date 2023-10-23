<?php

namespace Igniter\Coupons\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeIsLimitedToCartItemToApplyCouponOnEnum extends Migration
{
    public function up()
    {
        Schema::table('igniter_coupons', function (Blueprint $table) {
            $table->dropColumn('is_limited_to_cart_item');
            $table->enum('apply_coupon_on',
                ['whole_cart','menu_items', 'delivery_fee'])->default('whole_cart')->after('order_restriction');
        });
    }

    public function down()
    {
        Schema::table('igniter_coupons', function (Blueprint $table) {
            $table->dropColumn('apply_coupon_on');
            $table->boolean('is_limited_to_cart_item')->default(false)->after('order_restriction');
        });
    }
}
