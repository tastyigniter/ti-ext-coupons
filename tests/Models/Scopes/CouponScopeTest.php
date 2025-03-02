<?php

namespace Igniter\Coupons\Tests\Models\Scopes;

use Igniter\Coupons\Models\Scopes\CouponScope;
use Igniter\Flame\Database\Builder;
use Mockery;

beforeEach(function() {
    $this->couponScope = new CouponScope();
    $this->builder = Mockery::mock(Builder::class);
});

it('adds is auto applicable scope', function() {
    $this->builder->shouldReceive('where')
        ->with('auto_apply', '1')
        ->andReturnSelf();

    $result = ($this->couponScope->addIsAutoApplicable())($this->builder);

    expect($result)->toBe($this->builder);
});

it('adds where has category scope', function() {
    $categoryId = 1;
    $this->builder->shouldReceive('whereHas')
        ->with('categories', Mockery::on(function($callback) use ($categoryId) {
            $query = Mockery::mock(Builder::class);
            $query->shouldReceive('where')
                ->with('categories.category_id', $categoryId)
                ->andReturnSelf();
            $callback($query);
            return true;
        }))
        ->andReturnSelf();

    $result = ($this->couponScope->addWhereHasCategory())($this->builder, $categoryId);

    expect($result)->toBe($this->builder);
});

it('adds where has menu scope', function() {
    $menuId = 1;
    $this->builder->shouldReceive('whereHas')
        ->with('menus', Mockery::on(function($callback) use ($menuId) {
            $query = Mockery::mock(Builder::class);
            $query->shouldReceive('where')
                ->with('menus.menu_id', $menuId)
                ->andReturnSelf();
            $callback($query);
            return true;
        }))
        ->andReturnSelf();

    $result = ($this->couponScope->addWhereHasMenu())($this->builder, $menuId);

    expect($result)->toBe($this->builder);
});

it('adds where code and location scope', function() {
    $code = 'TESTCODE';
    $locationId = 1;
    $this->builder->shouldReceive('whereHasOrDoesntHaveLocation')
        ->with($locationId)
        ->andReturnSelf();
    $this->builder->shouldReceive('whereCode')
        ->with($code)
        ->andReturnSelf();

    $result = ($this->couponScope->addWhereCodeAndLocation())($this->builder, $code, $locationId);

    expect($result)->toBe($this->builder);
});
