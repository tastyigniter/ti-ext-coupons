<?php

namespace Igniter\Coupons\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DropForeignKeyConstraints extends Migration
{
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::table('igniter_coupons_history', function (Blueprint $table) {
            $table->dropForeign(['coupon_id']);
            $table->dropForeign(['order_id']);
            $table->dropForeign(['customer_id']);
            $table->dropIndex(sprintf('%s%s_%s_foreign', DB::getTablePrefix(), 'igniter_coupons_history', 'coupon_id'));
            $table->dropIndex(sprintf('%s%s_%s_foreign', DB::getTablePrefix(), 'igniter_coupons_history', 'order_id'));
            $table->dropIndex(sprintf('%s%s_%s_foreign', DB::getTablePrefix(), 'igniter_coupons_history', 'customer_id'));
        });

        Schema::table('igniter_coupon_categories', function (Blueprint $table) {
            $table->dropForeign(['coupon_id']);
            $table->dropForeign(['category_id']);
        });

        Schema::table('igniter_coupon_menus', function (Blueprint $table) {
            $table->dropForeign(['coupon_id']);
            $table->dropForeign(['menu_id']);
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down()
    {
    }
}
