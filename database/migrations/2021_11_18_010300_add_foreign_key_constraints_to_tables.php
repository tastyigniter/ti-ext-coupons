<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::table('igniter_coupons_history', function (Blueprint $table) {
            $table->foreignId('coupon_id')->nullable()->change();
            $table->foreign('coupon_id')
                ->references('coupon_id')
                ->on('igniter_coupons')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('order_id')->nullable()->change();
            $table->foreign('order_id')
                ->references('order_id')
                ->on('orders')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('customer_id')->nullable()->change();
            $table->foreign('customer_id')
                ->references('customer_id')
                ->on('customers')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });

        Schema::table('igniter_coupon_categories', function (Blueprint $table) {
            $table->foreignId('coupon_id')->nullable()->change();
            $table->foreign('coupon_id')
                ->references('coupon_id')
                ->on('igniter_coupons')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('category_id')->nullable()->change();
            $table->foreign('category_id')
                ->references('category_id')
                ->on('categories')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });

        Schema::table('igniter_coupon_menus', function (Blueprint $table) {
            $table->foreignId('coupon_id')->nullable()->change();
            $table->foreign('coupon_id')
                ->references('coupon_id')
                ->on('igniter_coupons')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('menu_id')->nullable()->change();
            $table->foreign('menu_id')
                ->references('menu_id')
                ->on('menus')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down()
    {
        try {
            Schema::table('igniter_coupons_history', function (Blueprint $table) {
                $table->dropForeignKeyIfExists('coupon_id');
                $table->dropForeignKeyIfExists('order_id');
                $table->dropForeignKeyIfExists('customer_id');
            });

            Schema::table('igniter_coupon_categories', function (Blueprint $table) {
                $table->dropForeignKeyIfExists('coupon_id');
                $table->dropForeignKeyIfExists('category_id');
            });

            Schema::table('igniter_coupon_menus', function (Blueprint $table) {
                $table->dropForeignKeyIfExists('coupon_id');
                $table->dropForeignKeyIfExists('menu_id');
            });
        }
        catch (\Exception $e) {
        }
    }
};
