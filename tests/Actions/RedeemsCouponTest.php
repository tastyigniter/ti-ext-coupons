<?php

namespace Igniter\Cart\Tests\Actions;

use Igniter\Cart\Models\Order;
use Igniter\Coupons\Actions\RedeemsCoupon;
use Igniter\Coupons\Models\Coupon as CouponModel;
use Igniter\Coupons\Models\CouponHistory;
use Illuminate\Support\Facades\Event;

it('redeems coupon correctly', function() {
    Event::fake();

    $order = Order::factory()->create();
    $order->totals()->create([
        'code' => 'coupon',
        'title' => 'Coupon (test-coupon)',
        'value' => 10,
        'priority' => 1,
    ]);

    $couponHistory = CouponHistory::create([
        'order_id' => $order->order_id,
        'coupon_id' => 1,
        'code' => 'test-coupon',
        'amount' => 10,
        'min_total' => 0,
    ]);

    (new RedeemsCoupon($order))->redeemCoupon();

    expect($couponHistory->fresh()->status)->toBeTrue();

    Event::assertDispatched('admin.order.couponRedeemed');
});

it('logs coupon history correctly', function() {
    Event::fake();

    $order = Order::factory()->create();
    $coupon = CouponModel::factory()->create();
    $redeemsCoupon = new RedeemsCoupon($order);

    expect($redeemsCoupon->logCouponHistory(10, $coupon))->toBeInstanceOf(CouponHistory::class);

    Event::assertDispatched('couponHistory.beforeAddHistory');
});

it('fails log coupon history when order does not exists', function() {
    Event::fake();

    $order = Order::factory()->make(['exists' => false]);
    $coupon = CouponModel::factory()->create();
    $redeemsCoupon = new RedeemsCoupon($order);

    expect($redeemsCoupon->logCouponHistory(10, $coupon))->toBeFalse();

    Event::assertNotDispatched('couponHistory.beforeAddHistory');
});
