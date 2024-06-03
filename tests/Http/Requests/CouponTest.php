<?php

namespace Igniter\Coupons\Tests\Http\Requests;

use Igniter\Coupons\Http\Requests\CouponRequest;
use Illuminate\Validation\Rule;

it('has required rule for inputs', function() {
    $rules = (new CouponRequest)->rules();

    expect('required')->toBeIn(array_get($rules, 'name'))
        ->and('required')->toBeIn(array_get($rules, 'code'))
        ->and('required')->toBeIn(array_get($rules, 'type'))
        ->and('required')->toBeIn(array_get($rules, 'discount'))
        ->and('required')->toBeIn(array_get($rules, 'customer_redemptions'))
        ->and('required')->toBeIn(array_get($rules, 'redemptions'))
        ->and('required')->toBeIn(array_get($rules, 'validity'));
});

it('has nullable rule for inputs', function() {
    $rules = (new CouponRequest)->rules();

    expect('nullable')->toBeIn(array_get($rules, 'fixed_date'))
        ->and('nullable')->toBeIn(array_get($rules, 'fixed_from_time'))
        ->and('nullable')->toBeIn(array_get($rules, 'fixed_to_time'))
        ->and('nullable')->toBeIn(array_get($rules, 'period_start_date'))
        ->and('nullable')->toBeIn(array_get($rules, 'period_end_date'))
        ->and('nullable')->toBeIn(array_get($rules, 'recurring_every'))
        ->and('nullable')->toBeIn(array_get($rules, 'recurring_from_time'))
        ->and('nullable')->toBeIn(array_get($rules, 'recurring_to_time'))
        ->and('nullable')->toBeIn(array_get($rules, 'order_restriction.*'));
});

it('has string rule for inputs: type and order_restriction.*', function() {
    $rules = (new CouponRequest)->rules();
    $inputNames = ['type', 'order_restriction.*'];
    $testExpectation = null;

    foreach ($inputNames as $key => $inputName) {
        if ($key == 0) {
            $testExpectation = expect('string')->toBeIn(array_get($rules, $inputName));
        }
        $testExpectation = $testExpectation->and('string')->toBeIn(array_get($rules, $inputName));
    }
});

it('has numeric rule for inputs: discount and min_total', function() {
    $rules = (new CouponRequest)->rules();
    $inputNames = ['discount', 'min_total'];
    $testExpectation = null;

    foreach ($inputNames as $key => $inputName) {
        if ($key == 0) {
            $testExpectation = expect('numeric')->toBeIn(array_get($rules, $inputName));
        }
        $testExpectation = $testExpectation->and('numeric')->toBeIn(array_get($rules, $inputName));
    }
});

it('has integer rule for inputs: redemptions, customer_redemptions and locations.*', function() {
    $rules = (new CouponRequest)->rules();
    $inputNames = ['redemptions', 'customer_redemptions', 'locations.*'];
    $testExpectation = null;

    foreach ($inputNames as $key => $inputName) {
        if ($key == 0) {
            $testExpectation = expect('integer')->toBeIn(array_get($rules, $inputName));
        }
        $testExpectation = $testExpectation->and('integer')->toBeIn(array_get($rules, $inputName));
    }
});

it('has boolean rule for inputs: status and auto_apply', function() {
    $rules = (new CouponRequest)->rules();
    $inputNames = ['status', 'auto_apply'];
    $testExpectation = null;

    foreach ($inputNames as $key => $inputName) {
        if ($key == 0) {
            $testExpectation = expect('boolean')->toBeIn(array_get($rules, $inputName));
        }
        $testExpectation = $testExpectation->and('boolean')->toBeIn(array_get($rules, $inputName));
    }
});

it('has unique rule for code input', function() {
    expect((string)(Rule::unique('igniter_coupons')->ignore(null, 'coupon_id')))
        ->toBeIn(
            collect(array_get((new CouponRequest)->rules(), 'code'))->map(function($rule) {
                return (string)$rule;
            })->toArray()
        );
});

it('has minimum of 2 chars rule for code input', function() {
    $rules = (new CouponRequest)->rules();

    expect('min:2')->toBeIn(array_get($rules, 'code'));
});

it('has number of characters between 2 and 128 rule for name input', function() {
    $rules = (new CouponRequest)->rules();

    expect('between:2,128')->toBeIn(array_get($rules, 'name'));
});

it('has number of 1 size rule for type input', function() {
    $rules = (new CouponRequest)->rules();

    expect('size:1')->toBeIn(array_get($rules, 'type'));
});

it('has in:P,F rule for type input', function() {
    $rules = (new CouponRequest)->rules();

    expect('in:P,F')->toBeIn(array_get($rules, 'type'));
});

it('has in:forever,fixed,period,recurring rule for validity input', function() {
    $rules = (new CouponRequest)->rules();

    expect('in:forever,fixed,period,recurring')->toBeIn(array_get($rules, 'validity'));
});

it('has a maximum of 1028 characters for description rule', function() {
    $rules = (new CouponRequest)->rules();

    expect('max:1028')->toBeIn(array_get($rules, 'description'));
});
