<?php

namespace Igniter\Coupons\Tests;

use Igniter\Cart\Models\Order;
use Igniter\Coupons\Extension;
use Igniter\Coupons\Models\Actions\RedeemsCoupon;
use Igniter\Coupons\Models\Coupon as CouponModel;
use Igniter\Coupons\Models\CouponHistory;
use Igniter\User\Models\Customer;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;

it('adds RedeemsCoupon trait to order model', function() {
    $order = new Order;

    expect($order->implement)->toContain(RedeemsCoupon::class);
});

it('logs coupon history after order save', function() {
    $order = Order::factory()->create();

    $coupon = CouponModel::factory()->create([
        'code' => 'test-coupon',
        'name' => 'coupon',
    ]);

    $order->totals()->create([
        'code' => 'coupon',
        'title' => 'Coupon [test-coupon]',
        'value' => 10,
    ]);

    event('igniter.checkout.afterSaveOrder', [$order]);

    expect($order->coupon_history()->count())->toBe(1);
});

it('redeems coupon after payment processed', function() {
    Mail::fake();
    Queue::fake();

    $order = Order::factory()->create();
    $order->totals()->create([
        'code' => 'coupon',
        'title' => 'Coupon (test-coupon)',
        'value' => 10,
    ]);
    $history = CouponHistory::create([
        'order_id' => $order->getKey(),
        'status' => 0,
    ]);

    event('admin.order.paymentProcessed', [$order]);

    expect($history->fresh()->status)->toBeTrue();
});

it('updates coupon history after customer created', function() {
    $order = Order::factory()->create();
    $history = CouponHistory::create([
        'order_id' => $order->getKey(),
        'customer_id' => 0,
    ]);

    $customer = Customer::factory()->create([
        'email' => $order->email,
    ]);

    expect($history->fresh()->customer_id)->toBe($customer->getKey());
});

it('registers api resources correctly', function() {
    $extension = new Extension(app());

    $resources = $extension->registerApiResources();

    expect($resources)->toBeArray()
        ->and($resources)->toHaveKey('coupons')
        ->and($resources['coupons'])->toBeArray()
        ->and($resources['coupons'])->toHaveKeys(['controller', 'name', 'description', 'actions']);
});

it('registers cart conditions correctly', function() {
    $extension = new Extension(app());

    $conditions = $extension->registerCartConditions();

    expect($conditions)->toBeArray()
        ->and($conditions)->toHaveKey(\Igniter\Coupons\CartConditions\Coupon::class)
        ->and($conditions[\Igniter\Coupons\CartConditions\Coupon::class])->toBeArray()
        ->and($conditions[\Igniter\Coupons\CartConditions\Coupon::class])->toHaveKeys(['name', 'label', 'description']);
});

it('registers permissions correctly', function() {
    $extension = new Extension(app());

    $permissions = $extension->registerPermissions();

    expect($permissions)->toBeArray()
        ->and($permissions)->toHaveKey('Admin.Coupons')
        ->and($permissions['Admin.Coupons'])->toBeArray()
        ->and($permissions['Admin.Coupons'])->toHaveKeys(['label', 'group']);
});

it('registers navigation correctly', function() {
    $extension = new Extension(app());

    $navigation = $extension->registerNavigation();

    expect($navigation)->toBeArray()
        ->and($navigation)->toHaveKey('marketing')
        ->and($navigation['marketing'])->toBeArray()
        ->and($navigation['marketing'])->toHaveKey('child')
        ->and($navigation['marketing']['child'])->toHaveKey('coupons')
        ->and($navigation['marketing']['child']['coupons'])->toBeArray()
        ->and($navigation['marketing']['child']['coupons'])->toHaveKeys(['priority', 'class', 'href', 'title', 'permission']);
});
