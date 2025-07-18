<?php

declare(strict_types=1);

namespace Igniter\Coupons\Tests\Http\Controllers;

use Igniter\User\Models\User;

it('loads coupon histories page', function (): void {
    actingAsSuperUser()
        ->get(route('igniter.coupons.histories'))
        ->assertOk();
});

function actingAsSuperUser()
{
    return test()->actingAs(User::factory()->superUser()->create(), 'igniter-admin');
}
