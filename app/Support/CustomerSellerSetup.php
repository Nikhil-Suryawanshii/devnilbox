<?php

namespace App\Support;

use App\Enums\Roles;
use App\Models\Shop;
use App\Models\User;

class CustomerSellerSetup
{
    public static function ensure(User $user): Shop
    {
        if (! $user->hasRole(Roles::SHOP->value)) {
            $user->assignRole(Roles::SHOP->value);
            $user->unsetRelation('roles');
        }

        return Shop::firstOrCreate(
            ['user_id' => $user->id],
            ['name' => $user->name ?: 'My Shop', 'status' => true]
        );
    }
}
