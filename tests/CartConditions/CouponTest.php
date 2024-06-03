<?php

namespace Igniter\Cart\Tests\CartConditions;

use Igniter\Cart\Models\Category;
use Igniter\Cart\Models\Menu;
use Igniter\Coupons\CartConditions\Coupon;
use Igniter\Coupons\Models\Coupon as CouponModel;
use Igniter\Local\Facades\Location;

beforeEach(function() {
    $this->coupon = CouponModel::factory()->create([
        'code' => 'test-coupon',
        'name' => 'Test Coupon',
        'type' => 'F',
        'discount' => 10,
    ]);
    $this->couponCondition = new Coupon([
        'name' => 'Coupon',
        'label' => 'Coupon: %s',
        'metaData' => ['code' => $this->coupon->code],
    ]);
});

afterEach(function() {
    unset($this->couponCondition);
});

it('gets label correctly', function() {
    expect($this->couponCondition->getLabel())->toBe('Coupon: test-coupon');
});

it('gets value correctly', function() {
    $this->couponCondition->calculate(20);

    expect($this->couponCondition->getValue())->toBe(-10.0);
});

it('gets model correctly', function() {
    expect($this->couponCondition->getModel()->getKey())->toBe($this->coupon->getKey());
});

it('gets applicable items correctly', function() {
    $category = Category::factory()->create();
    $category->menus()->save($categoryMenu = Menu::factory()->create());
    $this->coupon->menus()->save($menu = Menu::factory()->create());
    $this->coupon->categories()->save($category);

    expect($this->couponCondition->getApplicableItems($this->coupon))->toContain($menu->getKey(), $categoryMenu->getKey());
});

it('throws exception when invalid coupon is loaded', function() {
    $this->couponCondition->setMetaData(['code' => 'invalid']);

    $this->couponCondition->onLoad();

    expect($this->couponCondition->getMetaData('code'))->toBeNull();
});

it('does not apply when applies on menu items only', function() {
    $this->coupon->apply_coupon_on = 'menu_items';
    $this->coupon->save();

    expect($this->couponCondition->beforeApply())->toBeFalse();
});

it('does not apply when applies on delivery charge only', function() {
    $this->coupon->apply_coupon_on = 'delivery_fee';
    $this->coupon->save();

    Location::shouldReceive('getId')->once()->andReturnNull();
    Location::shouldReceive('orderTypeIsDelivery')->once()->andReturn(false);

    expect($this->couponCondition->beforeApply())->toBeFalse();
});

it('gets actions with percentage fee', function() {
    $this->coupon->type = 'P';
    $this->coupon->save();

    expect($this->couponCondition->getActions())->toBe([['value' => '-%10']]);
});

it('gets actions with fixed fee', function() {
    expect($this->couponCondition->getActions())->toBe([['value' => '-10']]);
});

it('is applicable to cart item', function() {
    $this->coupon->apply_coupon_on = 'menu_items';
    $this->coupon->save();

    $this->coupon->menus()->save($menu = Menu::factory()->create());

    $this->couponCondition->getModel();
    $this->couponCondition->getApplicableItems($this->coupon);

    expect($this->couponCondition->isApplicableTo((object)['id' => $menu->getKey()]))->toBeTrue();
});
